<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\RaceStoreRequest;
use App\Models\Race;
use App\Services\ImageService;
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

    public function store(RaceStoreRequest $request, ImageService $imageService): RedirectResponse
    {
        $validated = $request->validated();

        DB::transaction(function () use ($validated, $request, $imageService) {
            $inOurStudio = (bool) $validated['in_our_studio'];
            $coverImgUrl = null;
            if ($request->hasFile('cover_image')) {
                $coverImgUrl = $imageService->save($request->file('cover_image'), 'races');
            }

            $race = Race::create([
                'name' => $validated['name'],
                'location' => $validated['location'] ?? null,
                'description' => $validated['description'] ?? null,
                'date' => $validated['date'],
                'race_types' => $validated['race_types'],
                'in_our_studio' => $inOurStudio,
                'organizer_name' => $validated['organizer_name'] ?? null,
                'organizer_website_url' => $validated['organizer_website_url'] ?? null,
                'registration_url' => $inOurStudio ? null : ($validated['registration_url'] ?? null),
                'yandex_map_url' => $validated['yandex_map_url'] ?? null,
                'cover_img_url' => $coverImgUrl,
                'price' => $validated['price'] ?? 0,
                'is_published' => $validated['is_published'] ?? false,
            ]);

            if ($race->in_our_studio) {
                foreach ($validated['clusters'] as $clusterData) {
                    $race->clusters()->create([
                        'name' => $clusterData['name'],
                        'start_time' => $clusterData['start_time'],
                        'duration_minutes' => $clusterData['duration_minutes'],
                        'price' => $clusterData['price'] ?? null,
                    ]);
                }
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

    public function update(RaceStoreRequest $request, Race $race, ImageService $imageService): RedirectResponse
    {
        $validated = $request->validated();

        DB::transaction(function () use ($validated, $race, $request, $imageService) {
            $inOurStudio = (bool) $validated['in_our_studio'];
            $coverImgUrl = $race->cover_img_url;

            if ($request->hasFile('cover_image')) {
                if ($race->cover_img_url) {
                    $imageService->delete($race->cover_img_url);
                }
                $coverImgUrl = $imageService->save($request->file('cover_image'), 'races');
            }

            $race->update([
                'name' => $validated['name'],
                'location' => $validated['location'] ?? null,
                'description' => $validated['description'] ?? null,
                'date' => $validated['date'],
                'race_types' => $validated['race_types'],
                'in_our_studio' => $inOurStudio,
                'organizer_name' => $validated['organizer_name'] ?? null,
                'organizer_website_url' => $validated['organizer_website_url'] ?? null,
                'registration_url' => $inOurStudio ? null : ($validated['registration_url'] ?? null),
                'yandex_map_url' => $validated['yandex_map_url'] ?? null,
                'cover_img_url' => $coverImgUrl,
                'price' => $validated['price'] ?? 0,
                'is_published' => $validated['is_published'] ?? false,
            ]);

            if (!$inOurStudio) {
                return;
            }

            $existingClusterIds = [];
            foreach (($validated['clusters'] ?? []) as $clusterData) {
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

    public function destroy(Race $race, ImageService $imageService): RedirectResponse
    {
        $hasRegistrations = $race->clusters()->whereHas('cyclingActivities')->exists();

        if ($hasRegistrations) {
            return back()->with('error', 'Невозможно удалить гонку с зарегистрированными участниками');
        }

        if ($race->cover_img_url) {
            $imageService->delete($race->cover_img_url);
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
