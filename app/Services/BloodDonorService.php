<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\BloodDonor;
use App\Models\Contact;
use App\Models\DonationSession;
use Illuminate\Support\Facades\DB;

class BloodDonorService
{
    /**
     * Buat Contact baru dan langsung daftarkan sebagai BloodDonor dalam satu transaksi.
     */
    public function createWithContact(array $contactData, array $donorData): BloodDonor
    {
        return DB::transaction(function () use ($contactData, $donorData): BloodDonor {
            $phone = $contactData['phone'] ?? null;
            unset($contactData['phone']);

            $contact = Contact::create($contactData);

            if ($phone) {
                $contact->phones()->create([
                    'phone' => $phone,
                    'is_primary' => true,
                ]);
            }

            $donorData['contact_id'] = $contact->id;

            return BloodDonor::create($donorData);
        });
    }

    /**
     * Catat donasi baru untuk donor dan update last_donation_date.
     */
    public function logDonation(BloodDonor $donor, array $data, DonationSession $session): void
    {
        $session->donors()->syncWithoutDetaching([
            $donor->id => [
                'donated_at' => $data['donated_at'],
                'location' => $data['location'] ?? null,
                'notes' => $data['notes'] ?? null,
            ],
        ]);

        $this->updateLastDonationDate($donor);
    }

    /**
     * Hitung ulang last_donation_date dari semua sesi donasi donor.
     */
    public function updateLastDonationDate(BloodDonor $donor): void
    {
        $latestDate = $donor->donationSessions()->max('donated_at');
        $donor->update(['last_donation_date' => $latestDate]);
    }
}
