<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Statement;
use App\Services\CashCommissionService;
use App\Services\InvoiceService;

class InvoiceController extends Controller
{
    /**
     * Display a printable invoice for a booking.
     */
    public function show(Booking $booking)
    {
        abort_unless(
            $booking->passenger_id === auth()->id() ||
            auth()->user()->isAdmin() ||
            (auth()->user()->isOperator() && $booking->operator_id === auth()->user()->operator?->id),
            403
        );

        $data = InvoiceService::generateData($booking);
        return view('invoices.booking', $data);
    }

    /**
     * Display a commission invoice for cash bookings in a statement.
     */
    public function cashCommission(Statement $statement)
    {
        // Auth: operator owns statement, or admin
        abort_unless(
            auth()->user()->isAdmin() ||
            (auth()->user()->isOperator() && $statement->operator_id === auth()->user()->operator?->id),
            403
        );

        $data = CashCommissionService::generateInvoice($statement);

        if (!$data['has_cash']) {
            return back()->with('error', 'No cash bookings in this statement.');
        }

        return view('invoices.cash-commission', $data);
    }

    /**
     * Download / print-friendly invoice view.
     * User can use browser Print > Save as PDF.
     */
    public function download(Booking $booking)
    {
        abort_unless(
            $booking->passenger_id === auth()->id() ||
            auth()->user()->isAdmin() ||
            (auth()->user()->isOperator() && $booking->operator_id === auth()->user()->operator?->id),
            403
        );

        $data = InvoiceService::generateData($booking);
        return view('invoices.booking', $data);
    }
}
