<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\View\View;

class PasswordResetLinkController extends Controller
{
    /**
     * Show the "forgot password" page.
     */
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    /**
     * Send a password reset link to the given email address.
     *
     * The response is the same whether or not the address belongs to an account,
     * so the form cannot be used to discover which emails are registered.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        Password::sendResetLink($request->only('email'));

        return back()->with('status', __('If an account exists for that email, a reset link is on its way.'));
    }
}
