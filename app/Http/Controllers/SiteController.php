<?php

namespace App\Http\Controllers;

use App\Models\Site;
use Illuminate\Support\Str;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SiteController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required',
            'domain' => [
                'required',
                function (string $attribute, mixed $value, $fail) {
                    // trim the url protocol and www from the value
                    $value = preg_replace('#(^https?:\/\/)?(www\.)?|(\/$)#', '', trim($value));
                    if (Site::where('domain', $value)->exists()) {
                        $fail('This site\'s domain is already registered.');
                    }
                },
            ]
        ]);

        $site = new Site;
        $site->name = $request->name;
        $site->domain = $request->domain;
        $site->tracking_code = Str::random(32);
        $site->user_id = Auth::id();

        $site->save();

        $site->stats()->create([
            'total_visits' => 0,
            'total_visits_today' => 0,
            'total_visits_last_7_days' => 0,
            'total_uniques' => 0,
            'bounce_rate' => 0,
        ]);

        return redirect()->to('/sites/' . $site->domain);
    }

    public function destroy(Request $request, $siteDomain): RedirectResponse
    {
        $site = Site::where(['domain' => $siteDomain, 'user_id' => Auth::id()]);
        $site->delete();

        return redirect()->to('/sites');
    }

    public function changeAccess(Request $request, $siteDomain): RedirectResponse
    {
        $site = Site::where(['domain' => $siteDomain, 'user_id' => Auth::id()]);

        $site->update([
            'public_code' => $request->publicCode
        ]);

        return redirect()->to('/sites');
    }
}
