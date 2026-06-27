<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PublicContentController;

// Homepage - can be a dynamic page or static welcome
Route::get('/', [PublicContentController::class, 'indexPosts'])->name('home');

// Public content routes - posts and pages with dynamic templates
Route::get('/blog', [PublicContentController::class, 'indexPosts'])->name('public.posts.index');
Route::get('/news/{slug}', [PublicContentController::class, 'showPost'])->name('public.post.show');
Route::get('/{slug}', [PublicContentController::class, 'showPage'])->name('public.page.show');

// Include admin routes
require __DIR__.'/admin.php';
