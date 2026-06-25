<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Services\AuditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminSettingController extends Controller
{
    /**
     * Settings views page.
     */
    public function index()
    {
        return view('admin.settings');
    }

    /**
     * AJAX: Get all settings grouped.
     */
    public function getSettings()
    {
        $settings = Setting::all()->groupBy('setting_group');
        return response()->json($settings);
    }

    /**
     * AJAX: Update multiple settings at once.
     */
    public function updateSettings(Request $request)
    {
        $request->validate([
            'settings' => 'required|array'
        ]);

        $settingsToUpdate = $request->input('settings');
        $oldValues = [];
        $newValues = [];

        foreach ($settingsToUpdate as $key => $value) {
            $oldSetting = Setting::where('setting_key', $key)->first();
            $oldValues[$key] = $oldSetting ? $oldSetting->setting_value : null;

            // Handle checkbox / boolean settings sent as true/false
            if ($value === true || $value === 'true') {
                $value = '1';
            } elseif ($value === false || $value === 'false') {
                $value = '0';
            }

            Setting::set($key, $value);
            $newValues[$key] = $value;
        }

        AuditService::log(
            Auth::id(),
            'update_settings',
            'Setting',
            null,
            $oldValues,
            $newValues
        );

        return response()->json([
            'success' => true,
            'message' => 'Settings updated successfully.'
        ]);
    }
}
