<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Statement;
use Illuminate\Http\Request;

class StatementController extends Controller
{
    public function index(Request $request)
    {
        $query = Statement::with('operator');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('operator_id')) {
            $query->where('operator_id', $request->operator_id);
        }

        $statements = $query->latest()->paginate(20)->withQueryString();

        return view('admin.statements', compact('statements'));
    }
}
