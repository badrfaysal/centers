<?php

namespace App\Http\Controllers;

use App\Models\Child;
use App\Models\TherapySession;
use App\Models\Specialist;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ChildController extends Controller
{
    /**
     * Display a listing of registered children.
     */
    public function index(Request $request)
    {
        $query = Child::query();

        // Use the Filterable trait
        $query->filterAndSort(
            $request,
            ['name', 'code', 'phone', 'parent_name'], // Searchable fields
            ['diagnosis_category', 'status']          // Filterable fields
        );

        $children = $query->paginate(10)->withQueryString();
        $totalChildren = Child::count();
        $activeChildren = Child::where('status', 'active')->count();

        return view('children.index', compact('children', 'totalChildren', 'activeChildren'));
    }

    /**
     * Show the form for creating a new child profile.
     */
    public function create()
    {
        $nextCode = Child::generateNextCode();
        
        $specialists = $this->getSpecialistsList();
        $diagnoses = $this->getDiagnosesCategories();
        $packages = $this->getPackagesList();

        return view('children.create', compact('nextCode', 'specialists', 'diagnoses', 'packages'));
    }

    /**
     * Store a newly created child in database.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'national_id'         => 'required|string|size:14|unique:children,national_id|unique:users,username',
            'code'                => 'required|string|unique:children,code',
            'name'                => 'required|string|max:100',
            'birth_date'          => 'required|date',
            'mental_age'          => 'nullable|string|max:100',
            'gender'              => 'required|in:male,female',
            'photo'               => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'parent_name'         => 'required|string|max:100',
            'parent_relation'     => 'required|string|max:50',
            'phone'               => 'required|string|max:25',
            'emergency_phone'     => 'nullable|string|max:25',
            'address'             => 'nullable|string|max:255',
            'diagnoses'           => 'nullable|array',
            'diagnoses.*'         => 'nullable|string|max:255',
            'initial_diagnosis'   => 'nullable|string|max:3000',
            'diagnosis_category'  => 'required|string',
            'iq_tests_history'    => 'nullable|string',
            'main_specialist'     => 'nullable|string|max:100',
            'neurologist_name'    => 'nullable|string|max:150',
            'current_medications' => 'nullable|string',
            'medical_notes'       => 'nullable|string',
            'assistive_devices'   => 'nullable|string|max:150',
            'package_type'        => 'nullable|string|max:50',
            'status'              => 'required|in:active,on_hold,discharged',
        ]);

        $diagnosesList = [];
        if ($request->has('diagnoses') && is_array($request->diagnoses)) {
            $diagnosesList = array_values(array_filter(array_map('trim', $request->diagnoses)));
        }

        if (!empty($diagnosesList)) {
            $validated['initial_diagnosis'] = json_encode($diagnosesList, JSON_UNESCAPED_UNICODE);
        }

        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('children_photos', 'public');
            $validated['photo_path'] = $path;
        }

        $user = \App\Models\User::create([
            'name' => $validated['parent_name'],
            'username' => $validated['national_id'],
            'password' => \Illuminate\Support\Facades\Hash::make($validated['national_id']),
            'role' => 'parent',
        ]);

        $validated['user_id'] = $user->id;

        $child = Child::create($validated);

        return redirect()->route('children.index')->with('success', "تم تسجيل ملف الطفل ({$child->name}) وتوليد كوده ({$child->code}) بنجاح! ");
    }

    /**
     * Display the 360° profile of the specified child.
     */
    public function show(Child $child)
    {
        $dbSessions = $child->therapySessions()->latest('session_date')->get();

        $recentSessions = [];
        if ($dbSessions->isNotEmpty()) {
            foreach ($dbSessions as $s) {
                $recentSessions[] = [
                    'date' => $s->session_date ? $s->session_date->format('Y-m-d') : 'اليوم',
                    'time' => $s->session_time ?? '04:00 م',
                    'room' => $s->room_name,
                    'specialist' => $s->specialist_name,
                    'mood' => $s->child_mood,
                    'mood_color' => 'emerald',
                    'notes' => $s->clinical_notes,
                    'home_exercise' => $s->home_exercise,
                    'has_video' => !empty($s->video_path),
                    'video_path' => $s->video_path ? asset('storage/' . $s->video_path) : null,
                    'video_duration' => $s->video_duration ?? '0:30 دقيقة',
                    'goals' => $s->goals_evaluated ?? [],
                ];
            }
        } else {
            $recentSessions = [
                [
                    'date' => '20 أغسطس 2026',
                    'time' => '04:00 م - 04:45 م',
                    'room' => 'غرفة التخاطب 1',
                    'specialist' => $child->main_specialist ?? 'د. أحمد يسري',
                    'mood' => 'ممتاز ومتعاون ومتحمس',
                    'mood_color' => 'emerald',
                    'notes' => 'استجاب الطفل بشكل رائع للتدريب على صوت الكاف بالمرآة مع استخدام التعزيز الإيجابي.',
                    'home_exercise' => 'تكرار لعبة الكروت ونطق (كتاب - كرة) 5 مرات مع ولي الأمر قبل النوم.',
                    'has_video' => false,
                    'video_path' => null,
                    'video_duration' => null,
                    'goals' => ['نطق صوت حرف /ك/ في أول الكلمة'],
                ]
            ];
        }
        $iepGoals = [];
        $latestSession = $child->therapySessions()->whereNotNull('goals_evaluated')->latest('session_date')->first();
        if ($latestSession && is_array($latestSession->goals_evaluated)) {
            foreach ($latestSession->goals_evaluated as $i => $goal) {
                if (is_array($goal)) {
                    $pct = (int) ($goal['percentage'] ?? 0);
                    $iepGoals[] = [
                        'id' => $i + 1,
                        'title' => $goal['text'] ?? 'هدف علاجي',
                        'category' => 'مهارة مستهدفة',
                        'progress' => $pct,
                        'status' => $pct >= 100 ? 'achieved' : 'in_progress',
                        'target_date' => $latestSession->session_date ? \Carbon\Carbon::parse($latestSession->session_date)->addMonth()->format('Y-m-d') : date('Y-m-d'),
                        'specialist' => $latestSession->specialist_name ?? $child->main_specialist,
                    ];
                } elseif (is_string($goal)) {
                    $iepGoals[] = [
                        'id' => $i + 1,
                        'title' => $goal,
                        'category' => 'مهارة مستهدفة',
                        'progress' => 0,
                        'status' => 'in_progress',
                        'target_date' => date('Y-m-d'),
                        'specialist' => $latestSession->specialist_name ?? $child->main_specialist,
                    ];
                }
            }
        }

        $parentNotes = [
            [
                'author' => $child->parent_name,
                'relation' => $child->parent_relation,
                'date' => 'منذ يومين',
                'text' => 'لاحظنا في البيت إنه بدأ يقول كلمة "كورة" بوضوح لما يلعب مع أخوه، شكراً جزيلاً للدكتور المتابع!',
                'reply' => 'خبر رائع جداً ومؤشر ممتاز على استجابته السريعة! سنركز في الجلسة القادمة على دمج الكلمة في جملة.',
                'doctor' => $child->main_specialist ?? 'د. أحمد يسري'
            ]
        ];

        $packageInfo = [
            'name' => 'باقة التأهيل الشامل المكثفة (12 جلسة)',
            'total_sessions' => 12,
            'completed_sessions' => count($recentSessions),
            'remaining_sessions' => max(0, 12 - count($recentSessions)),
            'expiry_date' => '2026-09-15',
            'status' => 'active'
        ];

        return view('children.show', compact('child', 'iepGoals', 'recentSessions', 'parentNotes', 'packageInfo'));
    }

    /**
     * Print standalone official A4 report for the child.
     */
    public function print(Child $child)
    {
        $dbSessions = $child->therapySessions()->latest('session_date')->get();
        $recentSessions = [];
        if ($dbSessions->isNotEmpty()) {
            foreach ($dbSessions as $s) {
                $recentSessions[] = [
                    'date' => $s->session_date ? $s->session_date->format('Y-m-d') : 'اليوم',
                    'time' => $s->session_time ?? '04:00 م',
                    'room' => $s->room_name,
                    'specialist' => $s->specialist_name,
                    'mood' => $s->child_mood,
                    'notes' => $s->clinical_notes,
                    'home_exercise' => $s->home_exercise,
                    'goals' => $s->goals_evaluated ?? [],
                ];
            }
        } else {
            $recentSessions = [
                [
                    'date' => '2026-08-20',
                    'time' => '04:00 م',
                    'room' => 'غرفة التخاطب 1',
                    'specialist' => $child->main_specialist ?? 'د. أحمد يسري',
                    'mood' => 'ممتاز ومتعاون ومتحمس',
                    'notes' => 'استجاب الطفل بشكل رائع للتدريب على صوت الكاف بالمرآة مع استخدام التعزيز الإيجابي.',
                    'home_exercise' => 'تكرار لعبة الكروت ونطق (كتاب - كرة) 5 مرات مع ولي الأمر قبل النوم.',
                    'goals' => ['نطق صوت حرف /ك/ في أول الكلمة'],
                ]
            ];
        }
        $iepGoals = [];
        $latestSession = $child->therapySessions()->whereNotNull('goals_evaluated')->latest('session_date')->first();
        if ($latestSession && is_array($latestSession->goals_evaluated)) {
            foreach ($latestSession->goals_evaluated as $i => $goal) {
                if (is_array($goal)) {
                    $pct = (int) ($goal['percentage'] ?? 0);
                    $iepGoals[] = [
                        'id' => $i + 1,
                        'title' => $goal['text'] ?? 'هدف علاجي',
                        'category' => 'مهارة مستهدفة',
                        'progress' => $pct,
                        'status' => $pct >= 100 ? 'achieved' : 'in_progress',
                        'target_date' => $latestSession->session_date ? \Carbon\Carbon::parse($latestSession->session_date)->addMonth()->format('Y-m-d') : date('Y-m-d'),
                        'specialist' => $latestSession->specialist_name ?? $child->main_specialist,
                    ];
                } elseif (is_string($goal)) {
                    $iepGoals[] = [
                        'id' => $i + 1,
                        'title' => $goal,
                        'category' => 'مهارة مستهدفة',
                        'progress' => 0,
                        'status' => 'in_progress',
                        'target_date' => date('Y-m-d'),
                        'specialist' => $latestSession->specialist_name ?? $child->main_specialist,
                    ];
                }
            }
        }

        return view('children.print', compact('child', 'recentSessions', 'iepGoals'));
    }

    /**
     * Show the form for editing an existing child.
     */
    public function edit(Child $child)
    {
        $specialists = $this->getSpecialistsList();
        $diagnoses = $this->getDiagnosesCategories();
        $packages = $this->getPackagesList();

        return view('children.edit', compact('child', 'specialists', 'diagnoses', 'packages'));
    }

    /**
     * Update the specified child profile in database.
     */
    public function update(Request $request, Child $child)
    {
        $validated = $request->validate([
            'national_id'         => ['required', 'string', 'size:14', \Illuminate\Validation\Rule::unique('children')->ignore($child->id)],
            'code'                => ['required', 'string', \Illuminate\Validation\Rule::unique('children')->ignore($child->id)],
            'name'                => 'required|string|max:100',
            'birth_date'          => 'required|date',
            'mental_age'          => 'nullable|string|max:100',
            'gender'              => 'required|in:male,female',
            'photo'               => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'parent_name'         => 'required|string|max:100',
            'parent_relation'     => 'required|string|max:50',
            'phone'               => 'required|string|max:25',
            'emergency_phone'     => 'nullable|string|max:25',
            'address'             => 'nullable|string|max:255',
            'diagnoses'           => 'nullable|array',
            'diagnoses.*'         => 'nullable|string|max:255',
            'initial_diagnosis'   => 'nullable|string|max:3000',
            'diagnosis_category'  => 'required|string',
            'iq_tests_history'    => 'nullable|string',
            'main_specialist'     => 'nullable|string|max:100',
            'neurologist_name'    => 'nullable|string|max:150',
            'current_medications' => 'nullable|string',
            'medical_notes'       => 'nullable|string',
            'assistive_devices'   => 'nullable|string|max:150',
            'package_type'        => 'nullable|string|max:50',
            'status'              => 'required|in:active,on_hold,discharged',
        ]);

        $diagnosesList = [];
        if ($request->has('diagnoses') && is_array($request->diagnoses)) {
            $diagnosesList = array_values(array_filter(array_map('trim', $request->diagnoses)));
        }

        if (!empty($diagnosesList)) {
            $validated['initial_diagnosis'] = json_encode($diagnosesList, JSON_UNESCAPED_UNICODE);
        }

        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('children_photos', 'public');
            $validated['photo_path'] = $path;
        }

        $child->update($validated);

        if ($child->user_id) {
            $user = \App\Models\User::find($child->user_id);
            if ($user) {
                $request->validate([
                    'national_id' => [\Illuminate\Validation\Rule::unique('users', 'username')->ignore($user->id)]
                ]);
                $user->update([
                    'name' => $validated['parent_name'],
                    'username' => $validated['national_id'],
                    'password' => \Illuminate\Support\Facades\Hash::make($validated['national_id']),
                ]);
            }
        } else {
            $request->validate([
                'national_id' => 'unique:users,username'
            ]);
            $user = \App\Models\User::create([
                'name' => $validated['parent_name'],
                'username' => $validated['national_id'],
                'password' => \Illuminate\Support\Facades\Hash::make($validated['national_id']),
                'role' => 'parent',
            ]);
            $child->update(['user_id' => $user->id]);
        }

        return redirect()->route('children.index')->with('success', "تم تحديث وحفظ بيانات ملف الطفل ({$child->name}) بنجاح! ");
    }

    private function getSpecialistsList(): array
    {
        $db = Specialist::where('status', 'active')->orderBy('name')->pluck('name')->toArray();
        return !empty($db) ? $db : [
            'د. أحمد يسري (أخصائي تخاطب ونطق)',
            'د. مروة كمال (تكامل حسي وتعديل سلوك)',
            'د. سارة إبراهيم (تأهيل تخاطب سمعي)',
            'أ. حسام فؤاد (صعوبات تعلم وتنمية مهارات)',
        ];
    }

    private function getDiagnosesCategories(): array
    {
        return [
            'speech' => 'تأخر نمو لغوي ونطق (لدغات / تلعثم)',
            'autism' => 'طيف توحد (ASD) وتواصل اجتماعي',
            'adhd' => 'فرط حركة وتشتت انتباه (ADHD)',
            'hearing' => 'ضعف سمعي / زراعة قوقعة إلكترونية',
            'down' => 'متلازمة داون وتأهيل شامل',
            'learning' => 'صعوبات تعلم وعسر قراءة (Dyslexia)',
            'behavior' => 'تعديل سلوك وعناد واضطرابات انفعالية',
            'other' => 'تشخيص أو تقييم أولي آخر',
        ];
    }

    private function getPackagesList(): array
    {
        return [
            'evaluation' => 'جلسة تقييم واختبارات أولية',
            'package_8' => 'باقة أساسية (8 جلسات شهرياً)',
            'package_12' => 'باقة مكثفة (12 جلسة شهرياً)',
            'package_24' => 'باقة تأهيل شامل مدمجة (24 جلسة)',
            'pay_per_session' => 'محاسبة بالجلسة المفردة',
        ];
    }
}


