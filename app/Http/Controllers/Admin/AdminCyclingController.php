<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CyclingActivity;
use App\Models\CyclingStation;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
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
    
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'cycling_station_ids' => 'required|array|min:1',
            'starts_at' => 'required|date',
            'ends_at' => 'sometimes|date|after:starts_at',
            'user_id' => 'required|exists:users,id',
        ], [
            'cycling_station_id.required' => 'Выберите станцию.',
            'starts_at.required' => 'Укажите время начала.',
        ]);
        
        $start = Carbon::parse($request->input('starts_at'));
        
        if ($request->input('ends_at')) {
            $end = Carbon::parse($request->input('ends_at'));
        } else {
            $end = $start->copy()->addHours(2);
        }
        
        $hasOverlaps = CyclingActivity::whereIn('cycling_station_id', $validated['cycling_station_ids'])
            ->overlaps($start, $end)
            ->exists();
        
        if ($hasOverlaps) {
            throw ValidationException::withMessages([
                'starts_at' => 'Есть байки занятые на выбранное время'
            ]);
        }
        
        $now = now();
        
        $rows = collect($validated['cycling_station_ids'])->map(fn ($id) => [
            'user_id' => $validated['user_id'],
            'cycling_station_id' => $id,
            'starts_at' => $start,
            'ends_at' => $end,
            'created_at' => $now,
            'updated_at' => $now,
        ])->toArray();
        
        CyclingActivity::insert($rows);
        
        return redirect()
            ->route('adminka.cycling-studio.index')
            ->with('success', 'Успешно забронировано.');
    }
    
    public function create(): Response
    {
        return Inertia::render('adminka/cycling-studio/Create');
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
    
    public function bikeCheck(Request $request): JsonResponse
    {
        $start = Carbon::parse($request->input('startDatetime'));
        if ($request->input('endDatetime')) {
            $end = Carbon::parse($request->input('endDatetime'));
        } else {
            $end = $start->copy()->addHours(2);
        }
        
        $stations = CyclingStation::all();
        
        $busyStationIds = CyclingActivity::where(function ($q) use ($start, $end) {
            $q->where('starts_at', '<', $end)
                ->where('ends_at', '>', $start);
        })
            ->pluck('cycling_station_id')
            ->unique();
        
        $freeStations = $stations->whereNotIn('id', $busyStationIds);
        
        return response()->json([
            'stations' => $freeStations->values(),
        ]);
    }
}
