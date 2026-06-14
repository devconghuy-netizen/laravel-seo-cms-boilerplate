<?php

namespace App\Http\Controllers;

use App\Models\SiteSetting;
use Illuminate\Http\Request;

class AdminSiteSettingController extends Controller
{
    public function edit(Request $request)
    {
        abort_unless($request->user()?->hasPermission('manage-users'), 403);

        $settings = SiteSetting::values($this->defaults());

        return view('admin.settings.edit', compact('settings'));
    }

    public function update(Request $request)
    {
        abort_unless($request->user()?->hasPermission('manage-users'), 403);

        $data = $request->validate([
            'site_name' => ['required', 'string', 'max:120'],
            'site_tagline' => ['nullable', 'string', 'max:160'],
            'default_meta_description' => ['nullable', 'string', 'max:255'],
            'default_og_image' => ['nullable', 'url', 'max:255'],
            'facebook_url' => ['nullable', 'url', 'max:255'],
            'youtube_url' => ['nullable', 'url', 'max:255'],
            'tiktok_url' => ['nullable', 'url', 'max:255'],
        ]);

        SiteSetting::setMany($data);

        return redirect()
            ->route('admin.settings.edit')
            ->with('status', 'Đã lưu cấu hình website.');
    }

    private function defaults(): array
    {
        return [
            'site_name' => 'AffiPress',
            'site_tagline' => '',
            'default_meta_description' => '',
            'default_og_image' => '',
            'facebook_url' => '',
            'youtube_url' => '',
            'tiktok_url' => '',
        ];
    }
}
