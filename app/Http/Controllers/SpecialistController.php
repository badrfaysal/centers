<?php

namespace App\Http\Controllers;

use App\Models\Specialist;
use App\Models\Child;
use App\Models\TherapySession;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SpecialistController extends Controller
{
    /**
     * Display a listing of specialists.
     */
    public function index(Request $request)
    {
        $query = Specialist::withCount(['children', 'therapySessions']);

        $query->filterAndSort(
            $request,
            ['name', 'code', 'phone', 'specialization'], // Searchable fields
            ['specialization', 'status']                 // Filterable fields
        );

        $specialists = $query->paginate(12)->withQueryString();
        $totalCount = Specialist::count();
        $activeCount = Specialist::where('status', 'active')->count();
        $specializations = $this->getSpecializationsList();

        return view('specialists.index', compact('specialists', 'totalCount', 'activeCount', 'specializations'));
    }

    /**
     * Show the form for creating a new specialist.
     */
    public function create()
    {
        $nextCode = Specialist::generateNextCode();
        $specializations = $this->getSpecializationsList();
        $rooms = $this->getRoomsList();
        $daysList = ['السبت', 'الأحد', 'الإثنين', 'الثلاثاء', 'الأربعاء', 'الخميس', 'الجمعة'];

        return view('specialists.create', compact('nextCode', 'specializations', 'rooms', 'daysList'));
    }

    /**
     * Store a newly created specialist in database.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'phone'            => 'required|string|max:25|unique:specialists,phone',
            'code'             => 'required|string|unique:specialists,code',
            'name'             => 'required|string|max:100',
            'specialization'   => 'required|string|max:100',
            'job_title'        => 'required|string|max:100',
            'email'            => 'nullable|email|max:100',
            'license_number'   => 'nullable|string|max:100',
            'qualification'    => 'nullable|string|max:255',
            'experience_years' => 'required|integer|min:0|max:50',
            'default_room'     => 'nullable|string|max:100',
            'work_days'        => 'nullable|array',
            'salary_type'      => 'required|in:monthly,per_session',
            'session_rate'     => 'nullable|numeric|min:0',
            'photo'            => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'bio'              => 'nullable|string|max:2000',
            'status'           => 'required|in:active,on_leave,inactive',
        ]);

        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('specialists_photos', 'public');
            $validated['photo_path'] = $path;
        }

        $specialist = Specialist::create($validated);

        return redirect()->route('specialists.index')
            ->with('success', "تم تسجيل وإضافة الأخصائي ({$specialist->name}) بكود ({$specialist->code}) بنجاح! يمكنه الآن التوجه לבوابة الأخصائيين لإنشاء حساب.");
    }

    /**
     * Display the specified specialist profile.
     */
    public function show(Specialist $specialist)
    {
        $assignedChildren = Child::where('main_specialist', 'like', "%{$specialist->name}%")->get();
        $recentSessions = TherapySession::where('specialist_name', 'like', "%{$specialist->name}%")->with('child')->latest('session_date')->take(10)->get();

        return view('specialists.show', compact('specialist', 'assignedChildren', 'recentSessions'));
    }

    /**
     * Show the form for editing the specified specialist.
     */
    public function edit(Specialist $specialist)
    {
        $specializations = $this->getSpecializationsList();
        $rooms = $this->getRoomsList();
        $daysList = ['السبت', 'الأحد', 'الإثنين', 'الثلاثاء', 'الأربعاء', 'الخميس', 'الجمعة'];

        return view('specialists.edit', compact('specialist', 'specializations', 'rooms', 'daysList'));
    }

    /**
     * Update the specified specialist in database.
     */
    public function update(Request $request, Specialist $specialist)
    {
        $validated = $request->validate([
            'phone'            => ['required', 'string', 'max:25', \Illuminate\Validation\Rule::unique('specialists')->ignore($specialist->id)],
            'code'             => ['required', 'string', \Illuminate\Validation\Rule::unique('specialists')->ignore($specialist->id)],
            'name'             => 'required|string|max:100',
            'specialization'   => 'required|string|max:100',
            'job_title'        => 'required|string|max:100',
            'email'            => 'nullable|email|max:100',
            'license_number'   => 'nullable|string|max:100',
            'qualification'    => 'nullable|string|max:255',
            'experience_years' => 'required|integer|min:0|max:50',
            'default_room'     => 'nullable|string|max:100',
            'work_days'        => 'nullable|array',
            'salary_type'      => 'required|in:monthly,per_session',
            'session_rate'     => 'nullable|numeric|min:0',
            'photo'            => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'bio'              => 'nullable|string|max:2000',
            'status'           => 'required|in:active,on_leave,inactive',
        ]);

        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('specialists_photos', 'public');
            $validated['photo_path'] = $path;
        }

        $specialist->update($validated);

        return redirect()->route('specialists.index')
            ->with('success', "تم تحديث بيانات الأخصائي ({$specialist->name}) بنجاح.");
    }

    /**
     * Remove the specified specialist.
     */
    public function destroy(Specialist $specialist)
    {
        $specialist->delete();
        return redirect()->route('specialists.index')->with('success', 'تم حذف الأخصائي بنجاح.');
    }

    private function getSpecializationsList(): array
    {
        $list = \App\Http\Controllers\SettingController::getDropdownList('specializations');
        $assoc = [];
        foreach ($list as $item) {
            $assoc[$item] = $item;
        }
        return $assoc;
    }

    private function getRoomsList(): array
    {
        return \App\Http\Controllers\SettingController::getDropdownList('rooms');
    }
}