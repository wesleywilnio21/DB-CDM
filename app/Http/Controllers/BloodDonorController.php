<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Exports\BloodDonorsExport;
use App\Http\Requests\ImportFileRequest;
use App\Http\Requests\StoreBloodDonorRequest;
use App\Http\Requests\StoreBloodDonorWithContactRequest;
use App\Http\Requests\StoreDonationRequest;
use App\Http\Requests\UpdateBloodDonorRequest;
use App\Models\BloodDonor;
use App\Models\Contact;
use App\Models\DonationSession;
use App\Services\BloodDonorService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class BloodDonorController extends Controller
{
    public function __construct(
        private readonly BloodDonorService $bloodDonorService
    ) {}

    public function index(): View
    {
        $donors    = BloodDonor::with(['contact', 'donationSessions'])->paginate(15);
        $allDonors = BloodDonor::with('contact')->get()->map(fn (BloodDonor $donor) => [
            'id'    => $donor->id,
            'name'  => $donor->contact->name,
            'phone' => $donor->contact->phone,
            'type'  => $donor->blood_type . $donor->rhesus,
        ]);

        return view('blood_donors.index', compact('donors', 'allDonors'));
    }

    public function create(): View
    {
        $contacts = Contact::doesntHave('bloodDonor')->get();

        return view('blood_donors.create', compact('contacts'));
    }

    public function store(StoreBloodDonorRequest $request): RedirectResponse
    {
        BloodDonor::create($request->validated());

        return redirect()->route('blood-donors.index')->with('success', 'Blood donor record created.');
    }

    public function edit(BloodDonor $bloodDonor): View
    {
        return view('blood_donors.edit', compact('bloodDonor'));
    }

    public function update(UpdateBloodDonorRequest $request, BloodDonor $bloodDonor): RedirectResponse
    {
        $bloodDonor->update($request->validated());

        return redirect()->route('blood-donors.index')->with('success', 'Blood donor record updated.');
    }

    public function storeWithContact(StoreBloodDonorWithContactRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $contactData = array_intersect_key($data, array_flip(['name', 'phone', 'email', 'organization']));
        $donorData   = array_intersect_key($data, array_flip(['blood_type', 'rhesus', 'last_donation_date']));

        $this->bloodDonorService->createWithContact($contactData, $donorData);

        return redirect()->route('blood-donors.index')->with('success', 'New contact created and registered as blood donor.');
    }

    public function storeDonation(StoreDonationRequest $request, BloodDonor $bloodDonor): RedirectResponse
    {
        $data = $request->validated();

        $session = DonationSession::firstOrCreate(
            ['session_date' => $data['donated_at'], 'location' => $data['location'] ?? null],
        );

        $this->bloodDonorService->logDonation($bloodDonor, $data, $session);

        return redirect()->route('blood-donors.index')->with('success', 'Donation logged successfully.');
    }

    public function destroy(BloodDonor $bloodDonor): RedirectResponse
    {
        $bloodDonor->delete();

        return redirect()->route('blood-donors.index')->with('success', 'Blood donor record deleted.');
    }

    public function export(): \Symfony\Component\HttpFoundation\StreamedResponse|\Illuminate\Http\Response
    {
        return (new BloodDonorsExport)->download();
    }

    public function template(): \Symfony\Component\HttpFoundation\StreamedResponse|\Illuminate\Http\Response
    {
        return (new BloodDonorsExport)->template();
    }

    public function import(ImportFileRequest $request): RedirectResponse
    {
        $count = (new \App\Imports\BloodDonorsImport)->upload($request->file('file')->getRealPath());

        return redirect()->route('blood-donors.index')->with('success', "{$count} blood donors imported successfully.");
    }
}
