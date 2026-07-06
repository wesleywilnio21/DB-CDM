<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\BloodDonor;
use App\Models\Contact;
use App\Models\DonationSession;
use Illuminate\Support\Facades\DB;

class DonationSessionService
{
    public function __construct(
        private readonly BloodDonorService $bloodDonorService
    ) {}

    /**
     * Tambahkan banyak donor ke sesi donasi dan update last_donation_date mereka.
     */
    public function addDonors(DonationSession $session, array $donorIds): void
    {
        $syncData = [];
        foreach ($donorIds as $donorId) {
            $syncData[$donorId] = [
                'donated_at' => $session->session_date,
                'location' => $session->location,
            ];
        }

        $session->donors()->syncWithoutDetaching($syncData);

        // Ambil semua donor sekaligus (hindari N+1)
        $donors = BloodDonor::findMany($donorIds);
        foreach ($donors as $donor) {
            $this->bloodDonorService->updateLastDonationDate($donor);
        }
    }

    /**
     * Buat donor baru (beserta contact & phone) lalu tambahkan ke sesi donasi.
     */
    public function createAndAddDonor(DonationSession $session, array $data): BloodDonor
    {
        return DB::transaction(function () use ($session, $data): BloodDonor {
            $contact = Contact::create(['name' => $data['name']]);
            $contact->phones()->create(['phone' => $data['phone'], 'is_primary' => true]);

            $donor = BloodDonor::create([
                'contact_id' => $contact->id,
                'blood_type' => $data['blood_type'],
                'rhesus' => $data['rhesus'],
                'last_donation_date' => $session->session_date,
            ]);

            $session->donors()->attach($donor->id, [
                'donated_at' => $session->session_date,
                'location' => $session->location,
            ]);

            return $donor;
        });
    }
}
