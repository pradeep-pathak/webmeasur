<?php

namespace App\Jobs;

use App\Models\Pageview;
use App\Models\Visit;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class UpdateSiteStatsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public $siteId
    )
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // get total visits
        $visits = Visit::where('site_id', $this->siteId);
        $totalVisits = $visits->count();

        // get total visits count today
        $totalVisitsToday = $visits->whereBetween('visited_at', [Carbon::now()->startOfDay(), Carbon::now()->endOfDay()])->count();

        // get total visits count in last 7 days
        $totalVisitsLast7Days = Visit::where('site_id', $this->siteId)
            ->whereBetween('visited_at', [Carbon::now()->subDays(6)->startOfDay(), Carbon::now()->endOfDay()])
            ->count();

        // calculate bounce rate
        $singlePageVisits = Visit::where('site_id', $this->siteId)->has('pageviews', '=', 1);

        $bounceRate = $totalVisits > 0 ? ($singlePageVisits->count() / $totalVisits) * 100 : 0;

        // get total unique visitors
        $totalUniques = Visit::where('site_id', $this->siteId)
            ->distinct('user_signature')
            ->count();

        // insert all data into the site_stats table
        DB::table('site_stats')
            ->where('site_id', $this->siteId)
            ->update([
                'total_visits' => $totalVisits,
                'total_visits_today' => $totalVisitsToday,
                'total_visits_last_7_days' => $totalVisitsLast7Days,
                'total_uniques' => $totalUniques,
                'bounce_rate' => $bounceRate,
            ]);
    }
}
