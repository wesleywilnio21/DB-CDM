<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\AddDonorToSessionRequest;
use App\Http\Requests\CreateAndAddDonorRequest;
use App\Http\Requests\StoreDonationSessionRequest;
use App\Models\BloodDonor;
use App\Models\DonationSession;
use App\Services\DonationSessionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class DonationSessionController extends Controller
{
    public function __construct(
        private readonly DonationSessionService $donationSessionService
    ) {}

    public function index(): View
    {
        $sessions = DonationSession::withCount('donors')->orderBy('session_date', 'desc')->paginate(15);

        return view('donation_sessions.index', compact('sessions'));
    }

    public function store(StoreDonationSessionRequest $request): RedirectResponse
    {
        DonationSession::create($request->validated());

        return redirect()->route('donation-sessions.index')->with('success', 'Donation session created.');
    }

    public function show(DonationSession $donationSession): View
    {
        $donationSession->load(['donors.contact' => function ($q): void {
            $q->orderBy('name');
        }]);

        $allDonors = BloodDonor::with('contact')
            ->whereNotIn('id', $donationSession->donors->pluck('id'))
            ->get()
            ->sortBy('contact.name');

        return view('donation_sessions.show', compact('donationSession', 'allDonors'));
    }

    public function addDonor(AddDonorToSessionRequest $request, DonationSession $donationSession): RedirectResponse
    {
        $donorIds = $request->validated()['donor_ids'];

        // Delegasi ke service — menghilangkan N+1 dengan BloodDonor::findMany()
        $this->donationSessionService->addDonors($donationSession, $donorIds);

        return redirect()->route('donation-sessions.show', $donationSession)
            ->with('success', count($donorIds) . ' donors added successfully.');
    }

    public function removeDonor(DonationSession $donationSession, BloodDonor $donor): RedirectResponse
    {
        $donationSession->donors()->detach($donor->id);

        // Recalculate last_donation_date
        $donor->update(['last_donation_date' => $donor->donationSessions()->max('donated_at')]);

        return back()->with('success', 'Donor removed from session.');
    }

    public function createAndAddDonor(CreateAndAddDonorRequest $request, DonationSession $donationSession): RedirectResponse
    {
        $this->donationSessionService->createAndAddDonor($donationSession, $request->validated());

        return redirect()->route('donation-sessions.show', $donationSession)
            ->with('success', 'New donor registered and added to session.');
    }

    public function destroy(DonationSession $donationSession): RedirectResponse
    {
        $donationSession->delete();

        return redirect()->route('donation-sessions.index')->with('success', 'Session deleted.');
    }
}
