<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Membership;
use App\Models\MembershipApplication;
use App\Models\MembershipType;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class MembershipController extends Controller
{
    /**
     * Display a listing of memberships.
     */
    public function index(): View
    {
        $memberships = Membership::with(['user', 'membershipType'])
            ->latest()
            ->paginate(15);

        return view('admin.memberships.index', compact('memberships'));
    }

    /**
     * Show the form for creating a new membership type.
     */
    public function createType(): View
    {
        return view('admin.memberships.types.create');
    }

    /**
     * Store a newly created membership type.
     */
    public function storeType(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|unique:membership_types,slug',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'currency' => 'required|string|max:3',
            'duration_months' => 'required|integer|min:1',
            'benefits' => 'nullable|array',
            'is_active' => 'boolean',
        ]);

        MembershipType::create($validated);

        return redirect()->route('admin.memberships.types.index')
            ->with('success', 'Membership type created successfully.');
    }

    /**
     * Display pending membership applications.
     */
    public function applications(): View
    {
        $applications = MembershipApplication::with(['user', 'membershipType'])
            ->pending()
            ->latest()
            ->paginate(15);

        return view('admin.memberships.applications.index', compact('applications'));
    }

    /**
     * Approve a membership application.
     */
    public function approveApplication(MembershipApplication $application): RedirectResponse
    {
        $application->approve();

        return redirect()->route('admin.memberships.applications')
            ->with('success', 'Membership application approved successfully.');
    }

    /**
     * Reject a membership application.
     */
    public function rejectApplication(MembershipApplication $application, Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'rejection_reason' => 'nullable|string|max:1000',
        ]);

        $application->reject($validated['rejection_reason'] ?? null);

        return redirect()->route('admin.memberships.applications')
            ->with('success', 'Membership application rejected.');
    }
}
