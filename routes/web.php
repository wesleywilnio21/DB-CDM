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
});

require __DIR__ . '/auth.php';
