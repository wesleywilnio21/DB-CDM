<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Contact;
use App\Models\Event;

class EventService
{
    /**
     * Tambahkan banyak contact ke event dengan guest count masing-masing.
     */
    public function addContacts(Event $event, array $syncData): void
    {
        $event->contacts()->syncWithoutDetaching($syncData);
    }

    /**
     * Buat Contact baru (beserta phone) dan langsung tambahkan ke Event.
     */
    public function createAndAddContact(Event $event, array $data): Contact
    {
        $contact = Contact::create([
            'name'         => $data['name'],
            'email'        => $data['email'] ?? null,
            'organization' => $data['organization'] ?? null,
        ]);

        $contact->phones()->create([
            'phone'      => $data['phone'],
            'is_primary' => true,
        ]);

        $event->contacts()->attach($contact->id, [
            'guest_count' => $data['guest_count'] ?? 0,
        ]);

        return $contact;
    }
}
