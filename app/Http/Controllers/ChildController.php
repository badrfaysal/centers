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
            'medications_image'   => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
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

        if ($request->hasFile('medications_image')) {
            $path = $request->file('medications_image')->store('medications', 'public');
            $validated['medications_file'] = $path;
        }

        $child = Child::create($validated);

        return redirect()->route('children.index')->with('success', 'ØªÙ… Ø¥Ø¶Ø§ÙØ© Ø§Ù„Ø·ÙÙ„ Ø¨Ù†Ø¬Ø§Ø­. ÙŠÙ…ÙƒÙ† Ù„ÙˆÙ„ÙŠ Ø§Ù„Ø£Ù…Ø± Ø§Ù„Ø¢Ù† Ø¥Ù†Ø´Ø§Ø¡ Ø­Ø³Ø§Ø¨Ù‡ Ù…Ù† Ø§Ù„Ø¨ÙˆØ§Ø¨Ø©.');
    }

    /**
     * Display the 360Â° profile of the specified child.
     */
    public function show(Child $child)
    {
        $dbSessions = $child->therapySessions()->latest('session_date')->get();

        $recentSessions = [];
        if ($dbSessions->isNotEmpty()) {
            foreach ($dbSessions as $s) {
                $recentSessions[] = [
                    'date' => $s->session_date ? $s->session_date->format('Y-m-d') : 'Ø§Ù„ÙŠÙˆÙ…',
                    'time' => $s->session_time ?? '04:00 Ù…',
                    'room' => $s->room_name,
                    'specialist' => $s->specialist_name,
                    'mood' => $s->child_mood,
                    'mood_color' => 'emerald',
                    'notes' => $s->clinical_notes,
                    'home_exercise' => $s->home_exercise,
                    'has_video' => !empty($s->video_path),
                    'video_path' => is_array($s->video_path) 
                        ? (count($s->video_path) > 0 ? (str_starts_with($s->video_path[0], "http") ? $s->video_path[0] : asset("storage/" . $s->video_path[0])) : null) 
                        : ($s->video_path ? (str_starts_with($s->video_path, "http") ? $s->video_path : asset("storage/" . $s->video_path)) : null),
                    'all_videos' => is_array($s->video_path) 
                        ? array_map(function($p) { return str_starts_with($p, "http") ? $p : asset("storage/" . $p); }, $s->video_path)
                        : ($s->video_path ? [(str_starts_with($s->video_path, "http") ? $s->video_path : asset("storage/" . $s->video_path))] : []),

                    'video_duration' => $s->video_duration ?? '0:30 Ø¯Ù‚ÙŠÙ‚Ø©',
                    'goals' => $s->goals_evaluated ?? [],
                ];
            }
        }
        
        $iepGoals = [];
        $latestSession = $child->therapySessions()->whereNotNull('goals_evaluated')->latest('session_date')->first();
        if ($latestSession && is_array($latestSession->goals_evaluated)) {
            foreach ($latestSession->goals_evaluated as $i => $goal) {
                if (is_array($goal)) {
                    $pct = (int) ($goal['percentage'] ?? 0);
                    $iepGoals[] = [
                        'id' => $i + 1,
                        'title' => $goal['text'] ?? 'Ù‡Ø¯Ù Ø¹Ù„Ø§Ø¬ÙŠ',
                        'category' => 'Ù…Ù‡Ø§Ø±Ø© Ù…Ø³ØªÙ‡Ø¯ÙØ©',
                        'progress' => $pct,
                        'status' => $pct >= 100 ? 'achieved' : 'in_progress',
                        'target_date' => $latestSession->session_date ? \Carbon\Carbon::parse($latestSession->session_date)->addMonth()->format('Y-m-d') : date('Y-m-d'),
                        'specialist' => $latestSession->specialist_name ?? $child->main_specialist,
                    ];
                } elseif (is_string($goal)) {
                    $iepGoals[] = [
                        'id' => $i + 1,
                        'title' => $goal,
                        'category' => 'Ù…Ù‡Ø§Ø±Ø© Ù…Ø³ØªÙ‡Ø¯ÙØ©',
                        'progress' => 0,
                        'status' => 'in_progress',
                        'target_date' => date('Y-m-d'),
                        'specialist' => $latestSession->specialist_name ?? $child->main_specialist,
                    ];
                }
            }
        }

        $parentNotes = [];

        $packageInfo = [
            'name' => 'Ø¨Ø§Ù‚Ø© Ø§Ù„Ø¬Ù„Ø³Ø§Øª',
            'total_sessions' => 0,
            'completed_sessions' => 0,
            'remaining_sessions' => 0,
            'expiry_date' => null,
            'status' => 'inactive'
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
                    'date' => $s->session_date ? $s->session_date->format('Y-m-d') : 'Ø§Ù„ÙŠÙˆÙ…',
                    'time' => $s->session_time ?? '04:00 Ù…',
                    'room' => $s->room_name,
                    'specialist' => $s->specialist_name,
                    'mood' => $s->child_mood,
                    'notes' => $s->clinical_notes,
                    'home_exercise' => $s->home_exercise,
                    'goals' => $s->goals_evaluated ?? [],
                ];
            }
        }
        
        $iepGoals = [];
        $latestSession = $child->therapySessions()->whereNotNull('goals_evaluated')->latest('session_date')->first();
        if ($latestSession && is_array($latestSession->goals_evaluated)) {
            foreach ($latestSession->goals_evaluated as $i => $goal) {
                if (is_array($goal)) {
                    $pct = (int) ($goal['percentage'] ?? 0);
                    $iepGoals[] = [
                        'id' => $i + 1,
                        'title' => $goal['text'] ?? 'Ù‡Ø¯Ù Ø¹Ù„Ø§Ø¬ÙŠ',
                        'category' => 'Ù…Ù‡Ø§Ø±Ø© Ù…Ø³ØªÙ‡Ø¯ÙØ©',
                        'progress' => $pct,
                        'status' => $pct >= 100 ? 'achieved' : 'in_progress',
                        'target_date' => $latestSession->session_date ? \Carbon\Carbon::parse($latestSession->session_date)->addMonth()->format('Y-m-d') : date('Y-m-d'),
                        'specialist' => $latestSession->specialist_name ?? $child->main_specialist,
                    ];
                } elseif (is_string($goal)) {
                    $iepGoals[] = [
                        'id' => $i + 1,
                        'title' => $goal,
                        'category' => 'Ù…Ù‡Ø§Ø±Ø© Ù…Ø³ØªÙ‡Ø¯ÙØ©',
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
            'phone'               => ['required', 'string', 'max:25'],
            'code'                => ['required', 'string', \Illuminate\Validation\Rule::unique('children')->ignore($child->id)],
            'name'                => 'required|string|max:100',
            'birth_date'          => 'required|date',
            'mental_age'          => 'nullable|string|max:100',
            'gender'              => 'required|in:male,female',
            'photo'               => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'parent_name'         => 'required|string|max:100',
            'parent_relation'     => 'required|string|max:50',
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
            'medications_image'   => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
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

        if ($request->hasFile('medications_image')) {
            $path = $request->file('medications_image')->store('medications', 'public');
            $validated['medications_file'] = $path;
        }

        $child->update($validated);

        return redirect()->route('children.index')->with('success', "ØªÙ… ØªØ­Ø¯ÙŠØ« ÙˆØ­ÙØ¸ Ø¨ÙŠØ§Ù†Ø§Øª Ù…Ù„Ù Ø§Ù„Ø·ÙÙ„ ({$child->name}) Ø¨Ù†Ø¬Ø§Ø­!");
    }

    private function getSpecialistsList(): array
    {
        $db = Specialist::where('status', 'active')->orderBy('name')->pluck('name')->toArray();
        return !empty($db) ? $db : [
            'Ø¯. Ø£Ø­Ù…Ø¯ ÙŠØ³Ø±ÙŠ (Ø£Ø®ØµØ§Ø¦ÙŠ ØªØ®Ø§Ø·Ø¨ ÙˆÙ†Ø·Ù‚)',
            'Ø¯. Ù…Ø±ÙˆØ© ÙƒÙ…Ø§Ù„ (ØªÙƒØ§Ù…Ù„ Ø­Ø³ÙŠ ÙˆØªØ¹Ø¯ÙŠÙ„ Ø³Ù„ÙˆÙƒ)',
            'Ø¯. Ø³Ø§Ø±Ø© Ø¥Ø¨Ø±Ø§Ù‡ÙŠÙ… (ØªØ£Ù‡ÙŠÙ„ ØªØ®Ø§Ø·Ø¨ Ø³Ù…Ø¹ÙŠ)',
            'Ø£. Ø­Ø³Ø§Ù… ÙØ¤Ø§Ø¯ (ØµØ¹ÙˆØ¨Ø§Øª ØªØ¹Ù„Ù… ÙˆØªÙ†Ù…ÙŠØ© Ù…Ù‡Ø§Ø±Ø§Øª)',
        ];
    }

    private function getDiagnosesCategories(): array
    {
        return \App\Http\Controllers\SettingController::getDropdownList('diagnoses');
    }

    private function getPackagesList(): array
    {
        return [
            'evaluation' => 'Ø¬Ù„Ø³Ø© ØªÙ‚ÙŠÙŠÙ… ÙˆØ§Ø®ØªØ¨Ø§Ø±Ø§Øª Ø£ÙˆÙ„ÙŠØ©',
            'package_8' => 'Ø¨Ø§Ù‚Ø© Ø£Ø³Ø§Ø³ÙŠØ© (8 Ø¬Ù„Ø³Ø§Øª Ø´Ù‡Ø±ÙŠØ§Ù‹)',
            'package_12' => 'Ø¨Ø§Ù‚Ø© Ù…ÙƒØ«ÙØ© (12 Ø¬Ù„Ø³Ø© Ø´Ù‡Ø±ÙŠØ§Ù‹)',
            'package_24' => 'Ø¨Ø§Ù‚Ø© ØªØ£Ù‡ÙŠÙ„ Ø´Ø§Ù…Ù„ Ù…Ø¯Ù…Ø¬Ø© (24 Ø¬Ù„Ø³Ø©)',
            'pay_per_session' => 'Ù…Ø­Ø§Ø³Ø¨Ø© Ø¨Ø§Ù„Ø¬Ù„Ø³Ø© Ø§Ù„Ù…ÙØ±Ø¯Ø©',
        ];
    }

    public function uploadMedications(Request $request, Child $child)
    {
        $request->validate([
            'medications_file' => 'required|file|mimes:jpeg,png,jpg,webp,pdf,doc,docx|max:10240',
        ]);

        if ($request->hasFile('medications_file')) {
            if ($child->medications_file) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($child->medications_file);
            }
            $path = $request->file('medications_file')->store('children/medications', 'public');
            $child->update(['medications_file' => $path]);
        }

        return redirect()->back()->with('success', 'ØªÙ… Ø±ÙØ¹ Ù…Ù„Ù Ø§Ù„Ø£Ø¯ÙˆÙŠØ© Ø¨Ù†Ø¬Ø§Ø­.');
    }

    public function storeTest(Request $request, Child $child)
    {
        $validated = $request->validate([
            'test_name' => 'required|string|max:255',
            'test_date' => 'required|date',
            'score'     => 'nullable|string|max:255',
            'notes'     => 'nullable|string',
            'file_path' => 'nullable|file|mimes:jpeg,png,jpg,webp,pdf,doc,docx|max:10240',
        ]);

        if ($request->hasFile('file_path')) {
            $validated['file_path'] = $request->file('file_path')->store('children/tests', 'public');
        }

        $child->tests()->create($validated);

        return redirect()->back()->with('success', 'ØªÙ… Ø¥Ø¶Ø§ÙØ© Ø§Ù„Ø§Ø®ØªØ¨Ø§Ø± Ø¨Ù†Ø¬Ø§Ø­.');
    }

    public function destroyTest(\App\Models\ChildTest $test)
    {
        if ($test->file_path) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($test->file_path);
        }
        $test->delete();

        return redirect()->back()->with('success', 'ØªÙ… Ø­Ø°Ù Ø§Ù„Ø§Ø®ØªØ¨Ø§Ø± Ø¨Ù†Ø¬Ø§Ø­.');
    }
}

