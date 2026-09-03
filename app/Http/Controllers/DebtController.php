<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Illuminate\Http\Request;

class DebtController extends Controller
{
    /**
     * عرض كل الديون المعلقة مجمّعة حسب ولي الأمر.
     */
    public function index(Request $request)
    {
        $query = Invoice::where('remaining_amount', '>', 0)
            ->with(['child', 'specialist'])
            ->latest('invoice_date');

        // بحث
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('parent_name', 'like', "%{$search}%")
                  ->orWhere('child_name', 'like', "%{$search}%")
                  ->orWhere('invoice_number', 'like', "%{$search}%");
            });
        }

        $debtInvoices = $query->get();

        // تجميع حسب ولي الأمر
        $debtsByParent = $debtInvoices->groupBy('parent_name');

        // إحصائيات
        $totalDebts      = $debtInvoices->sum('remaining_amount');
        $parentsCount    = $debtsByParent->count();

        $settings = \App\Http\Controllers\SettingController::getSettings();

        return view('debts.index', compact('debtsByParent', 'totalDebts', 'parentsCount', 'settings'));
    }

    /**
     * تسديد جزئي أو كلي لدين فاتورة.
     */
    public function payDebt(Request $request, Invoice $invoice)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01|max:' . $invoice->remaining_amount,
        ]);

        $newPaid      = $invoice->paid_amount + $request->amount;
        $newRemaining = $invoice->net_amount - $newPaid;

        if ($newRemaining <= 0) {
            $newRemaining = 0;
            $paymentStatus = 'paid';
        } else {
            $paymentStatus = 'partial';
        }

        $invoice->update([
            'paid_amount'      => $newPaid,
            'remaining_amount' => $newRemaining,
            'payment_status'   => $paymentStatus,
        ]);

        return redirect()->route('debts.index')
            ->with('success', "تم تسديد مبلغ ({$request->amount}) على الفاتورة ({$invoice->invoice_number}) بنجاح!");
    }
}
