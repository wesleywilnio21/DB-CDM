<?php

namespace App\Http\Controllers;

use App\Models\BloodDonor;
use App\Models\Contact;
use App\Models\DonationSession;
use Illuminate\Http\Request;

class BloodDonorController extends Controller
{
    public function index()
    {
        $donors = BloodDonor::with(['contact', 'donationSessions'])->paginate(15);
        $allDonors = BloodDonor::with('contact')->get()->map(function($donor) {
            return [
                'id' => $donor->id,
                'name' => $donor->contact->name,
                'phone' => $donor->contact->phone,
                'type' => $donor->blood_type . $donor->rhesus,
            ];
        });
        return view('blood_donors.index', compact('donors', 'allDonors'));
    }

    public function create()
    {
        $contacts = Contact::doesntHave('bloodDonor')->get();
        return view('blood_donors.create', compact('contacts'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'contact_id' => 'required|exists:contacts,id|unique:blood_donors',
            'blood_type' => 'required|in:A,B,AB,O',
            'rhesus' => 'required|in:+,-',
            'last_donation_date' => 'nullable|date',
        ]);

        BloodDonor::create($validated);
        return redirect()->route('blood-donors.index')->with('success', 'Blood donor record created.');
    }

    public function edit(BloodDonor $bloodDonor)
    {
        return view('blood_donors.edit', compact('bloodDonor'));
    }

    public function update(Request $request, BloodDonor $bloodDonor)
    {
        $validated = $request->validate([
            'blood_type' => 'required|in:A,B,AB,O',
            'rhesus' => 'required|in:+,-',
            'last_donation_date' => 'nullable|date',
        ]);

        $bloodDonor->update($validated);
        return redirect()->route('blood-donors.index')->with('success', 'Blood donor record updated.');
    }

    public function storeWithContact(Request $request)
    {
        $validatedContact = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:255|unique:contacts',
            'email' => 'nullable|email|max:255',
            'organization' => 'nullable|string|max:255',
        ]);

        $validatedDonor = $request->validate([
            'blood_type' => 'required|in:A,B,AB,O',
            'rhesus' => 'required|in:+,-',
            'last_donation_date' => 'nullable|date',
        ]);

        \Illuminate\Support\Facades\DB::transaction(function () use ($validatedContact, $validatedDonor) {
            $contact = Contact::create($validatedContact);
            
            $validatedDonor['contact_id'] = $contact->id;
            BloodDonor::create($validatedDonor);
        });

        return redirect()->route('blood-donors.index')->with('success', 'New contact created and registered as blood donor.');
    }

    public function storeDonation(Request $request, BloodDonor $bloodDonor)
    {
        $validated = $request->validate([
            'donated_at' => 'required|date',
            'location' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $session = DonationSession::firstOrCreate(
            ['session_date' => $validated['donated_at'], 'location' => $validated['location']],
        );

        $session->donors()->syncWithoutDetaching([
            $bloodDonor->id => [
                'donated_at' => $validated['donated_at'],
                'location' => $validated['location'],
                'notes' => $validated['notes']
            ]
        ]);
        
        // Update the last_donation_date to be the most recent
        $latestDonationDate = $bloodDonor->donationSessions()->max('donated_at');
        $bloodDonor->update(['last_donation_date' => $latestDonationDate]);

        return redirect()->route('blood-donors.index')->with('success', 'Donation logged successfully.');
    }

    public function destroy(BloodDonor $bloodDonor)
    {
        $bloodDonor->delete();
        return redirect()->route('blood-donors.index')->with('success', 'Blood donor record deleted.');
    }

    public function export()
    {
        return (new \App\Exports\BloodDonorsExport())->download();
    }

    public function template()
    {
        return (new \App\Exports\BloodDonorsExport())->template();
    }

    public function import(Request $request)
    {
        $request->validate(['file' => 'required|file|mimes:csv,txt|max:5000']);
        
        $count = (new \App\Imports\BloodDonorsImport())->upload($request->file('file')->getRealPath());
        
        return redirect()->route('blood-donors.index')->with('success', "{$count} blood donors imported successfully.");
    }
}
