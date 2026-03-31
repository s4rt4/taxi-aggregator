<?php

namespace App\Http\Controllers;

use App\Models\Booking;
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
