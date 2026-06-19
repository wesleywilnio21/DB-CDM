<?php

namespace App\Http\Controllers;

use App\Models\AppSetting;
use Illuminate\Http\Request;

class OrganizationSettingController extends Controller
{
    public function index()
    {
        $settings = AppSetting::getOrg();

        return view('settings.organization', compact('settings'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'org_name' => 'required|string|max:255',
            'org_address' => 'required|string|max:500',
            'org_phone' => 'required|string|max:255',
            'org_tagline' => 'nullable|string|max:255',
            'org_city_default' => 'required|string|max:255',
        ]);

        foreach ($validated as $key => $value) {
            AppSetting::set($key, $value);
        }

        return redirect()->route('settings.organization')->with('success', 'Organization settings updated successfully.');
    }
}
