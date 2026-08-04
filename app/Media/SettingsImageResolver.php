<?php

namespace App\Media;

use App\Models\Setting;

class SettingsImageResolver
{
    public function logo(?Setting $settings): string
    {
        if (!$settings || empty($settings->logo)) {
            return asset('defaults/logo.webp');
        }

        return app(ImageMedia::class)->url($settings->logo);
    }

    public function favicon(?Setting $settings): string
    {
        if (!$settings || empty($settings->favicon)) {
            return asset('default_favicon.ico');
        }

        return app(ImageMedia::class)->url($settings->favicon);
    }

    public function favicon16(?Setting $settings): string
    {
        return $this->replaceSuffix($this->favicon($settings), '_16');
    }

    public function favicon32(?Setting $settings): string
    {
        return $this->replaceSuffix($this->favicon($settings), '_32');
    }

    public function appleTouchIcon(?Setting $settings): string
    {
        return $this->replaceSuffix($this->favicon($settings), '_180');
    }

    protected function replaceSuffix(string $url, string $suffix): string
    {
        return preg_replace('/\.webp$/i', $suffix . '.webp', $url);
    }

}
