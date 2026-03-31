<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Dispute;
use App\Models\DisputeMessage;
use Illuminate\Http\Request;

class DisputeController extends Controller
{
    public function index(Request $request)
    {
        $query = Dispute::with(['booking', 'raisedBy']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('reference', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $disputes = $query->latest()->paginate(20)->withQueryString();

        return view('admin.disputes.index', compact('disputes'));
    }

    public function show(Dispute $dispute)
    {
        $dispute->load([
            'booking',
            'booking.operator',
            'booking.passenger',
            'raisedBy',
            'resolvedBy',
            'messages.user',
        ]);

        return view('admin.disputes.show', compact('dispute'));
    }

    public function resolve(Request $request, Dispute $dispute)
    {
        $request->validate([
            'resolution' => 'required|in:refund,credit,no_action,warning,suspend',
            'resolution_notes' => 'required|string|max:2000',
            'refund_amount' => 'nullable|numeric|min:0',
        ]);

        $dispute->update([
            'status' => 'resolved',
            'resolution' => $request->resolution,
            'resolution_notes' => $request->resolution_notes,
            'refund_amount' => $request->refund_amount,
            'resolved_by' => auth()->id(),
            'resolved_at' => now(),
        ]);

        return back()->with('success', 'Dispute has been resolved.');
    }

    public function addMessage(Request $request, Dispute $dispute)
    {
        $request->validate([
            'message' => 'required|string|max:2000',
            'is_internal' => 'boolean',
        ]);

        DisputeMessage::create([
            'dispute_id' => $dispute->id,
            'user_id' => auth()->id(),
            'message' => $request->message,
            'is_internal' => $request->boolean('is_internal', false),
        ]);

        return back()->with('success', 'Message added to dispute.');
    }
}
