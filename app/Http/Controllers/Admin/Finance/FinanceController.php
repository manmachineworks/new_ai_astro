<?php

namespace App\Http\Controllers\Admin\Finance;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class FinanceController extends Controller
{
    public function earnings()
    {
        // Simple KPIs
        $totalRevenue = \App\Models\PaymentOrder::where('status', 'completed')->sum('amount');
        $totalPaidOut = \App\Models\WalletTransaction::where('type', 'debit')->where('reference_type', 'payout')->sum('amount');
        $platformBalance = $totalRevenue - $totalPaidOut; // Rough estimate

        return view('admin.finance.earnings', compact('totalRevenue', 'totalPaidOut', 'platformBalance'));
    }

    public function refunds(Request $request)
    {
        // Re-use the Refund Report logic or view
        // Ideally we redirect to the specialized report we built earlier
        return redirect()->route('admin.reports.refunds');
    }

    public function commissions()
    {
        $settings = \App\Models\PricingSetting::all();
        return view('admin.finance.commissions', compact('settings'));
    }

    public function exports()
    {
        return view('admin.finance.exports');
    }
}
