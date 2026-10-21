<?php

namespace App\Http\Controllers;

use App\Models\ContactMessage;
use App\Models\NewsletterSubscriber;
use App\Models\SiteSetting;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function show()
    {
        $contactEmail = SiteSetting::get('contact_email', 'info@apnikheti.org');
        $contactPhone = SiteSetting::get('contact_phone', '+91 98765 43210');
        $contactAddress = SiteSetting::get('contact_address', 'Punjab, India');
        $facebookUrl = SiteSetting::get('facebook_url');
        $youtubeUrl = SiteSetting::get('youtube_url');

        return view('contact', compact(
            'contactEmail',
            'contactPhone',
            'contactAddress',
            'facebookUrl',
            'youtubeUrl'
        ));
    }

    public function submitMessage(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:50',
            'subject' => 'nullable|string|max:255',
            'message' => 'required|string',
            'consent' => 'accepted',
        ]);

        ContactMessage::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'subject' => $validated['subject'] ?? 'General Inquiry',
            'message' => $validated['message'],
        ]);

        return redirect()->back()->with('success', 'Thank you! Your message has been sent successfully.');
    }

    public function subscribeNewsletter(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email|max:255',
        ]);

        NewsletterSubscriber::firstOrCreate(
            ['email' => $validated['email']],
            ['is_active' => true]
        );

        return redirect()->back()->with('success', 'Thank you for subscribing to our agricultural newsletter!');
    }
}
