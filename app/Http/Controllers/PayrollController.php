<?php

namespace App\Http\Controllers;

use App\Models\Specialist;
use App\Models\Expense;
use App\Models\TherapySession;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Http\Controllers\SettingController;

class PayrollController extends Controller
{
    public function index()
    {
        $settings = SettingController::getSettings();
        $specialists = Specialist::where('status', 'active')->get();
        $employees = \App\Models\Employee::where('status', 'active')->get();
        
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        foreach ($specialists as $specialist) {
            $specialist->bonus = 0;
            $specialist->advance = 0;

            if ($specialist->salary_type === 'per_session') {
                $sessionsCount = TherapySession::where('specialist_name', 'like', "%{$specialist->name}%")
                    ->whereMonth('session_date', $currentMonth)
                    ->whereYear('session_date', $currentYear)
                    ->count();
                $specialist->calculated_salary = $sessionsCount * $specialist->session_rate;
                $specialist->notes = "($sessionsCount جلسة × {$specialist->session_rate})";
            } else {
                $specialist->calculated_salary = $specialist->session_rate;
                $specialist->notes = "راتب شهري / ثابت";
            }
        }
        
        foreach ($employees as $employee) {
            $employee->bonus = 0;
            $employee->advance = 0;
            $employee->calculated_salary = $employee->salary;
            $employee->notes = "راتب شهري / ثابت";
        }

        return view('payroll.index', compact('settings', 'specialists', 'employees', 'currentMonth', 'currentYear'));
    }

    public function payAll(Request $request)
    {
        $validated = $request->validate([
            'payroll' => 'nullable|array',
            'payroll.*.base_salary' => 'required|numeric|min:0',
            'payroll.*.bonus' => 'nullable|numeric|min:0',
            'payroll.*.advance' => 'nullable|numeric|min:0',
            'employees_payroll' => 'nullable|array',
            'employees_payroll.*.base_salary' => 'required|numeric|min:0',
            'employees_payroll.*.bonus' => 'nullable|numeric|min:0',
            'employees_payroll.*.advance' => 'nullable|numeric|min:0',
        ]);

        $count = 0;
        
        if ($request->has('payroll')) {
            foreach ($request->payroll as $specialistId => $data) {
                $specialist = Specialist::find($specialistId);
                if (!$specialist) continue;

                $base = floatval($data['base_salary']);
                $bonus = floatval($data['bonus'] ?? 0);
                $advance = floatval($data['advance'] ?? 0);
                $net = $base + $bonus - $advance;

                if ($net > 0) {
                    $notes = "راتب أساسي/مستحق: $base";
                    if ($bonus > 0) $notes .= " | مكافأة: $bonus";
                    if ($advance > 0) $notes .= " | سلفة/خصم: $advance";

                    Expense::create([
                        'amount' => $net,
                        'statement' => "مرتبات أخصائيين - " . $specialist->name,
                        'expense_date' => Carbon::now()->format('Y-m-d'),
                        'notes' => $notes,
                    ]);
                    $count++;
                }
            }
        }
        
        if ($request->has('employees_payroll')) {
            foreach ($request->employees_payroll as $employeeId => $data) {
                $employee = \App\Models\Employee::find($employeeId);
                if (!$employee) continue;

                $base = floatval($data['base_salary']);
                $bonus = floatval($data['bonus'] ?? 0);
                $advance = floatval($data['advance'] ?? 0);
                $net = $base + $bonus - $advance;

                if ($net > 0) {
                    $notes = "راتب أساسي/مستحق: $base";
                    if ($bonus > 0) $notes .= " | مكافأة: $bonus";
                    if ($advance > 0) $notes .= " | سلفة/خصم: $advance";

                    Expense::create([
                        'amount' => $net,
                        'statement' => "مرتبات موظفين - " . $employee->name,
                        'expense_date' => Carbon::now()->format('Y-m-d'),
                        'notes' => $notes,
                    ]);
                    $count++;
                }
            }
        }

        return redirect()->route('expenses.index')->with('success', "تم صرف المرتبات بنجاح لـ $count شخص، وتم إدراجها في المصروفات تلقائياً.");
    }
}
