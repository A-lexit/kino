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
            return asset('defaults/favicon.webp');
        }

        return app(ImageMedia::class)->url($settings->favicon);
    }

    public function favicon16(?Setting $settings): string
    {
        return $this->replaceFilename(
            $this->favicon($settings),
            'favicon-16.webp'
        );
    }

    public function favicon32(?Setting $settings): string
    {
        return $this->replaceFilename(
            $this->favicon($settings),
            'favicon-32.webp'
        );
    }

    public function appleTouchIcon(?Setting $settings): string
    {
        return $this->replaceFilename(
            $this->favicon($settings),
            'favicon-180.webp'
        );
    }

    protected function replaceFilename(
        string $url,
        string $filename
    ): string {
        return preg_replace(
            '/[^\/]+$/',
            $filename,
            $url
        );
    }

}
