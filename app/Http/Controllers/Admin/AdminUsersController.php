<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Pedals;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Enum;
use Inertia\Inertia;
use Inertia\Response;
use function to_route;

class AdminUsersController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('adminka/users/Index', [
            'users' => User::select([
                'id',
                'name',
                'email',
                'paid_cycling_count',
                'phone',
                'telegram_username',
                'is_coffeerider',
                'is_mechanic',
            ])->get()
        ]);
    }
    
    public function edit(User $user): Response
    {
        return Inertia::render('adminka/users/Edit', [
            'user' => $user
        ]);
    }
    
    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'height' => ['sometimes', 'integer'],
            'pedals' => ['required', new Enum(Pedals::class)],
            'weight' => ['sometimes', 'integer'],
            'is_mechanic' => ['required', 'boolean'],
        ]);
        
        $user->update($validated);
        
        return to_route('adminka.users.index')->with('success', 'Данные пользователя обновлены');
    }
    
    public function updateCyclingActivitiesCount(Request $request, User $user)
    {
        $validated = $request->validate([
            'paid_cycling_count' => ['required', 'integer', 'min:0'],
        ]);
        
        $user->update($validated);
        
        return back()->with('success', 'Количество тренировок обновлено ');
    }
    
    public function updateIsCoffeeRider(Request $request, User $user)
    {
        $validated = $request->validate([
            'is_coffeerider' => ['required', 'boolean'],
        ]);
        
        $user->update($validated);
        
        return back()->with('success', 'Заапдейчено');
    }

    public function updateIsMechanic(Request $request, User $user)
    {
        $validated = $request->validate([
            'is_mechanic' => ['required', 'boolean'],
        ]);

        $user->update($validated);

        return back()->with('success', 'Статус механика обновлён');
    }
}
