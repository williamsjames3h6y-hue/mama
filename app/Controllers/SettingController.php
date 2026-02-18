<?php

namespace App\Controllers;

use App\Models\SiteSetting;

class SettingController extends Controller
{
    private $settingModel;

    public function __construct(SiteSetting $settingModel)
    {
        $this->settingModel = $settingModel;
    }

    public function index()
    {
        try {
            $settings = $this->settingModel->getAllSettings();
            return $this->json($settings);
        } catch (\Exception $e) {
            return $this->error('Failed to fetch settings', 500);
        }
    }

    public function update($user)
    {
        if ($user['role'] !== 'admin') {
            return $this->error('Forbidden: Admin access required', 403);
        }

        $data = $this->getJsonInput();

        try {
            $updated = [];

            foreach ($data as $key => $value) {
                $result = $this->settingModel->updateSetting($key, $value);
                if ($result) {
                    $updated[$key] = $value;
                }
            }

            return $this->json(['message' => 'Settings updated successfully', 'settings' => $updated]);
        } catch (\Exception $e) {
            return $this->error('Failed to update settings', 500);
        }
    }
}
