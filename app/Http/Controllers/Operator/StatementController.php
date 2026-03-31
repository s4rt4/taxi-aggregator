<?php

namespace App\Http\Controllers\Operator;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class StatementController extends Controller
{
    public function index(Request $request)
    {
        $operator = auth()->user()->operator;

        if (!$operator) {
            $statements = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 10);

            return view('operator.statements.index', compact('statements'));
        }

        $query = $operator->statements()->latest('period_start');

        if ($request->filled('date_from')) {
            $query->whereDate('period_start', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('period_end', '<=', $request->date_to);
        }

        $statements = $query->paginate(10)->appends($request->query());

        return view('operator.statements.index', compact('statements'));
    }
}
