<?php

namespace App\Http\Controllers;

use App\Models\CyclingActivity;
use App\Models\CyclingStation;
use App\Services\AdminTelegram;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class CyclingStudioController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('cycling-studio/Index');
    }
    
    public function booking(): Response
    {
        return Inertia::render('cycling-studio/Booking');
    }
    
    public function storeBooking(Request $request, AdminTelegram $adminTelegram): RedirectResponse
    {
        $validated = $request->validate([
            'cycling_station_id' => ['required', 'exists:cycling_stations,id'],
            'starts_at' => ['required', 'date'],
        ], [
            'cycling_station_id.required' => 'Выберите станцию.',
            'starts_at.required' => 'Укажите время начала.',
        ]);
        
        $startsAt = Carbon::parse($validated['starts_at']);
        $endsAt = (clone $startsAt)->addHours(2);
        
        $hasConflict = CyclingActivity::where('cycling_station_id', $validated['cycling_station_id'])
            ->where(function ($q) use ($startsAt, $endsAt) {
                $q->where('starts_at', '<', $endsAt)
                    ->where('ends_at', '>', $startsAt);
            })
            ->exists();
        
        if ($hasConflict) {
            throw ValidationException::withMessages([
                'starts_at' => 'Станция уже занята в выбранный интервал.',
            ]);
        }
        
        $activity = CyclingActivity::create([
            'user_id' => auth()->user()->id,
            'cycling_station_id' => $validated['cycling_station_id'],
            'starts_at' => $startsAt,
            'ends_at' => $endsAt,
        ]);
        
        $adminTelegram->sendStudioBookingNotification($activity);
        
        return redirect()
            ->route('cycling-studio.index')
            ->with('success', 'Занятие успешно забронировано.');
    }
    
    public function bikeCheck(Request $request): JsonResponse
    {
        $start = Carbon::parse($request->input('datetime'));
        $end = $start->copy()->addHours(2);
        
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