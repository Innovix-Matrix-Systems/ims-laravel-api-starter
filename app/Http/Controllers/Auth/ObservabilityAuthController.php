<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class ObservabilityAuthController extends Controller
{
    public function showLogin()
    {
        return view('observability.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $email = config('observability.auth.email');
        $password = config('observability.auth.password');

        if ($request->input('email') === $email && $request->input('password') === $password) {
            session(['observability_authed' => true, 'observability_email' => $request->input('email')]);

            if ($request->boolean('remember')) {
                cookie()->queue(cookie('observability_remember', $request->input('email'), 60 * 24 * 30));
            }

            return Redirect::intended(url('observability'));
        }

        return Redirect::back()->withErrors(['email' => 'Invalid credentials'])->withInput();
    }

    public function logout(Request $request)
    {
        $request->session()->forget(['observability_authed', 'observability_email']);
        cookie()->queue(cookie()->forget('observability_remember'));

        return redirect()->to(url('observability-auth/login'));
    }

    public function dashboard()
    {
        return view('observability.dashboard');
    }
}
