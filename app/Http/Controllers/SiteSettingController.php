<?php

namespace App\Http\Controllers;

use App\Http\Requests\EmailSettingRequest;
use App\Models\SiteSetting;
use Illuminate\Http\Request;

class SiteSettingController extends Controller
{
    public function index()
    {
        return response()->json([
            'status' => true,
            'message' => 'Site settings fetched successfully',
            'data' => SiteSetting::first(),
        ]);
    }

    public function updateEmailSettings(Request $request)
    {
        $siteSetting = SiteSetting::first();
        $siteSetting->update($request->all());
        return response()->json([
            'status' => true,
            'message' => 'Site email settings updated successfully',
            'data' => $siteSetting,
        ]);
    }

}
