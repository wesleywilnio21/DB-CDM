<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Contact;
use Illuminate\Support\Facades\DB;

class ContactService
{
    /**
     * Create a new contact with phones, tags, and events.
     */
    public function createContact(array $data, ?array $phones, ?array $tags, ?array $events): Contact
    {
        return DB::transaction(function () use ($data, $phones, $tags, $events) {
            $contact = Contact::create($data);

            if ($phones !== null) {
                $phonesData = array_map(function ($phone, $index) {
                    return ['phone' => $phone, 'is_primary' => $index === 0];
                }, $phones, array_keys($phones));
                $contact->phones()->createMany($phonesData);
            }

            if ($tags !== null) {
                $contact->tags()->sync($tags);
            }

            if ($events !== null) {
                $contact->events()->sync($events);
            }

            ActivityLogger::log('created', $contact, "Created contact: {$contact->name}");

            return $contact;
        });
    }

    /**
     * Update an existing contact with its relationships.
     */
    public function updateContact(Contact $contact, array $data, ?array $phones, ?array $tags, ?array $events): Contact
    {
        return DB::transaction(function () use ($contact, $data, $phones, $tags, $events) {
            $oldData           = $contact->only(['name', 'email', 'organization']);
            $oldData['phones'] = $contact->phones->pluck('phone')->toArray();

            $contact->update($data);

            if ($phones !== null) {
                $contact->phones()->delete();
                $phonesData = array_map(function ($phone, $index) {
                    return ['phone' => $phone, 'is_primary' => $index === 0];
                }, $phones, array_keys($phones));
                $contact->phones()->createMany($phonesData);
            }

            $contact->tags()->sync($tags ?? []);
            $contact->events()->sync($events ?? []);

            $newData           = $contact->only(['name', 'email', 'organization']);
            $newData['phones'] = $phones ?? [];

            ActivityLogger::log('updated', $contact, "Updated contact: {$contact->name}", [
                'old' => $oldData,
                'new' => $newData,
            ]);

            return $contact;
        });
    }

    /**
     * Quick add a contact to an event.
     */
    public function quickAddToEvent(Contact $contact, int $eventId): void
    {
        $contact->events()->syncWithoutDetaching([$eventId]);
    }
}
