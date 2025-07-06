<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use function to_route;

class AdminController extends Controller
{
    public function index(): RedirectResponse
    {
        return to_route('adminka.rent-bikes.index');
    }
}
