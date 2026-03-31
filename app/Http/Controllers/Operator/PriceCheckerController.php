<?php

namespace App\Http\Controllers\Operator;

use App\Http\Controllers\Controller;

class PriceCheckerController extends Controller
{
    /**
     * Show the price checker tool.
     *
     * For now, just displays the view.
     * Data will come from QuoteService later.
     */
    public function index()
    {
        $operator = auth()->user()->operator;

        return view('operator.price-checker.index', compact('operator'));
    }
}
