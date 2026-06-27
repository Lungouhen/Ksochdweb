<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Page;
use Artesaos\SEOTools\Facades\SEOTools;
use Illuminate\Http\Request;

/**
 * PublicContentController
 * Handles public-facing content rendering with dynamic template selection
 */
class PublicContentController extends Controller
{
    /**
     * Display a single post with its selected template
     */
    public function showPost($slug)
    {
        $post = Post::with(['author', 'category', 'tags'])
            ->published()
            ->where('slug', $slug)
            ->firstOrFail();

        // Increment view count
        $post->incrementViewCount();

        // Hydrate SEO Tools dynamically
        $this->hydratePostSEO($post);

        // Resolve view based on template_layout
        $viewPath = "public.posts.{$post->template_layout}";

        // Fallback to classic-grid if custom template doesn't exist
        if (!view()->exists($viewPath)) {
            $viewPath = 'public.posts.classic-grid';
        }

        return view($viewPath, compact('post'));
    }

    /**
     * Display a single page with its selected template
     */
    public function showPage($slug)
    {
        $page = Page::where('slug', $slug)
            ->where('is_published', true)
            ->firstOrFail();

        // Hydrate SEO Tools
        $this->hydratePageSEO($page);

        // Resolve view based on template_layout
        $viewPath = "public.pages.{$page->template_layout}";

        // Fallback to minimalist-legal if custom template doesn't exist
        if (!view()->exists($viewPath)) {
            $viewPath = 'public.pages.minimalist-legal';
        }

        return view($viewPath, compact('page'));
    }

    /**
     * List all published posts (optional blog index)
     */
    public function indexPosts(Request $request)
    {
        $query = Post::with(['author', 'category'])
            ->published()
            ->latest('published_at');

        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        if ($request->filled('tag')) {
            $query->whereHas('tags', function ($q) use ($request) {
                $q->where('id', $request->tag);
            });
        }

        $posts = $query->paginate(12);
        $categories = \App\Models\Category::where('is_active', true)->get();
        $tags = \App\Models\Tag::all();

        // Set SEO for blog index
        SEOTools::setTitle('Blog');
        SEOTools::setDescription('Latest news and updates from our organization');

        return view('public.posts.index', compact('posts', 'categories', 'tags'));
    }

    /**
     * Hydrate SEO tools for a post
     */
    private function hydratePostSEO(Post $post): void
    {
        SEOTools::setTitle($post->seo['title'] ?? $post->title);
        SEOTools::setDescription($post->excerpt ?? substr(strip_tags($post->body), 0, 160));
        SEOTools::opengraph()->addProperty('type', 'article');
        SEOTools::opengraph()->addProperty('author', $post->author->name);
        
        if ($post->og_image) {
            SEOTools::opengraph()->addProperty('image', asset('storage/' . $post->og_image));
        } elseif ($post->featured_image) {
            SEOTools::opengraph()->addProperty('image', asset('storage/' . $post->featured_image));
        }
        
        SEOTools::twitter()->setType('summary_large_image');
    }

    /**
     * Hydrate SEO tools for a page
     */
    private function hydratePageSEO(Page $page): void
    {
        SEOTools::setTitle($page->seo['title'] ?? $page->title);
        SEOTools::setDescription($page->meta_description ?? substr(strip_tags($page->body), 0, 160));
        
        if ($page->og_image) {
            SEOTools::opengraph()->addProperty('image', asset('storage/' . $page->og_image));
        }
    }
}
