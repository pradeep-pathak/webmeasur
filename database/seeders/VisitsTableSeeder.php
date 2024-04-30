<?php

namespace Database\Seeders;

use App\Models\Visit;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class VisitsTableSeeder extends Seeder
{
    public function run(): void
    {
        $site_id = DB::table('sites')->pluck('id')->first();
        $userSignatures = ['eoirfjeorijf', 'ioerhfoierfjio', 'iojfeorijfer', 'oierfjeoirfj', 'oeirfjeroifj', 'ioerfjorijfrtg', 'ioejrfioefes', 'oierufroea', 'eriofjerofji', 'oierjfoerjiotig', 'oekeriofpoekr', 'ioewjiowejd', 'ioerfiuriurf', 'oirjtgiorjgtr'];
        $userSignatures = [];
        for ($i = 0; $i < 85; $i++) {
            $userSignatures[] = (string) \Illuminate\Support\Str::uuid();
        }
        $countries = ['India', 'United States', 'Canada', 'United Kingdom', 'Australia'];
        $countryCodes = ['in', 'us', 'ca', 'gb', 'au'];
        $regions = ['Maharashtra', 'California', 'Ontario', 'England', 'New South Wales'];
        $cities = ['Mumbai', 'Los Angeles', 'Toronto', 'London', 'Sydney'];
        $referrers = ['https://google.com', 'https://facebook.com', 'https://twitter.com', 'Direct Visit'];
        $dates = [now()->today()];
        for ($i = 0; $i < 60; $i++) {
            $dates[] = now()->subDays($i);
        }
        foreach (range(1, 100) as $i) {
            $country = array_rand($countries);
            $date = array_rand($dates);
            $visit = Visit::create([
                'site_id' => $site_id,
                'user_signature' => $userSignatures[array_rand($userSignatures)],
                'entry_page' => '/',
                'duration' => 107,
                'referrer' => $referrers[array_rand($referrers)],
                'device' => 'Desktop',
                'browser' => 'Chrome 121',
                'os' => 'Windows',
                'country' => $countries[$country],
                'country_code' => $countryCodes[$country],
                'region' => $regions[$country],
                'city' => $cities[$country],
                'visited_at' => $dates[$date]
            ]);

            $pageviewsArray = [
                [['path' => '/', 'site_id' => $site_id, 'visit_id' => $visit->id, 'viewed_at' => $visit->visited_at]],
                [['path' => '/', 'site_id' => $site_id, 'visit_id' => $visit->id, 'viewed_at' => $visit->visited_at], ['path' => '/pricing', 'site_id' => $site_id, 'visit_id' => $visit->id, 'viewed_at' => $visit->visited_at]],
                [['path' => '/', 'site_id' => $site_id, 'visit_id' => $visit->id, 'viewed_at' => $visit->visited_at], ['path' => '/pricing', 'site_id' => $site_id, 'visit_id' => $visit->id, 'viewed_at' => $visit->visited_at], ['path' => '/sign-up', 'site_id' => $site_id, 'visit_id' => $visit->id, 'viewed_at' => $visit->visited_at]]
            ];
            $visit->pageviews()->createMany($pageviewsArray[array_rand($pageviewsArray)]);

            dispatch(new \App\Jobs\UpdateSiteStatsJob($visit->site_id));
        }
    }
}
