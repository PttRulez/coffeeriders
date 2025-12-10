<?php

namespace App\Http\Controllers;

use Illuminate\Validation\Rules\Enum;
use App\Enums\Pedals;
use App\Http\Controllers\Controller;
use App\Http\Resources\CyclingActivityResource;
use Hash;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\Password;
use Inertia\Inertia;
use Inertia\Response;

class UserAccountController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('user-account/Index', [
            'activities' => CyclingActivityResource::collection(Auth::user()
                ->cyclingActivities()
                ->orderByDesc('starts_at')
                ->get())->resolve(),
        ]);
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
    
    public function updateUserInfo(Request $request): RedirectResponse
    {
        $user = Auth::user();
        
        $validated = $request->validate([
            'email' => [
                'required',
                'email',
                Rule::unique('users')->ignore($user->id),
            ],
            'height' => ['sometimes', 'integer'],
            'name' => ['required', 'string', 'max:255'],
            'pedals' => ['required', new Enum(Pedals::class)],
            'phone' => [
                'nullable',
                'regex:/^\+7\d{10}$/',
                'max:32',
                'required_without:telegram_username',
                Rule::unique('users')->ignore($user->id),
            ],
            'telegram_username' => ['nullable', 'string', 'max:50'],
            'weight' => ['sometimes', 'integer'],
        ],
            [
                'name.required' => 'Пожалуйста, укажите ваше имя.',
                'email.required' => 'Введите email.',
                'email.email' => 'Формат email некорректный.',
                'email.unique' => 'Такой email уже зарегистрирован.',
                'telegram_username.required_without' => 'Укажите Telegram или телефон.',
                'phone.required_without' => 'Укажите телефон или Telegram.',
                'phone.regex' => 'Неверный формат телефона',
            ]);
        
        $user->update($validated);
        
        return back()->with('success', 'Профиль обновлён');
    }
}