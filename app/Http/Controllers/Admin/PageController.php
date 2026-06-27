<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;

class PageController extends Controller
{
    /**
     * Display a listing of pages.
     */
    public function index(): View
    {
        $pages = Page::with('author')
            ->latest()
            ->paginate(15);

        return view('admin.pages.index', compact('pages'));
    }

    /**
     * Show the form for creating a new page.
     */
    public function create(): View
    {
        $templates = Page::getAvailableTemplates();
        
        return view('admin.pages.create', compact('templates'));
    }

    /**
     * Store a newly created page.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|unique:pages,slug',
            'body' => 'required|string',
            'excerpt' => 'nullable|string|max:500',
            'template_layout' => 'required|in:minimalist-legal,classic-grid,editorial-news,donation-campaign,event-hub',
            'featured_image' => 'nullable|image|max:2048',
            'og_image' => 'nullable|image|max:2048',
            'is_published' => 'boolean',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'is_home' => 'boolean',
        ]);

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['title']);
        }

        $validated['created_by'] = auth()->id();

        if ($validated['is_home'] ?? false) {
            Page::where('is_home', true)->update(['is_home' => false]);
        }

        // Handle featured image upload
        if ($request->hasFile('featured_image')) {
            $path = $request->file('featured_image')->store('pages', 'public');
            $validated['featured_image'] = $path;
        }

        // Handle OG image upload
        if ($request->hasFile('og_image')) {
            $path = $request->file('og_image')->store('pages/og', 'public');
            $validated['og_image'] = $path;
        }

        Page::create($validated);

        return redirect()->route('admin.pages.index')
            ->with('success', 'Page created successfully.');
    }

    /**
     * Display the specified page.
     */
    public function show(Page $page): View
    {
        $page->load('author');

        return view('admin.pages.show', compact('page'));
    }

    /**
     * Show the form for editing the specified page.
     */
    public function edit(Page $page): View
    {
        $templates = Page::getAvailableTemplates();
        
        return view('admin.pages.edit', compact('page', 'templates'));
    }

    /**
     * Update the specified page.
     */
    public function update(Request $request, Page $page): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|unique:pages,slug,' . $page->id,
            'body' => 'required|string',
            'excerpt' => 'nullable|string|max:500',
            'template_layout' => 'required|in:minimalist-legal,classic-grid,editorial-news,donation-campaign,event-hub',
            'featured_image' => 'nullable|image|max:2048',
            'og_image' => 'nullable|image|max:2048',
            'is_published' => 'boolean',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'is_home' => 'boolean',
        ]);

        if ($validated['is_home'] ?? false) {
            Page::where('is_home', true)->where('id', '!=', $page->id)->update(['is_home' => false]);
        }

        $validated['updated_by'] = auth()->id();

        // Handle featured image upload
        if ($request->hasFile('featured_image')) {
            if ($page->featured_image) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($page->featured_image);
            }
            $path = $request->file('featured_image')->store('pages', 'public');
            $validated['featured_image'] = $path;
        }

        // Handle OG image upload
        if ($request->hasFile('og_image')) {
            if ($page->og_image) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($page->og_image);
            }
            $path = $request->file('og_image')->store('pages/og', 'public');
            $validated['og_image'] = $path;
        }

        $page->update($validated);

        return redirect()->route('admin.pages.index')
            ->with('success', 'Page updated successfully.');
    }

    /**
     * Remove the specified page.
     */
    public function destroy(Page $page): RedirectResponse
    {
        if ($page->is_home) {
            return redirect()->route('admin.pages.index')
                ->with('error', 'Cannot delete the home page.');
        }

        $page->delete();

        return redirect()->route('admin.pages.index')
            ->with('success', 'Page deleted successfully.');
    }
}
