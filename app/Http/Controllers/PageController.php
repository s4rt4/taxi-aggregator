<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PageController extends Controller
{
    public function privacyPolicy()
    {
        return view('pages.privacy-policy');
    }

    public function termsOfService()
    {
        return view('pages.terms-of-service');
    }

    public function cookiePolicy()
    {
        return view('pages.cookie-policy');
    }

    public function about()
    {
        return view('pages.about');
    }

    public function howItWorks()
    {
        return view('pages.how-it-works');
    }

    public function forOperators()
    {
        return view('pages.for-operators');
    }

    public function contact()
    {
        return view('pages.contact');
    }
}
