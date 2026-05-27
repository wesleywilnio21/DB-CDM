<?php

namespace App\Http\Controllers;

use App\Models\DonationSession;
use App\Models\BloodDonor;
use App\Models\Contact;
use Illuminate\Http\Request;

class DonationSessionController extends Controller
{
    public function index()
    {
        $sessions = DonationSession::withCount('donors')->orderBy('session_date', 'desc')->paginate(15);
        return view('donation_sessions.index', compact('sessions'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'session_date' => 'required|date',
            'location' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        DonationSession::create($validated);
        return redirect()->route('donation-sessions.index')->with('success', 'Donation session created.');
    }

    public function show(DonationSession $donationSession)
    {
        $donationSession->load(['donors.contact' => function($q) {
            $q->orderBy('name');
        }]);
        
        $allDonors = BloodDonor::with('contact')->whereNotIn('id', $donationSession->donors->pluck('id'))->get()->sortBy('contact.name');
        
        return view('donation_sessions.show', compact('donationSession', 'allDonors'));
    }

    public function addDonor(Request $request, DonationSession $donationSession)
    {
        $request->validate([
            'donor_ids' => 'required|array',
            'donor_ids.*' => 'exists:blood_donors,id'
        ]);

        $syncData = [];
        foreach ($request->donor_ids as $donorId) {
            $syncData[$donorId] = [
                'donated_at' => $donationSession->session_date,
                'location' => $donationSession->location,
            ];
        }

        $donationSession->donors()->syncWithoutDetaching($syncData);

        // Update last_donation_date for added donors based on all their sessions
        foreach ($request->donor_ids as $donorId) {
            $donor = BloodDonor::find($donorId);
            $donor->update(['last_donation_date' => $donor->donationSessions()->max('donated_at')]);
        }

        return redirect()->route('donation-sessions.show', $donationSession)
            ->with('success', count($request->donor_ids) . ' donors added successfully.');
    }

    public function removeDonor(DonationSession $donationSession, BloodDonor $donor)
    {
        $donationSession->donors()->detach($donor->id);
        
        // Recalculate last_donation_date
        $donor->update(['last_donation_date' => $donor->donationSessions()->max('donated_at')]);
        
        return back()->with('success', "Donor removed from session.");
    }

    public function createAndAddDonor(Request $request, DonationSession $donationSession)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:255',
            'blood_type' => 'required|in:A,B,AB,O',
            'rhesus' => 'required|in:+,-'
        ]);

        \Illuminate\Support\Facades\DB::transaction(function () use ($validated, $donationSession) {
            $contact = Contact::create(['name' => $validated['name']]);
            $contact->phones()->create(['phone' => $validated['phone'], 'is_primary' => true]);
            
            $donor = BloodDonor::create([
                'contact_id' => $contact->id,
                'blood_type' => $validated['blood_type'],
                'rhesus' => $validated['rhesus'],
                'last_donation_date' => $donationSession->session_date
            ]);

            $donationSession->donors()->attach($donor->id, [
                'donated_at' => $donationSession->session_date,
                'location' => $donationSession->location
            ]);
        });

        return redirect()->route('donation-sessions.show', $donationSession)->with('success', "New donor registered and added to session.");
    }

    public function destroy(DonationSession $donationSession)
    {
        $donationSession->delete();
        return redirect()->route('donation-sessions.index')->with('success', 'Session deleted.');
    }
}
