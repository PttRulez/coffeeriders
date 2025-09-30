<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Hash;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\Password;
use Inertia\Inertia;
use Inertia\Response;

class UserAccountController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('user-account/Index');
    }
    
    public function updatePassword(Request $request): RedirectResponse
    {
        $request->validate([
            'new_password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);
        
        $request->user()->update([
            'password' => Hash::make($request->new_password),
        ]);
        
        return back()->with('success', 'Пароль обновлён');
    }
}