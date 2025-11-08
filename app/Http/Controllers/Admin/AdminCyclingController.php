<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CyclingActivity;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Response;
use Inertia\Inertia;

class AdminCyclingController extends Controller
{
    
    public function index(Request $request): Response
    {
        $date = today()->toDateString();
        
        if ($request->filled('date')) {
            try {
                $date = Carbon::parse($request->input('date'))->toDateString();
            } catch (\Exception $e) {
                // если дата невалидная — остаётся today()
            }
        }
        
        return Inertia::render('adminka/cycling-studio/Index', [
            'activities' => CyclingActivity::with(['couponUsage', 'cyclingStation', 'user'])
                ->whereDate('starts_at', $date)
                ->orderBy('starts_at')
                ->get(),
        ]);
    }
    
    public function update(Request $request, CyclingActivity $cyclingActivity): RedirectResponse
    {
        $data = $request->validate([
            'distance' => 'required|numeric|min:0',
        ]);
        
        $cyclingActivity->update($data);
        
        return back()->with('success', 'Дистанция обновлена');
    }
    
    public function destroy(CyclingActivity $cyclingActivity): RedirectResponse
    {
        $cyclingActivity->delete();
        
        return back()->with('success', 'Тренировка удалена');
    }
}
