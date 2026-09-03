<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use Illuminate\Http\Request;
use App\Http\Controllers\SettingController;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        $settings = SettingController::getSettings();
        
        $query = Expense::query();
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('statement', 'like', "%{$search}%")
                  ->orWhere('notes', 'like', "%{$search}%");
        }

        $expenses = $query->orderBy('expense_date', 'desc')
                          ->orderBy('created_at', 'desc')
                          ->paginate(15);
                          
        $totalExpenses = Expense::sum('amount');
        $thisMonthExpenses = Expense::whereMonth('expense_date', date('m'))
                                    ->whereYear('expense_date', date('Y'))
                                    ->sum('amount');

        // Chart Data: Expenses by Statement (Top 7)
        $expensesChartData = Expense::selectRaw('statement, SUM(amount) as total')
            ->groupBy('statement')
            ->orderByDesc('total')
            ->limit(7)
            ->get();

        return view('expenses.index', compact('settings', 'expenses', 'totalExpenses', 'thisMonthExpenses', 'expensesChartData'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'amount'       => 'required|numeric|min:0.01',
            'statement'    => 'required|string|max:255',
            'expense_date' => 'required|date',
            'notes'        => 'nullable|string|max:1000',
        ]);

        Expense::create($validated);

        return redirect()->route('expenses.index')->with('success', 'تم تسجيل المصروف بنجاح');
    }

    public function destroy(Expense $expense)
    {
        $expense->delete();
        return redirect()->route('expenses.index')->with('success', 'تم حذف المصروف بنجاح');
    }
}
