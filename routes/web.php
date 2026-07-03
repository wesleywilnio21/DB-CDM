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

    Route::resource('tags', App\Http\Controllers\TagController::class)->only(['store', 'destroy']);
    Route::resource('contacts', ContactController::class);
    Route::post('contacts/{contact}/add-event', [ContactController::class, 'quickAddToEvent'])->name('contacts.add-event');

    Route::resource('events', EventController::class);
    Route::post('events/{event}/add-contact', [EventController::class, 'addContact'])->name('events.add-contact');

    Route::post('blood-donors/{bloodDonor}/donate', [BloodDonorController::class, 'storeDonation'])->name('blood-donors.donate');
    Route::post('blood-donors/store-with-contact', [BloodDonorController::class, 'storeWithContact'])->name('blood-donors.store-with-contact');
    Route::resource('blood-donors', BloodDonorController::class);

    // Excel & Activity Log
    Route::get('/contacts-export', [ContactController::class, 'export'])->name('contacts.export');
    Route::get('/contacts-template', [ContactController::class, 'downloadTemplate'])->name('contacts.template');
    Route::post('/contacts-import', [ContactController::class, 'import'])->name('contacts.import');
    Route::get('/activity-logs', [ActivityLogController::class, 'index'])->name('activity-logs.index');
    Route::resource('users', UserController::class)->except(['create', 'edit', 'show']);
    Route::resource('roles', \App\Http\Controllers\RoleController::class)->except(['create', 'show']);

    // Surat
    Route::get('letter-templates/{letterTemplate}/variables', [\App\Http\Controllers\LetterTemplateController::class, 'getVariables'])->name('letter-templates.variables');
    Route::resource('letter-templates', \App\Http\Controllers\LetterTemplateController::class);
    Route::get('letter-documents/{letterDocument}/print', [\App\Http\Controllers\LetterDocumentController::class, 'print'])->name('letter-documents.print');
    Route::get('letter-documents/{letterDocument}/envelope', [\App\Http\Controllers\LetterDocumentController::class, 'envelope'])->name('letter-documents.envelope');
    Route::resource('letter-documents', \App\Http\Controllers\LetterDocumentController::class);
});

require __DIR__ . '/auth.php';
