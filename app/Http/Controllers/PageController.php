<?php

namespace App\Http\Controllers;

use App\Models\Subscriber;
use Illuminate\Http\Request;

class PageController extends Controller
{
    public function contact()
    {
        return view('pages.contact');
    }

    public function privacy()
    {
        return view('pages.privacy');
    }

    public function terms()
    {
        return view('pages.terms');
    }

    public function subscribe(Request $request)
    {
        $data = $request->validate(['newsletterEmail' => ['required', 'email', 'max:191']]);
        Subscriber::firstOrCreate(['email' => $data['newsletterEmail']]);

        return back()->with('success', 'Terima kasih! Email kamu sudah terdaftar di newsletter.');
    }
}
