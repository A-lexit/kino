<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Media\ImageMedia;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function __construct(
        protected ImageMedia $imageMedia
    ) {}

    public function index()
    {
        $this->authorize('viewAny', Setting::class);

        $settings = Setting::first();

        return view(
            'admin.settings.index',
            compact('settings')
        );
    }

    public function update(Request $request)
    {
        $settings = Setting::firstOrNew([]);

        $this->authorize('update', $settings);

        $settings->title = $request->input('title');
        $settings->description = $request->input('description');

        if ($request->hasFile('favicon')) {

            $oldFavicon = $settings->favicon;

            $settings->favicon = $this->imageMedia->uploadFavicon(
                $request->file('favicon'),
                'settings'
            )['original'];

            if ($oldFavicon) {
                $this->imageMedia->deleteFavicon($oldFavicon);
            }

        } elseif ($request->boolean('delete_favicon')) {

            $this->imageMedia->deleteFavicon(
                $settings->favicon
            );

            $settings->favicon = null;
        }


        if ($request->hasFile('logo')) {

            $oldLogo = $settings->logo;

            $settings->logo = $this->imageMedia->uploadLogo(
                $request->file('logo'),
                'settings'
            );

            if ($oldLogo) {
                $this->imageMedia->deleteLogo($oldLogo);
            }

        } elseif ($request->boolean('delete_logo')) {

            $this->imageMedia->deleteLogo(
                $settings->logo
            );

            $settings->logo = null;
        }

        $settings->save();

        return redirect()
            ->back()
            ->with('success', 'Налаштування збережено');
    }
}
