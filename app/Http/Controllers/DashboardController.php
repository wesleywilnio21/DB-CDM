<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\Event;
use App\Models\BloodDonor;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $totalContacts = Contact::count();
        $totalEvents = Event::count();
        $totalDonors = BloodDonor::count();
        
        $eligibleDonors = BloodDonor::get()->filter(function ($donor) {
            return $donor->next_eligible_date && $donor->next_eligible_date->isPast();
        })->count();

        $recentContacts = Contact::latest()->take(5)->get();
        $upcomingEvents = Event::where('date', '>=', today())->orderBy('date')->take(3)->get();

        return view('dashboard', compact(
            'totalContacts', 'totalEvents', 'totalDonors', 'eligibleDonors',
            'recentContacts', 'upcomingEvents'
        ));
    }
}
