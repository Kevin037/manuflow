<?php

namespace App\Http\Controllers;

use App\Models\Account;
use Illuminate\Http\Request;

class ChartOfAccountController extends Controller
{
    public function index(Request $request)
    {
        $accounts = Account::with('parent')
            ->orderBy('code','asc')
            ->get();

        return view('accounting.chart.index', compact('accounts'));
    }
}
