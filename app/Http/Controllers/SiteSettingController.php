<?php

namespace App\Http\Controllers;

use App\Http\Requests\EmailSettingRequest;
use App\Models\SiteSetting;

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
        SiteSetting::updateOrCreate([], $request->all());
        $this->setNewEnv('MAIL_MAILER', $request->mail_mailer);
        $this->setNewEnv('MAIL_HOST', $request->mail_host);
        $this->setNewEnv('MAIL_PORT', $request->mail_port);
        $this->setNewEnv('MAIL_USERNAME', $request->mail_username);
        $this->setNewEnv('MAIL_PASSWORD', $request->mail_password);
        return response()->json([
            'status' => true,
            'message' => 'Site email settings updated successfully',
            'data' => SiteSetting::first(),
        ]);
    }

    /**
     * Set new environment variable
     *
     * @param string $key
     * @param string $value
     * @return void
     */
    private function setNewEnv($key, $value)
    {
        $path = base_path('.env');
        if (file_exists($path)) {
            $envLines = file($path, FILE_IGNORE_NEW_LINES);
            $found = false;
            foreach ($envLines as &$line) {
                if (str_starts_with($line, $key . '=')) {
                    $line = $key . "=" . $value;
                    $found = true;
                }
            }
            if (! $found) {
                $envLines[] = $key . "=" . $value;
            }
            file_put_contents($path, implode("\n", $envLines));
        }
    }

}
