<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\UpdateOrganizationSettingRequest;
use App\Models\AppSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class OrganizationSettingController extends Controller
{
    public function index(): View
    {
        $settings = AppSetting::getOrg();

        return view('settings.organization', compact('settings'));
    }

    public function update(UpdateOrganizationSettingRequest $request): RedirectResponse
    {
        foreach ($request->validated() as $key => $value) {
            AppSetting::set($key, $value);
        }

        return redirect()->route('settings.organization')->with('success', 'Organization settings updated successfully.');
    }
}
