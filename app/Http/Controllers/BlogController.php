<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use Inertia\Inertia;
use Inertia\Response;

class BlogController extends Controller
{
    public function index(): Response
    {
        $blogs = Blog::where('is_published', true)
            ->orderByDesc('date')
            ->get();

        return Inertia::render('blog/Index', [
            'blogs' => $blogs,
        ]);
    }

    public function show(Blog $blog): Response
    {
        abort_unless($blog->is_published, 404);

        return Inertia::render('blog/Show', [
            'blog' => $blog,
        ]);
    }
}