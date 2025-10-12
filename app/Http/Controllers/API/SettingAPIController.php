<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class SettingAPIController extends Controller
{
    public function index(): JsonResponse
    {
        $setting = Setting::where('user_id', auth()->id())->first();

        if (!$setting) {
            return response()->json([
                'success' => false,
                'message' => 'Settings not found for this user.',
                'data' => null,
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $setting,
            'message' => 'Settings retrieved successfully.',
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'business_name' => 'required|string|max:255',
            'business_email' => 'required|email|max:255',
            'business_phone' => 'required|string|max:20',
            'location' => 'nullable|string|max:255',
            'school_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $validated['school_id'] = auth()->user()->school_id;

        $setting = Setting::where('user_id', auth()->id())->first();

        // Handle logo upload
        if ($request->hasFile('school_logo')) {
            // Delete old logo if exists
            if ($setting && $setting->school_logo) {
                Storage::disk('public')->delete($setting->school_logo);
            }

            // Store new logo
            $logoPath = $request->file('school_logo')->store('logos', 'public');
            $validated['school_logo'] = $logoPath;
        }

        if ($setting) {
            $setting->update(array_merge($validated, ['user_id' => auth()->id()]));
        } else {
            $setting = Setting::create(array_merge($validated, ['user_id' => auth()->id()]));
        }

        return response()->json([
            'success' => true,
            'message' => 'Settings saved successfully.',
            'data' => $setting,
        ]);
    }

    /**
     * Get school logo for current user
     */
    public function getLogo(): JsonResponse
    {
        $setting = Setting::where('school_id', auth()->user()->school_id)->first();

        if (!$setting || !$setting->school_logo) {
            return response()->json([
                'success' => false,
                'message' => 'Logo not found.',
                'data' => null,
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'logo_url' => Storage::url($setting->school_logo),
                'logo_path' => $setting->school_logo,
            ],
            'message' => 'Logo retrieved successfully.',
        ]);
    }
}