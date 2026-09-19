<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Expense;
use App\Models\TherapySession;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Http\Controllers\SettingController;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $settings = SettingController::getSettings();
        
        // Date Filtering
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));

        // 1. Financials
        $incomes = Invoice::whereBetween('invoice_date', [$startDate, $endDate])->sum('paid_amount');
        $expenses = Expense::whereBetween('expense_date', [$startDate, $endDate])->sum('amount');
        $netProfit = $incomes - $expenses;
        $debts = Invoice::whereBetween('invoice_date', [$startDate, $endDate])->sum('remaining_amount');

        // 2. System Stats
        $totalSessions = TherapySession::whereBetween('session_date', [$startDate, $endDate])->count();
        $newChildrenCount = \App\Models\Child::whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])->count();
        $totalChildrenCount = \App\Models\Child::count();
        $newBookingsCount = \App\Models\ConsultationBooking::whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])->count();
        $specialistsCount = \App\Models\Specialist::count();
        $invoicesCount = Invoice::whereBetween('invoice_date', [$startDate, $endDate])->count();
        
        // 3. Daily Data for Chart (Income vs Expenses)
        $dailyIncomes = Invoice::selectRaw('invoice_date as date, SUM(paid_amount) as total')
            ->whereBetween('invoice_date', [$startDate, $endDate])
            ->groupBy('invoice_date')
            ->get()->keyBy('date');
            
        $dailyExpenses = Expense::selectRaw('expense_date as date, SUM(amount) as total')
            ->whereBetween('expense_date', [$startDate, $endDate])
            ->groupBy('expense_date')
            ->get()->keyBy('date');

        // Generate date range array for the chart
        $chartDates = [];
        $chartIncomes = [];
        $chartExpenses = [];
        
        $currentDate = Carbon::parse($startDate);
        $endDateObj = Carbon::parse($endDate);
        
        while ($currentDate <= $endDateObj) {
            $dateStr = $currentDate->format('Y-m-d');
            $chartDates[] = $dateStr;
            $chartIncomes[] = isset($dailyIncomes[$dateStr]) ? $dailyIncomes[$dateStr]->total : 0;
            $chartExpenses[] = isset($dailyExpenses[$dateStr]) ? $dailyExpenses[$dateStr]->total : 0;
            $currentDate->addDay();
        }

        // 4. Attendance & Apologies Stats
        $totalApologies = \App\Models\SessionSchedule::whereBetween('session_date', [$startDate, $endDate])
            ->where('status', 'cancelled')
            ->count();

        $mostApologizedChildData = \App\Models\SessionSchedule::whereBetween('session_date', [$startDate, $endDate])
            ->where('status', 'cancelled')
            ->selectRaw('child_id, count(*) as count')
            ->groupBy('child_id')
            ->orderByDesc('count')
            ->first();
        $mostApologizedChild = $mostApologizedChildData ? \App\Models\Child::find($mostApologizedChildData->child_id) : null;
        $mostApologizedCount = $mostApologizedChildData ? $mostApologizedChildData->count : 0;

        $mostAttendedChildData = \App\Models\TherapySession::whereBetween('session_date', [$startDate, $endDate])
            ->selectRaw('child_id, count(*) as count')
            ->groupBy('child_id')
            ->orderByDesc('count')
            ->first();
        $mostAttendedChild = $mostAttendedChildData ? \App\Models\Child::find($mostAttendedChildData->child_id) : null;
        $mostAttendedCount = $mostAttendedChildData ? $mostAttendedChildData->count : 0;

        $childrenAbsence = [];
        $allChildren = \App\Models\Child::all();
        foreach ($allChildren as $child) {
            $lastSession = \App\Models\SessionSchedule::where('child_id', $child->id)
                ->where('attendance_status', 'attended')
                ->where('session_date', '<=', Carbon::now()->endOfDay())
                ->orderBy('session_date', 'desc')
                ->first();
                
            if ($lastSession) {
                $daysAbsent = (int) Carbon::parse($lastSession->session_date)->startOfDay()->diffInDays(Carbon::now()->startOfDay());
                $childrenAbsence[] = [
                    'child_name' => $child->name,
                    'days_absent' => $daysAbsent,
                    'last_session_date' => $lastSession->session_date->format('Y-m-d')
                ];
            } else {
                $childrenAbsence[] = [
                    'child_name' => $child->name,
                    'days_absent' => -1, // Using -1 for sorting "Never attended"
                    'last_session_date' => '-'
                ];
            }
        }
        usort($childrenAbsence, function($a, $b) {
            return $b['days_absent'] <=> $a['days_absent'];
        });

        // 5. Expenses by Category
        $expensesByCategory = Expense::selectRaw('statement as category, SUM(amount) as total')
            ->whereBetween('expense_date', [$startDate, $endDate])
            ->groupBy('statement')
            ->orderByDesc('total')
            ->get();

        return view('reports.index', compact(
            'settings', 'startDate', 'endDate', 
            'incomes', 'expenses', 'netProfit', 'debts',
            'totalSessions', 'newChildrenCount', 'totalChildrenCount', 'newBookingsCount', 'specialistsCount', 'invoicesCount',
            'chartDates', 'chartIncomes', 'chartExpenses',
            'totalApologies', 'mostApologizedChild', 'mostApologizedCount',
            'mostAttendedChild', 'mostAttendedCount', 'childrenAbsence',
            'expensesByCategory'
        ));
    }
}
