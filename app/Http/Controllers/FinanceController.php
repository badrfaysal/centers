<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Child;
use App\Models\Specialist;
use Illuminate\Http\Request;

class FinanceController extends Controller
{
    /**
     * عرض كل الفواتير مع إحصائيات وفلاتر.
     */
    public function index(Request $request)
    {
        $query = Invoice::with(['child', 'specialist'])->latest('invoice_date');

        // بحث
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                  ->orWhere('child_name', 'like', "%{$search}%")
                  ->orWhere('parent_name', 'like', "%{$search}%")
                  ->orWhere('specialist_name', 'like', "%{$search}%");
            });
        }

        // فلتر حالة الدفع
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('payment_status', $request->status);
        }

        $invoices = $query->paginate(15);

        // إحصائيات
        $totalRevenue     = Invoice::sum('net_amount');
        $totalPaid        = Invoice::sum('paid_amount');
        $totalDebts       = Invoice::where('remaining_amount', '>', 0)->sum('remaining_amount');
        $invoicesCount    = Invoice::count();

        $settings = \App\Http\Controllers\SettingController::getSettings();

        return view('finances.index', compact(
            'invoices', 'totalRevenue', 'totalPaid', 'totalDebts', 'invoicesCount', 'settings'
        ));
    }

    /**
     * نموذج إنشاء فاتورة جديدة.
     */
    public function create()
    {
        $children    = Child::orderBy('name')->get();
        $specialists = Specialist::where('status', 'active')->orderBy('name')->get();
        $nextNumber  = Invoice::generateNextInvoiceNumber();
        $settings    = \App\Http\Controllers\SettingController::getSettings();

        return view('finances.create', compact('children', 'specialists', 'nextNumber', 'settings'));
    }

    /**
     * حفظ فاتورة جديدة.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'child_id'            => 'required|exists:children,id',
            'specialist_id'       => 'required|exists:specialists,id',
            'session_price'       => 'required|numeric|min:0',
            'sessions_count'      => 'required|integer|min:1',
            'discount_type'       => 'nullable|in:percentage,fixed',
            'discount_value'      => 'nullable|numeric|min:0',
            'paid_amount'         => 'required|numeric|min:0',
            'payment_method'      => 'required|in:cash,transfer,visa',
            'invoice_date'        => 'required|date',
            'notes'               => 'nullable|string|max:1000',
        ]);

        $child      = Child::findOrFail($validated['child_id']);
        $specialist = Specialist::findOrFail($validated['specialist_id']);

        $sessionPrice   = $validated['session_price'];
        $sessionsCount  = $validated['sessions_count'];
        $totalAmount    = $sessionPrice * $sessionsCount;

        // حساب الخصم
        $discountAmount     = 0;
        $discountPercentage = 0;

        if ($request->filled('discount_value') && $request->discount_value > 0) {
            if ($request->discount_type === 'percentage') {
                $discountPercentage = min($request->discount_value, 100);
                $discountAmount = ($totalAmount * $discountPercentage) / 100;
            } else {
                $discountAmount = min($request->discount_value, $totalAmount);
                $discountPercentage = $totalAmount > 0 ? ($discountAmount / $totalAmount) * 100 : 0;
            }
        }

        $netAmount       = $totalAmount - $discountAmount;
        $paidAmount      = min($validated['paid_amount'], $netAmount);
        $remainingAmount = $netAmount - $paidAmount;

        // تحديد حالة الدفع
        if ($paidAmount >= $netAmount) {
            $paymentStatus = 'paid';
        } elseif ($paidAmount > 0) {
            $paymentStatus = 'partial';
        } else {
            $paymentStatus = 'unpaid';
        }

        $invoice = Invoice::create([
            'invoice_number'      => Invoice::generateNextInvoiceNumber(),
            'child_id'            => $child->id,
            'specialist_id'       => $specialist->id,
            'child_name'          => $child->name,
            'specialist_name'     => $specialist->name,
            'parent_name'         => $child->parent_name,
            'session_price'       => $sessionPrice,
            'sessions_count'      => $sessionsCount,
            'total_amount'        => $totalAmount,
            'discount_amount'     => $discountAmount,
            'discount_percentage' => $discountPercentage,
            'net_amount'          => $netAmount,
            'paid_amount'         => $paidAmount,
            'remaining_amount'    => $remainingAmount,
            'payment_method'      => $validated['payment_method'],
            'payment_status'      => $paymentStatus,
            'notes'               => $validated['notes'] ?? null,
            'invoice_date'        => $validated['invoice_date'],
        ]);

        return redirect()->route('finances.index')
            ->with('success', "تم إنشاء الفاتورة ({$invoice->invoice_number}) بنجاح!");
    }

    /**
     * عرض الفاتورة للطباعة.
     */
    public function show(Invoice $invoice)
    {
        $settings = \App\Http\Controllers\SettingController::getSettings();
        return view('finances.invoice', compact('invoice', 'settings'));
    }

    public function print(Invoice $invoice)
    {
        $settings = \App\Http\Controllers\SettingController::getSettings();
        return view('finances.print', compact('invoice', 'settings'));
    }

    /**
     * API: إرجاع سعر جلسة أخصائي محدد.
     */
    public function getSpecialistPrice(Specialist $specialist)
    {
        return response()->json([
            'session_rate' => $specialist->session_rate,
            'name'         => $specialist->name,
        ]);
    }
}
