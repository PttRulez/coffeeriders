<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\RaceStoreRequest;
use App\Models\Race;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class AdminRaceController extends Controller
{
    public function index(): Response
    {
        $races = Race::orderByDesc('date')->get();

        return Inertia::render('adminka/races/Index', [
            'races' => $races,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('adminka/races/Create');
    }

    public function store(RaceStoreRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        DB::transaction(function () use ($validated) {
            $race = Race::create([
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'date' => $validated['date'],
                'price' => $validated['price'],
                'is_published' => $validated['is_published'] ?? false,
            ]);

            foreach ($validated['clusters'] as $clusterData) {
                $race->clusters()->create([
                    'name' => $clusterData['name'],
                    'start_time' => $clusterData['start_time'],
                    'duration_minutes' => $clusterData['duration_minutes'],
                    'price' => $clusterData['price'] ?? null,
                ]);
            }
        });

        return to_route('adminka.races.index')
            ->with('success', 'Гонка создана');
    }

    public function edit(Race $race): Response
    {
        $race->load('clusters');

        return Inertia::render('adminka/races/Edit', [
            'race' => $race,
        ]);
    }

    public function update(RaceStoreRequest $request, Race $race): RedirectResponse
    {
        $validated = $request->validated();

        DB::transaction(function () use ($validated, $race) {
            $race->update([
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'date' => $validated['date'],
                'price' => $validated['price'],
                'is_published' => $validated['is_published'] ?? false,
            ]);

            $existingClusterIds = [];

            foreach ($validated['clusters'] as $clusterData) {
                if (!empty($clusterData['id'])) {
                    $race->clusters()->where('id', $clusterData['id'])->update([
                        'name' => $clusterData['name'],
                        'start_time' => $clusterData['start_time'],
                        'duration_minutes' => $clusterData['duration_minutes'],
                        'price' => $clusterData['price'] ?? null,
                    ]);
                    $existingClusterIds[] = $clusterData['id'];
                } else {
                    $cluster = $race->clusters()->create([
                        'name' => $clusterData['name'],
                        'start_time' => $clusterData['start_time'],
                        'duration_minutes' => $clusterData['duration_minutes'],
                        'price' => $clusterData['price'] ?? null,
                    ]);
                    $existingClusterIds[] = $cluster->id;
                }
            }

            $race->clusters()
                ->whereNotIn('id', $existingClusterIds)
                ->whereDoesntHave('cyclingActivities')
                ->delete();
        });

        return to_route('adminka.races.index')
            ->with('success', 'Гонка обновлена');
    }

    public function destroy(Race $race): RedirectResponse
    {
        $hasRegistrations = $race->clusters()->whereHas('cyclingActivities')->exists();

        if ($hasRegistrations) {
            return back()->with('error', 'Невозможно удалить гонку с зарегистрированными участниками');
        }

        $race->delete();

        return back()->with('success', 'Гонка удалена');
    }

    public function show(Race $race): Response
    {
        $race->load(['clusters.cyclingActivities.user']);

        return Inertia::render('adminka/races/Show', [
            'race' => $race,
        ]);
    }
}
