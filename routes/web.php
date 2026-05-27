<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\BloodDonorController;
use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\UserController;

Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/settings/organization', [\App\Http\Controllers\OrganizationSettingController::class, 'index'])->name('settings.organization');
    Route::post('/settings/organization', [\App\Http\Controllers\OrganizationSettingController::class, 'update'])->name('settings.organization.update');

    Route::resource('letter-assets', \App\Http\Controllers\LetterAssetController::class)->only(['index', 'store', 'destroy']);

    Route::resource('tags', App\Http\Controllers\TagController::class)->only(['store', 'destroy']);
    Route::resource('contacts', ContactController::class);
    Route::post('contacts/{contact}/add-event', [ContactController::class, 'quickAddToEvent'])->name('contacts.add-event');
    
    Route::resource('events', EventController::class);
    Route::post('events/{event}/add-contact', [EventController::class, 'addContact'])->name('events.add-contact');
    Route::post('events/{event}/create-contact', [EventController::class, 'createAndAddContact'])->name('events.create-contact');
    Route::delete('events/{event}/remove-contact/{contact}', [EventController::class, 'removeContact'])->name('events.remove-contact');
    Route::patch('events/{event}/participants/{contact}/guest-count', [EventController::class, 'updateGuestCount'])->name('events.update-guest-count');
    
    Route::get('events/{event}/letters/bulk-generate', [\App\Http\Controllers\EventLetterController::class, 'bulkGenerate'])->name('letters.bulk-generate');
    Route::post('events/{event}/letters/bulk-store', [\App\Http\Controllers\EventLetterController::class, 'bulkStore'])->name('letters.bulk-store');
    Route::resource('events/{event}/letters', \App\Http\Controllers\EventLetterController::class)->except(['show', 'create', 'store']);
    Route::get('letters/{letter}/pdf', [\App\Http\Controllers\EventLetterController::class, 'exportPdf'])->name('letters.pdf');
    
    Route::resource('letter-templates', \App\Http\Controllers\LetterTemplateController::class)->except(['show']);
    
    Route::post('blood-donors/{bloodDonor}/donate', [BloodDonorController::class, 'storeDonation'])->name('blood-donors.donate');
    Route::post('blood-donors/store-with-contact', [BloodDonorController::class, 'storeWithContact'])->name('blood-donors.store-with-contact');
    Route::resource('blood-donors', BloodDonorController::class);

    Route::resource('donation-sessions', \App\Http\Controllers\DonationSessionController::class);
    Route::post('donation-sessions/{donation_session}/add-donor', [\App\Http\Controllers\DonationSessionController::class, 'addDonor'])->name('donation-sessions.add-donor');
    Route::post('donation-sessions/{donation_session}/create-donor', [\App\Http\Controllers\DonationSessionController::class, 'createAndAddDonor'])->name('donation-sessions.create-donor');
    Route::delete('donation-sessions/{donation_session}/remove-donor/{donor}', [\App\Http\Controllers\DonationSessionController::class, 'removeDonor'])->name('donation-sessions.remove-donor');

    // Excel & Activity Log
    Route::get('/contacts-export', [ContactController::class, 'export'])->name('contacts.export');
    Route::get('/contacts-template', [ContactController::class, 'downloadTemplate'])->name('contacts.template');
    Route::post('/contacts-import', [ContactController::class, 'import'])->name('contacts.import');
    
    Route::get('/blood-donors-export', [BloodDonorController::class, 'export'])->name('blood-donors.export');
    Route::get('/blood-donors-template', [BloodDonorController::class, 'template'])->name('blood-donors.template');
    Route::post('/blood-donors-import', [BloodDonorController::class, 'import'])->name('blood-donors.import');

    Route::get('/activity-logs', [ActivityLogController::class, 'index'])->name('activity-logs.index');
    Route::resource('users', UserController::class)->except(['create', 'edit', 'show']);
});

require __DIR__ . '/auth.php';
