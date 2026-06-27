<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Campaign;
use App\Models\Donation;
use App\Models\RecurringDonation;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;

class CampaignController extends Controller
{
    /**
     * Display a listing of campaigns.
     */
    public function index(): View
    {
        $campaigns = Campaign::with('creator')
            ->latest()
            ->paginate(15);

        return view('admin.campaigns.index', compact('campaigns'));
    }

    /**
     * Show the form for creating a new campaign.
     */
    public function create(): View
    {
        return view('admin.campaigns.create');
    }

    /**
     * Store a newly created campaign.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|unique:campaigns,slug',
            'description' => 'required|string',
            'goal_amount' => 'required|numeric|min:0',
            'current_amount' => 'nullable|numeric|min:0',
            'currency' => 'required|string|max:3',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after:start_date',
            'featured_image' => 'nullable|image|max:2048',
            'status' => 'required|in:draft,active,completed,cancelled',
            'priority' => 'nullable|integer|min:1|max:10',
        ]);

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['title']);
        }

        $validated['creator_id'] = auth()->id();

        if ($request->hasFile('featured_image')) {
            $path = $request->file('featured_image')->store('campaigns', 'public');
            $validated['featured_image'] = $path;
        }

        Campaign::create($validated);

        return redirect()->route('admin.campaigns.index')
            ->with('success', 'Campaign created successfully.');
    }

    /**
     * Display the specified campaign.
     */
    public function show(Campaign $campaign): View
    {
        $campaign->load(['creator', 'donations']);

        return view('admin.campaigns.show', compact('campaign'));
    }

    /**
     * Show the form for editing the specified campaign.
     */
    public function edit(Campaign $campaign): View
    {
        return view('admin.campaigns.edit', compact('campaign'));
    }

    /**
     * Update the specified campaign.
     */
    public function update(Request $request, Campaign $campaign): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|unique:campaigns,slug,' . $campaign->id,
            'description' => 'required|string',
            'goal_amount' => 'required|numeric|min:0',
            'current_amount' => 'nullable|numeric|min:0',
            'currency' => 'required|string|max:3',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after:start_date',
            'featured_image' => 'nullable|image|max:2048',
            'status' => 'required|in:draft,active,completed,cancelled',
            'priority' => 'nullable|integer|min:1|max:10',
        ]);

        if ($request->hasFile('featured_image')) {
            $path = $request->file('featured_image')->store('campaigns', 'public');
            $validated['featured_image'] = $path;
        }

        $campaign->update($validated);

        return redirect()->route('admin.campaigns.index')
            ->with('success', 'Campaign updated successfully.');
    }

    /**
     * Remove the specified campaign.
     */
    public function destroy(Campaign $campaign): RedirectResponse
    {
        $campaign->delete();

        return redirect()->route('admin.campaigns.index')
            ->with('success', 'Campaign deleted successfully.');
    }

    /**
     * Display all donations.
     */
    public function donations(): View
    {
        $donations = Donation::with(['donor', 'campaign'])
            ->latest()
            ->paginate(20);

        return view('admin.campaigns.donations.index', compact('donations'));
    }

    /**
     * Display recurring donations.
     */
    public function recurringDonations(): View
    {
        $recurringDonations = RecurringDonation::with(['donor', 'campaign'])
            ->latest()
            ->paginate(20);

        return view('admin.campaigns.donations.recurring', compact('recurringDonations'));
    }
}
