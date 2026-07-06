<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\BloodDonor;
use App\Models\Contact;
use App\Models\Event;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $totalContacts = Contact::count();
        $totalEvents = Event::count();
        $totalDonors = BloodDonor::count();

        // Menggunakan scopeEligible (query DB) menggantikan get()->filter() di PHP
        $eligibleDonors = BloodDonor::eligible()->count();

        $recentContacts = Contact::latest()->take(5)->get();
        $upcomingEvents = Event::where('date', '>=', today())->orderBy('date')->take(3)->get();

        return view('dashboard', compact(
            'totalContacts', 'totalEvents', 'totalDonors', 'eligibleDonors',
            'recentContacts', 'upcomingEvents'
        ));
    }
}
