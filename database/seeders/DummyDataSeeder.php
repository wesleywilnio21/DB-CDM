<?php

namespace Database\Seeders;

use App\Models\Contact;
use App\Models\ContactPhone;
use App\Models\Event;
use App\Models\EventLetter;
use App\Models\Tag;
use App\Models\BloodDonor;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class DummyDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Create Tags
        $tags = collect(['Donatur Tetap', 'Relawan', 'Sponsor', 'VIP', 'Medis'])->map(function ($tagName) {
            return Tag::firstOrCreate(['name' => $tagName]);
        });

        // 2. Create Events
        $events = [
            [
                'name' => 'Donor Darah Masal Ramadhan',
                'date' => Carbon::now()->addDays(5),
                'location' => 'Aula Masjid Agung, Jakarta',
                'description' => 'Kegiatan donor darah tahunan di bulan Ramadhan untuk membantu ketersediaan stok darah PMI.'
            ],
            [
                'name' => 'Seminar Kesehatan Donor Darah',
                'date' => Carbon::now()->addDays(12),
                'location' => 'Gedung Serbaguna Sehat Jaya, Jakarta',
                'description' => 'Seminar pentingnya donor darah secara rutin bagi kesehatan tubuh.'
            ],
            [
                'name' => 'Donor Darah Rutin Triwulan',
                'date' => Carbon::now()->subDays(20),
                'location' => 'Kantor Pusat DB-CDM, Bandung',
                'description' => 'Kegiatan donor rutin setiap 3 bulan untuk karyawan dan umum.'
            ]
        ];

        $createdEvents = collect($events)->map(function ($eventData) {
            return Event::firstOrCreate(
                ['name' => $eventData['name']],
                $eventData
            );
        });

        // 3. Create Contacts and phone numbers
        $contactsData = [
            [
                'name' => 'Budi Santoso',
                'email' => 'budi.santoso@email.com',
                'address' => 'Jl. Merdeka No. 10, Jakarta',
                'organization' => 'PT. Makmur Sentosa',
                'notes' => 'Sering mendonorkan darah secara sukarela.',
                'birthdate' => '1990-05-15',
                'phones' => [
                    ['phone' => '081234567890', 'is_primary' => true],
                    ['phone' => '0217654321', 'is_primary' => false],
                ],
                'blood_type' => 'O',
                'rhesus' => '+',
                'last_donation_date' => Carbon::now()->subDays(45),
                'histories' => [
                    ['donated_at' => Carbon::now()->subDays(105), 'location' => 'PMI Jakarta Pusat', 'notes' => 'Lancar'],
                    ['donated_at' => Carbon::now()->subDays(45), 'location' => 'Aula Masjid Agung', 'notes' => 'Lancar'],
                ]
            ],
            [
                'name' => 'Siti Aminah',
                'email' => 'siti.aminah@email.com',
                'address' => 'Jl. Mawar Indah No. 4, Bandung',
                'organization' => 'Universitas Indonesia',
                'notes' => 'Relawan aktif dalam kepanitiaan.',
                'birthdate' => '1995-08-20',
                'phones' => [
                    ['phone' => '089876543210', 'is_primary' => true],
                ],
                'blood_type' => 'A',
                'rhesus' => '+',
                'last_donation_date' => Carbon::now()->subDays(15),
                'histories' => [
                    ['donated_at' => Carbon::now()->subDays(15), 'location' => 'Kantor Pusat DB-CDM', 'notes' => 'Lancar'],
                ]
            ],
            [
                'name' => 'Ahmad Hidayat',
                'email' => 'ahmad.h@email.com',
                'address' => 'Jl. Sudirman Kav. 21, Jakarta',
                'organization' => 'Bank Mandiri',
                'notes' => 'Sponsor untuk event seminar.',
                'birthdate' => '1985-12-01',
                'phones' => [
                    ['phone' => '085211223344', 'is_primary' => true],
                ],
                'blood_type' => 'B',
                'rhesus' => '-',
                'last_donation_date' => null,
                'histories' => []
            ]
        ];

        foreach ($contactsData as $data) {
            $contact = Contact::firstOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'address' => $data['address'],
                    'organization' => $data['organization'],
                    'notes' => $data['notes'],
                    'birthdate' => $data['birthdate'],
                ]
            );

            // Add phones
            foreach ($data['phones'] as $phoneData) {
                ContactPhone::firstOrCreate(
                    ['contact_id' => $contact->id, 'phone' => $phoneData['phone']],
                    ['is_primary' => $phoneData['is_primary']]
                );
            }

            // Sync tags
            if ($data['name'] == 'Budi Santoso') {
                $contact->tags()->sync([$tags[0]->id, $tags[3]->id]); // Donatur Tetap, VIP
                $contact->events()->sync([$createdEvents[0]->id, $createdEvents[2]->id]);
            } elseif ($data['name'] == 'Siti Aminah') {
                $contact->tags()->sync([$tags[1]->id]); // Relawan
                $contact->events()->sync([$createdEvents[2]->id]);
            } else {
                $contact->tags()->sync([$tags[2]->id]); // Sponsor
                $contact->events()->sync([$createdEvents[1]->id]);
            }

            // Blood donor info
            if ($data['blood_type']) {
                $donor = BloodDonor::firstOrCreate(
                    ['contact_id' => $contact->id],
                    [
                        'blood_type' => $data['blood_type'],
                        'rhesus' => $data['rhesus'],
                        'last_donation_date' => $data['last_donation_date']
                    ]
                );
            }
        }

        // 4. Create Letter Templates and Event Letters
        $template1 = \App\Models\LetterTemplate::firstOrCreate(
            ['title' => 'Template Undangan Donor Darah'],
            [
                'body' => '<p>Halo {nama},</p><p>Kami mengundang Anda untuk berpartisipasi dalam acara <strong>Donor Darah Masal Ramadhan</strong> yang akan diadakan di Aula Masjid Agung.</p><p>Kehadiran Anda sangat berarti bagi mereka yang membutuhkan.</p><p>Salam hangat,<br>DB-CDM Team</p>',
                'sig_text_above' => 'Hormat Kami,',
                'sig_name' => 'Panitia DB-CDM',
            ]
        );

        $template2 = \App\Models\LetterTemplate::firstOrCreate(
            ['title' => 'Template Sponsorship'],
            [
                'body' => '<p>Kepada Yth. Bapak/Ibu {nama},</p><p>Sehubungan dengan pelaksanaan acara <strong>Seminar Kesehatan Donor Darah</strong>, kami bermaksud mengajukan permohonan kerjasama sponsorship.</p><p>Terlampir proposal detail penawaran kerjasama ini.</p><p>Hormat kami,<br>DB-CDM Team</p>',
                'sig_text_above' => 'Hormat Kami,',
                'sig_name' => 'Ketua Panitia',
            ]
        );

        $lettersData = [
            [
                'event_id' => $createdEvents[0]->id,
                'template_id' => $template1->id,
                'recipient_name' => 'Budi Santoso',
                'recipient_phone' => '081234567890',
            ],
            [
                'event_id' => $createdEvents[1]->id,
                'template_id' => $template2->id,
                'recipient_name' => 'Ahmad Hidayat',
                'recipient_phone' => '085211223344',
            ]
        ];

        foreach ($lettersData as $letterData) {
            $template = \App\Models\LetterTemplate::find($letterData['template_id']);
            $existing = EventLetter::where('event_id', $letterData['event_id'])
                                   ->where('recipient_name', $letterData['recipient_name'])
                                   ->first();
            if (!$existing) {
                $event = Event::find($letterData['event_id']);
                $generated = EventLetter::generateForEvent($event);
                
                EventLetter::create([
                    'event_id' => $event->id,
                    'title' => $template->title,
                    'recipient_name' => $letterData['recipient_name'],
                    'recipient_phone' => $letterData['recipient_phone'],
                    'body' => str_replace('{nama}', $letterData['recipient_name'], $template->body),
                    'issued_at' => now(),
                    'city' => 'Jakarta',
                    'letter_sequence' => $generated['sequence'],
                    'letter_number' => $generated['letter_number'],
                    'sig_text_above' => $template->sig_text_above,
                    'sig_name' => $template->sig_name,
                ]);
            }
        }
    }
}
