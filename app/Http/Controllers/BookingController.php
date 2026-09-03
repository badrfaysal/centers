<?php

namespace App\Http\Controllers;

use App\Models\ConsultationBooking;
use App\Models\Specialist;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    /**
     * Display the Bookings & Consultations Hub.
     */
    public function index(Request $request)
    {
        $query = ConsultationBooking::latest();

        // بحث بالاسم أو الهاتف أو الكود
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('child_name', 'like', "%{$search}%")
                  ->orWhere('parent_name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('booking_code', 'like', "%{$search}%");
            });
        }

        // فلترة بالحالة
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        $bookings = $query->paginate(12);

        // إحصائيات
        $pendingCount = ConsultationBooking::where('status', 'pending')->count();
        $confirmedCount = ConsultationBooking::where('status', 'confirmed')->count();
        $completedCount = ConsultationBooking::where('status', 'completed')->count();
        $totalCount = ConsultationBooking::count();

        // قائمة الأخصائيين والغرف للمودال
        $specialists = Specialist::where('status', 'active')->orderBy('name')->get();
        $rooms = [
            'غرفة التخاطب 1',
            'غرفة التخاطب 2',
            'غرفة التكامل الحسي Sensory Room',
            'غرفة تنمية المهارات وتعديل السلوك',
            'غرفة العلاج الوظيفي OT Room',
            'غرفة مقاييس واختبارات الذكاء',
        ];

        return view('bookings.index', compact(
            'bookings',
            'pendingCount',
            'confirmedCount',
            'completedCount',
            'totalCount',
            'specialists',
            'rooms'
        ));
    }

    /**
     * Approve and schedule a booking appointment with price and room details.
     */
    public function approve(Request $request, ConsultationBooking $booking)
    {
        $validated = $request->validate([
            'scheduled_at'    => 'required|date',
            'specialist_name' => 'required|string|max:100',
            'room'            => 'required|string|max:100',
            'session_price'   => 'required|numeric|min:0',
            'payment_status'  => 'required|in:unpaid,paid,deposit',
            'admin_notes'     => 'nullable|string|max:1000',
        ]);

        $booking->update([
            'scheduled_at'    => $validated['scheduled_at'],
            'specialist_name' => $validated['specialist_name'],
            'room'            => $validated['room'],
            'session_price'   => $validated['session_price'],
            'payment_status'  => $validated['payment_status'],
            'admin_notes'     => $validated['admin_notes'] ?? null,
            'status'          => 'confirmed',
            'confirmed_by'    => 'إدارة المركز العامة',
            'confirmed_at'    => now(),
        ]);

        $child = $booking->child;
        if (!$child) {
            $child = \App\Models\Child::create([
                'name' => $booking->child_name,
                'parent_name' => $booking->parent_name,
                'phone' => $booking->phone,
                'code' => \App\Models\Child::generateNextCode(),
                'status' => 'active',
                'main_specialist' => $validated['specialist_name'],
                'birth_date' => now()->format('Y-m-d'),
                'initial_diagnosis' => 'قيد التقييم (تلقائي من الحجز)',
            ]);
            $booking->update(['child_id' => $child->id]);
        }

        $specialist = \App\Models\Specialist::where('name', $validated['specialist_name'])->first();
        $scheduledAt = \Carbon\Carbon::parse($validated['scheduled_at']);

        \App\Models\SessionSchedule::create([
            'child_id' => $child->id,
            'specialist_id' => $specialist ? $specialist->id : null,
            'specialist_name' => $validated['specialist_name'],
            'session_title' => $booking->service ?? 'جلسة تقييم أولي',
            'session_date' => $scheduledAt->format('Y-m-d'),
            'start_time' => $scheduledAt->format('H:i:s'),
            'end_time' => $scheduledAt->copy()->addMinutes(45)->format('H:i:s'),
            'room_name' => $validated['room'],
            'status' => 'scheduled',
            'notes' => 'جلسة تقييم ناتجة عن طلب حجز إلكتروني.',
        ]);

        return redirect()->route('bookings.index')
            ->with('success', "تمت الموافقة على طلب الحجز للبطل ({$booking->child_name}) وتحديد الموعد بسعر ({$booking->session_price} ج.م)! سيظهر فوراً لولي الأمر في حسابه وبوابته.");
    }

    /**
     * Update booking status (completed / cancelled / pending).
     */
    public function updateStatus(Request $request, ConsultationBooking $booking)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,confirmed,completed,cancelled',
        ]);

        $booking->update(['status' => $validated['status']]);

        return redirect()->back()->with('success', 'تم تحديث حالة طلب الحجز بنجاح.');
    }

    /**
     * Remove the specified booking.
     */
    public function destroy(ConsultationBooking $booking)
    {
        $booking->delete();
        return redirect()->route('bookings.index')->with('success', 'تم حذف طلب الحجز من السجل بنجاح.');
    }

    /**
     * API: Check for new pending bookings (for real-time notification polling).
     */
    public function checkNewBookings()
    {
        $newBookings = ConsultationBooking::where('status', 'pending')
            ->where('created_at', '>=', now()->subHours(24))
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get()
            ->map(function ($b) {
                return [
                    'id'         => $b->id,
                    'code'       => $b->booking_code,
                    'child_name' => $b->child_name,
                    'parent_name'=> $b->parent_name,
                    'phone'      => $b->phone,
                    'service'    => $b->service,
                    'created_at' => $b->created_at->diffForHumans(),
                ];
            });

        return response()->json([
            'count'    => $newBookings->count(),
            'bookings' => $newBookings,
        ]);
    }
}