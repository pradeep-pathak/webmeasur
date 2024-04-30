<?php

namespace App\Jobs;

use App\Models\Visit;
use App\Models\Pageview;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class SavePageviewJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public $path,
        public $duration,
        public $scrollDepth,
        public $referrer,
        public $title,
        public $ipAddress,
        public $userAgent,
        public $host,
        public $userSignature,
        public $country,
        public $countryCode,
        public $region,
        public $city,
        public $siteId,
        public $device,
        public $browser,
        public $os
    )
    {
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Use a transaction to ensure data consistency
        try {
            DB::beginTransaction();
            // Attempt to find an exisiting session
            try {
                $visit = Visit::where([
                    'user_signature' => $this->userSignature,
                    'site_id' => $this->siteId
                ])->whereDate('visited_at', '>=', Carbon::now()->subMinutes(30))->latest()->firstOrFail();
                // update the visit duration
                $visit->update([
                    'duration' => now()->diffInSeconds($visit->visited_at)
                ]);
            } catch (ModelNotFoundException $e) {
                // This is a new visit. Insert a new visit record
                $visit = new Visit([
                    'site_id' => $this->siteId,
                    'user_signature' => $this->userSignature,
                    'entry_page' => $this->path,
                    'referrer' => $this->referrer,
                    'device' => $this->device,
                    'browser' => $this->browser,
                    'os' => $this->os,
                    'country' => $this->country,
                    'country_code' => $this->countryCode,
                    'region' => $this->region,
                    'city' => $this->city,
                ]);
                $visit->save();
            }

            // Only insert the pageview if it doesn't already exists
            if (Pageview::where(['visit_id' => $visit->id, 'path' => $this->path])->doesntExist()) {
                // Create a new pageview
                $pageview = new Pageview([
                    'site_id' => $visit->site_id,
                    'path' => $this->path,
                    'title' => $this->title,
                    'duration' => $this->duration,
                    'scroll_depth' => $this->scrollDepth
                ]);

                $visit->pageviews()->save($pageview);
            }
            // Comit the database transaction
            DB::commit();
        } catch (\Exception $e) {
            // An error occurred, rollback the database transaction
            DB::rollback();

            // log the exception
            logger($e);
        }
    }
}
