<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Media\ImageMedia;
use Illuminate\Http\Request;
use App\Constants\ImageSizes;

class SettingsController extends Controller
{
    protected $imageMedia;

    public function __construct(ImageMedia $imageMedia)
    {
        $this->imageMedia = $imageMedia;
    }

    public function index()
    {
        $settings = Setting::first();

        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $settings = Setting::firstOrNew([]);

        $settings->title = $request->input('title');
        $settings->description = $request->input('description');

        $this->handleUpload($request, $settings, 'favicon');
        $this->handleUpload($request, $settings, 'logo');

        $settings->save();

        return redirect()->back()->with('success', 'Налаштування збережено');
    }

    protected function handleUpload(Request $request, Setting $settings, string $field): void
    {
        if (!$request->hasFile($field)) {
            return;
        }

        if ($settings->{$field}) {
            $this->imageMedia->delete($settings->{$field});
        }

        if ($field === 'favicon') {
            $files = $this->imageMedia->uploadFavicon(
                $request->file('favicon'),
                'settings'
            );

            $settings->favicon = $files['original'];
        } elseif ($field === 'logo') {
            $settings->logo = $this->imageMedia->upload(
                $request->file('logo'),
                'settings',
                ImageSizes::LOGO_WIDTH,
                ImageSizes::LOGO_HEIGHT,
                'logo'
            );
        }
    }

}
