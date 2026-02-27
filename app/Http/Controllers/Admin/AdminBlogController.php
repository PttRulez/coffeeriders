<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Services\ImageService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;
use function to_route;

class AdminBlogController extends Controller
{
    public function index()
    {
        return Inertia::render('adminka/blog/Index', [
            'blogs' => Blog::orderByDesc('date')->get(),
        ]);
    }
    
    public function create(): Response
    {
        return Inertia::render('adminka/blog/Create');
    }
    
    public function store(ImageService $imageService): RedirectResponse
    {
        $validated = request()->validate([
            'title' => 'required|string',
            'seo_title' => 'nullable|string|max:255',
            'seo_description' => 'nullable|string|max:500',
            'content' => 'required|string',
            'date' => 'required|date',
            'featured_image' => 'required|image|max:5120',
        ]);

        $featuredImgPath = $imageService->save(request()->file('featured_image'), 'blog');

        Blog::create([
            'title' => $validated['title'],
            'seo_title' => $validated['seo_title'] ?? null,
            'seo_description' => $validated['seo_description'] ?? null,
            'content' => $validated['content'],
            'date' => $validated['date'],
            'featured_img_path' => $featuredImgPath,
        ]);

        return to_route('adminka.blog.index')->with('success', 'Запись в блоге создана');
    }

    public function edit(Blog $blog): Response
    {
        return Inertia::render('adminka/blog/Edit', [
            'blog' => $blog,
        ]);
    }

    public function update(Blog $blog, ImageService $imageService): RedirectResponse
    {
        $validated = request()->validate([
            'title' => 'required|string',
            'seo_title' => 'nullable|string|max:255',
            'seo_description' => 'nullable|string|max:500',
            'content' => 'required|string',
            'date' => 'required|date',
            'featured_image' => 'nullable|image|max:5120',
        ]);

        $data = [
            'title' => $validated['title'],
            'seo_title' => $validated['seo_title'] ?? null,
            'seo_description' => $validated['seo_description'] ?? null,
            'content' => $validated['content'],
            'date' => $validated['date'],
        ];

        if (request()->hasFile('featured_image')) {
            if ($blog->featured_img_path) {
                $imageService->delete($blog->featured_img_path);
            }
            $data['featured_img_path'] = $imageService->save(request()->file('featured_image'), 'blog');
        }

        $blog->update($data);

        return to_route('adminka.blog.index')->with('success', 'Запись в блоге обновлена');
    }

    public function togglePublished(Blog $blog)
    {
        $blog->update([
            'is_published' => !$blog->is_published,
        ]);

        return response()->json([
            'ok' => true,
            'new_state' => $blog->is_published,
        ]);
    }
}
