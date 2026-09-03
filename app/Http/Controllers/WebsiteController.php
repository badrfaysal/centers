<?php

namespace App\Http\Controllers;

use App\Models\Specialist;
use App\Models\SpecialistRating;
use App\Models\ConsultationBooking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class WebsiteController extends Controller
{
    /**
     * Display the Public Center Website & Landing Page.
     */
    public function index()
    {
        $specialists = Specialist::where('status', 'active')->orderBy('experience_years', 'desc')->get();
        
        $testimonials = SpecialistRating::where('rating', '>=', 4)
            ->latest()
            ->take(6)
            ->get();

        if ($testimonials->isEmpty()) {
            $testimonials = collect([
                (object)[
                    'parent_name' => 'أ. محمود السعدني',
                    'rating' => 5,
                    'feedback' => 'تجربة ممتازة جداً مع المركز، ابني كان يعاني من تأخر نطق وتلعثم وخلال 3 أشهر فقط لاحظنا فرقاً مذهلاً في ثقته ونطقه للكلمات، شكراً لفريق التخاطب الرائع!',
                    'child_condition' => 'تأخر لغوي وتلعثم'
                ],
                (object)[
                    'parent_name' => 'د. فاطمة الزهراء',
                    'rating' => 5,
                    'feedback' => 'برامج التكامل الحسي مجهزة على أعلى مستوى، ومتابعة الفيديوهات والتمارين المنزلية بعد كل جلسة على تطبيق الأهل كانت سبباً أساسياً في تسريع التطور.',
                    'child_condition' => 'فرط حركة وتكامل حسي'
                ],
                (object)[
                    'parent_name' => 'م. عمر عبد العزيز',
                    'rating' => 5,
                    'feedback' => 'أفضل مركز تأهيل تعاملنا معه، أمانة واحترافية عالية وخطة علاجية فردية دقيقة ومقاييس ذكاء معتمدة من استشاريين متميزين.',
                    'child_condition' => 'صعوبات تعلم وتنمية مهارات'
                ]
            ]);
        }

        $programs = [
            [
                'icon' => 'fa-comments',
                'color' => 'teal',
                'title' => 'برنامج التخاطب وعلاج اضطرابات النطق والكلام',
                'desc' => 'علاج التلعثم، اللدغات، التأخر اللغوي، الخنف، والتأهيل السمعي واللفظي بعد زراعة القوقعة بأحدث البرامج المعتمدة.',
                'features' => ['جلسات فردية 1-on-1', 'تمارين بصرية بالمرآة', 'تأهيل بعد زراعة القوقعة']
            ],
            [
                'icon' => 'fa-shapes',
                'color' => 'purple',
                'title' => 'برنامج التكامل الحسي (Sensory Integration)',
                'desc' => 'قاعة حسية متكاملة (Sensory Room) مجهزة بأحدث أدوات التفريغ والتنظيم الحسي لعلاج الحساسية المفرطة أو نقص الاستجابة.',
                'features' => ['أرجوحات حسية متطورة', 'تفريغ طاقة منظم', 'تهدئة وتوازن حركي']
            ],
            [
                'icon' => 'fa-puzzle-piece',
                'color' => 'blue',
                'title' => 'برنامج تأهيل طيف التوحد (ASD) والتواصل',
                'desc' => 'برامج متخصصة لزيادة التواصل البصري، التفاعل الاجتماعي، والاعتماد على النفس باستخدام مبادئ تحليل السلوك التطبيقي.',
                'features' => ['خطة فردية (IEP)', 'تنمية مهارات الاستقلالية', 'تواصل اجتماعي وتبادل أدوار']
            ],
            [
                'icon' => 'fa-bolt',
                'color' => 'amber',
                'title' => 'برنامج فرط الحركة وتشتت الانتباه (ADHD)',
                'desc' => 'تدريبات لزيادة مدى الانتباه والتركيز، التحكم في الاندفاعية، وتنظيم النشاط الحركي لدمج الطفل بنجاح في المدرسة والمجتمع.',
                'features' => ['أنشطة تركيز دقيقة', 'تعديل سلوك إيجابي', 'إشراف طبي متكامل']
            ],
            [
                'icon' => 'fa-book-open-reader',
                'color' => 'indigo',
                'title' => 'برنامج صعوبات التعلم وعسر القراءة (Dyslexia)',
                'desc' => 'تأهيل أكاديمي ومعرفي لعلاج صعوبات القراءة، الكتابة، الحساب، وتنمية الذاكرة البصرية والسمعية بطرق تفاعلية مبسطة.',
                'features' => ['تقييم أكاديمي شامل', 'استراتيجيات تعلم تفاعلية', 'تنمية الذاكرة والتركيز']
            ],
            [
                'icon' => 'fa-brain',
                'color' => 'rose',
                'title' => 'وحدة اختبارات ومقاييس الذكاء المعتمدة',
                'desc' => 'تطبيق اختبارات ستانفورد بينيه (الصورة الخامسة)، مقياس كارز للتوحد، مقياس فاينلاند للسلوك التكيفي واختبارات اللغة الرسمية.',
                'features' => ['تقارير طبية رسمية معتمدة', 'تحديد العمر العقلي واللغوي', 'توجيه للخطة المناسبة']
            ],
        ];

        return view('website.index', compact('specialists', 'testimonials', 'programs'));
    }

    /**
     * Handle public booking request with parent account credentials.
     */
    public function bookConsultation(Request $request)
    {
        $validated = $request->validate([
            'parent_name'     => 'required|string|max:100',
            'child_name'      => 'required|string|max:100',
            'child_age'       => 'required|string|max:50',
            'phone'           => 'required|string|max:25',
            'parent_password' => 'nullable|string|min:4|max:50',
            'service'         => 'required|string|max:100',
            'notes'           => 'nullable|string|max:1000',
        ]);

        $booking = ConsultationBooking::create([
            'booking_code'    => ConsultationBooking::generateNextCode(),
            'parent_name'     => $validated['parent_name'],
            'child_name'      => $validated['child_name'],
            'child_age'       => $validated['child_age'],
            'phone'           => $validated['phone'],
            'parent_password' => !empty($validated['parent_password']) ? Hash::make($validated['parent_password']) : null,
            'service'         => $validated['service'],
            'notes'           => $validated['notes'] ?? null,
            'session_price'   => 250.00,
            'status'          => 'pending',
        ]);

        session([
            'parent_phone' => $booking->phone,
            'parent_name'  => $booking->parent_name,
            'last_booking' => $booking->booking_code
        ]);

        return redirect()->route('parent.bookings.track', ['phone' => $booking->phone])
            ->with('booking_success', "تم تسجيل طلب حجزك بنجاح بكود ({$booking->booking_code})! تم فتح حسابك لمتابعة تفاصيل الموعد والسعر عند اعتماده من إدارة المركز.");
    }
}