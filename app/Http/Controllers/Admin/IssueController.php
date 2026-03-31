<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TripIssue;
use Illuminate\Http\Request;

class IssueController extends Controller
{
    public function index(Request $request)
    {
        $query = TripIssue::with(['booking', 'operator']);

        if ($request->filled('issue_type')) {
            $query->where('issue_type', $request->issue_type);
        }

        if ($request->filled('investigation_status')) {
            $query->where('investigation_status', $request->investigation_status);
        }

        $issues = $query->latest()->paginate(20)->withQueryString();

        return view('admin.issues', compact('issues'));
    }
}
