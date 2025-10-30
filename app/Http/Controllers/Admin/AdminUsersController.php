<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AdminUsersController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('adminka/users/Index', [
            'users' => User::select(['id', 'name', 'email', 'paid_cycling_count', 'phone', 'telegram_username', 'is_coffeerider'])->get()
        ]);
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
}
