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

    public function updateEmailSettings(EmailSettingRequest $request)
    {
        SiteSetting::createOrFirst($request->all());
        return response()->json([
            'status' => true,
            'message' => 'Site email settings updated successfully',
            'data' => SiteSetting::first(),
        ]);
    }

}
