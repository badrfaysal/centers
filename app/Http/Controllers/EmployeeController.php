<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'job_title' => 'required|string|max:100',
            'salary' => 'required|numeric|min:0',
            'phone' => 'nullable|string|max:25',
        ]);

        \App\Models\Employee::create($validated);

        return back()->with('success', 'تم إضافة الموظف بنجاح');
    }

    public function destroy(\App\Models\Employee $employee)
    {
        $employee->delete();
        return back()->with('success', 'تم حذف الموظف بنجاح');
    }
}
