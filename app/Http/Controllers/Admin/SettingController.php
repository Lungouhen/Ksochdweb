<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\ContactMessage;
use App\Models\Newsletter;
use App\Models\Subscriber;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class SettingController extends Controller
{
    /**
     * Display general settings.
     */
    public function index(): View
    {
        $settings = Setting::all()->groupBy('group');

        return view('admin.settings.index', compact('settings'));
    }

    /**
     * Update general settings.
     */
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'settings' => 'nullable|array',
            'settings.*.key' => 'required|string',
            'settings.*.value' => 'nullable',
        ]);

        if (isset($validated['settings'])) {
            foreach ($validated['settings'] as $settingData) {
                Setting::where('key', $settingData['key'])->update(['value' => $settingData['value']]);
            }
        }

        return redirect()->route('admin.settings.index')
            ->with('success', 'Settings updated successfully.');
    }

    /**
     * Display contact messages.
     */
    public function contacts(): View
    {
        $messages = ContactMessage::latest()
            ->paginate(20);

        return view('admin.settings.contacts.index', compact('messages'));
    }

    /**
     * Mark a contact message as read.
     */
    public function markContactAsRead(ContactMessage $message): RedirectResponse
    {
        $message->markAsRead();

        return redirect()->route('admin.settings.contacts')
            ->with('success', 'Message marked as read.');
    }

    /**
     * Delete a contact message.
     */
    public function deleteContact(ContactMessage $message): RedirectResponse
    {
        $message->delete();

        return redirect()->route('admin.settings.contacts')
            ->with('success', 'Message deleted successfully.');
    }

    /**
     * Display newsletter management.
     */
    public function newsletters(): View
    {
        $newsletters = Newsletter::with('creator')
            ->latest()
            ->paginate(15);

        return view('admin.settings.newsletters.index', compact('newsletters'));
    }

    /**
     * Show the form for creating a new newsletter.
     */
    public function createNewsletter(): View
    {
        return view('admin.settings.newsletters.create');
    }

    /**
     * Store a new newsletter.
     */
    public function storeNewsletter(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'subject' => 'required|string|max:255',
            'content' => 'required|string',
            'send_at' => 'nullable|date|after:now',
        ]);

        $validated['creator_id'] = auth()->id();
        $validated['status'] = 'draft';

        Newsletter::create($validated);

        return redirect()->route('admin.settings.newsletters.index')
            ->with('success', 'Newsletter created successfully.');
    }

    /**
     * Send a newsletter.
     */
    public function sendNewsletter(Newsletter $newsletter): RedirectResponse
    {
        $newsletter->send();

        return redirect()->route('admin.settings.newsletters.index')
            ->with('success', 'Newsletter sent successfully.');
    }

    /**
     * Display subscriber list.
     */
    public function subscribers(): View
    {
        $subscribers = Subscriber::latest()
            ->paginate(20);

        return view('admin.settings.subscribers.index', compact('subscribers'));
    }

    /**
     * Add a new subscriber.
     */
    public function addSubscriber(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'email' => 'required|email|unique:subscribers,email',
            'name' => 'nullable|string|max:255',
        ]);

        Subscriber::create($validated);

        return redirect()->route('admin.settings.subscribers.index')
            ->with('success', 'Subscriber added successfully.');
    }

    /**
     * Remove a subscriber.
     */
    public function removeSubscriber(Subscriber $subscriber): RedirectResponse
    {
        $subscriber->delete();

        return redirect()->route('admin.settings.subscribers.index')
            ->with('success', 'Subscriber removed successfully.');
    }

    /**
     * Export subscribers to CSV.
     */
    public function exportSubscribers()
    {
        $subscribers = Subscriber::all();

        $csv = "ID,Email,Name,Subscribed At,Status\n";
        foreach ($subscribers as $subscriber) {
            $csv .= "{$subscriber->id},{$subscriber->email},{$subscriber->name},{$subscriber->created_at},{$subscriber->is_active}\n";
        }

        return response($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="subscribers.csv"',
        ]);
    }
}
