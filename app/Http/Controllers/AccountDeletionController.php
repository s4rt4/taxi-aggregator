<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AccountDeletionController extends Controller
{
    /**
     * Show the account deletion confirmation page.
     */
    public function request()
    {
        return view('passenger.delete-account');
    }

    /**
     * Process account deletion (GDPR-compliant).
     * Soft-deletes the user and anonymises personal data.
     */
    public function destroy(Request $request)
    {
        $request->validate([
            'password' => 'required|current_password',
        ]);

        $user = auth()->user();
        Auth::logout();

        // Anonymise personal data while retaining booking history for legal compliance
        $user->update([
            'name' => 'Deleted User',
            'email' => 'deleted_' . $user->id . '@deleted.com',
            'phone' => null,
            'is_active' => false,
        ]);
        $user->delete(); // soft delete

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('success', 'Your account has been deleted.');
    }
}
