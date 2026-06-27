<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\MembershipController;
use App\Http\Controllers\Admin\PostController;
use App\Http\Controllers\Admin\PageController;
use App\Http\Controllers\Admin\CampaignController;
use App\Http\Controllers\Admin\EventController;
use App\Http\Controllers\Admin\SettingController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/

Route::prefix('admin')->name('admin.')->middleware(['auth', 'verified'])->group(function () {
    
    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    
    // Users & Roles Management
    Route::prefix('users')->name('users.')->controller(UserController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/create', 'create')->name('create');
        Route::post('/', 'store')->name('store');
        Route::get('/{user}', 'show')->name('show');
        Route::get('/{user}/edit', 'edit')->name('edit');
        Route::put('/{user}', 'update')->name('update');
        Route::delete('/{user}', 'destroy')->name('destroy');
        Route::post('/{user}/toggle-active', 'toggleActive')->name('toggle-active');
        
        // Roles
        Route::get('/roles', 'roles')->name('roles.index');
        Route::get('/roles/create', 'createRole')->name('roles.create');
        Route::post('/roles', 'storeRole')->name('roles.store');
    });
    
    // Membership Management
    Route::prefix('memberships')->name('memberships.')->controller(MembershipController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/applications', 'applications')->name('applications');
        Route::post('/applications/{application}/approve', 'approveApplication')->name('applications.approve');
        Route::post('/applications/{application}/reject', 'rejectApplication')->name('applications.reject');
    });
    
    // CMS - Posts
    Route::prefix('posts')->name('posts.')->controller(PostController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/create', 'create')->name('create');
        Route::post('/', 'store')->name('store');
        Route::get('/{post}', 'show')->name('show');
        Route::get('/{post}/edit', 'edit')->name('edit');
        Route::put('/{post}', 'update')->name('update');
        Route::delete('/{post}', 'destroy')->name('destroy');
    });
    
    // CMS - Pages
    Route::prefix('pages')->name('pages.')->controller(PageController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/create', 'create')->name('create');
        Route::post('/', 'store')->name('store');
        Route::get('/{page}', 'show')->name('show');
        Route::get('/{page}/edit', 'edit')->name('edit');
        Route::put('/{page}', 'update')->name('update');
        Route::delete('/{page}', 'destroy')->name('destroy');
    });
    
    // Campaigns & Donations
    Route::prefix('campaigns')->name('campaigns.')->controller(CampaignController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/create', 'create')->name('create');
        Route::post('/', 'store')->name('store');
        Route::get('/{campaign}', 'show')->name('show');
        Route::get('/{campaign}/edit', 'edit')->name('edit');
        Route::put('/{campaign}', 'update')->name('update');
        Route::delete('/{campaign}', 'destroy')->name('destroy');
        
        // Donations
        Route::get('/donations', 'donations')->name('donations.index');
        Route::get('/donations/recurring', 'recurringDonations')->name('donations.recurring');
    });
    
    // Events & Volunteers
    Route::prefix('events')->name('events.')->controller(EventController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/create', 'create')->name('create');
        Route::post('/', 'store')->name('store');
        Route::get('/{event}', 'show')->name('show');
        Route::get('/{event}/edit', 'edit')->name('edit');
        Route::put('/{event}', 'update')->name('update');
        Route::delete('/{event}', 'destroy')->name('destroy');
        
        // Registrations
        Route::get('/{event}/registrations', 'registrations')->name('registrations.index');
    });
    
    // Volunteers
    Route::prefix('volunteers')->name('volunteers.')->controller(EventController::class)->group(function () {
        Route::get('/opportunities', 'volunteerOpportunities')->name('opportunities.index');
        Route::get('/applications', 'volunteerApplications')->name('applications.index');
        Route::post('/applications/{application}/approve', 'approveVolunteerApplication')->name('applications.approve');
        Route::get('/hours', 'volunteerHours')->name('hours.index');
    });
    
    // Settings
    Route::prefix('settings')->name('settings.')->controller(SettingController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('/', 'update')->name('update');
        
        // Contact Messages
        Route::get('/contacts', 'contacts')->name('contacts.index');
        Route::post('/contacts/{message}/read', 'markContactAsRead')->name('contacts.mark-read');
        Route::delete('/contacts/{message}', 'deleteContact')->name('contacts.delete');
        
        // Newsletters
        Route::get('/newsletters', 'newsletters')->name('newsletters.index');
        Route::get('/newsletters/create', 'createNewsletter')->name('newsletters.create');
        Route::post('/newsletters', 'storeNewsletter')->name('newsletters.store');
        Route::post('/newsletters/{newsletter}/send', 'sendNewsletter')->name('newsletters.send');
        
        // Subscribers
        Route::get('/subscribers', 'subscribers')->name('subscribers.index');
        Route::post('/subscribers', 'addSubscriber')->name('subscribers.add');
        Route::delete('/subscribers/{subscriber}', 'removeSubscriber')->name('subscribers.remove');
        Route::get('/subscribers/export', 'exportSubscribers')->name('subscribers.export');
    });
});
