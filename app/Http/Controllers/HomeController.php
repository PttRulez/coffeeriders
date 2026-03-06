<?php

namespace App\Http\Controllers;

use App\Models\Race;
use Inertia\Inertia;

class HomeController extends Controller
{
    public function __invoke() {
        $races = Race::published()
            ->inOurStudio()
            ->upcoming()
            ->orderBy('date')
            ->get();
        
        return Inertia::render('Home', ['ourIndoorRaces' => $races]);
    }
}