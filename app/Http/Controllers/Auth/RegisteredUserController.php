<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Inertia\Inertia;
use Inertia\Response;

class RegisteredUserController extends Controller
{
    /**
     * Show the registration page.
     */
    public function create(): Response
    {
        return Inertia::render('auth/Register');
    }
    
    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): mixed
    {
        $telegramRule = new TelegramUsername();
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:' . User::class,
            'telegram_username' => ['nullable', 'string', 'max:255', 'required_without:phone', $telegramRule],
            'phone' => ['nullable', 'regex:/^\+7\d{10}$/', 'max:32', 'required_without:telegram_username'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ],
            [
                'name.required' => 'Пожалуйста, укажите ваше имя.',
                'email.required' => 'Введите email.',
                'email.email' => 'Формат email некорректный.',
                'email.unique' => 'Такой email уже зарегистрирован.',
                'telegram_username.required_without' => 'Укажите Telegram или телефон.',
                'phone.required_without' => 'Укажите телефон или Telegram.',
                'password.confirmed' => 'Пароли не совпадают.',
                'password.min' => 'Пароль минимум 8 символов.',
                'password.required' => 'Придумайте пароль.',
            ]);
        
        // Получаем извлечённый никнейм из правила валидации
        $telegramUsername = $request->telegram_username
            ? $telegramRule->getExtractedUsername()
            : null;
        
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'telegram_username' => $telegramUsername,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
        ]);
        
        event(new Registered($user));
        
        Auth::login($user);
        
        return Inertia::location(route('home'));
    }
}
