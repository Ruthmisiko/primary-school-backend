<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

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
        ]);

        $validated['school_id'] = auth()->user()->school_id;


        $setting = Setting::where('user_id', auth()->id())->first();

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
}