<?php

namespace App\Http\Controllers\Corporate;

use App\Http\Controllers\Controller;
use App\Models\CorporateInvoice;

class InvoiceController extends Controller
{
    protected function requireCorporate()
    {
        $user = auth()->user();
        $corporate = $user->activeCorporate();

        if (!$corporate || $corporate->status !== 'approved') {
            abort(403, 'Your corporate account is not yet active.');
        }

        return $corporate;
    }

    public function index()
    {
        $corporate = $this->requireCorporate();

        $invoices = $corporate->invoices()
            ->latest()
            ->paginate(25);

        return view('corporate.invoices.index', compact('corporate', 'invoices'));
    }

    public function show(CorporateInvoice $invoice)
    {
        $corporate = $this->requireCorporate();

        abort_unless($invoice->corporate_id === $corporate->id, 403);

        $invoice->load(['bookings.operator', 'bookings.fleetType', 'bookings.passenger']);

        return view('corporate.invoices.show', compact('corporate', 'invoice'));
    }

    public function download(CorporateInvoice $invoice)
    {
        $corporate = $this->requireCorporate();

        abort_unless($invoice->corporate_id === $corporate->id, 403);

        $invoice->load(['bookings.operator', 'bookings.fleetType', 'bookings.passenger']);

        return view('invoices.corporate', [
            'invoice' => $invoice,
            'corporate' => $corporate,
            'vat_rate' => 20,
        ]);
    }
}
