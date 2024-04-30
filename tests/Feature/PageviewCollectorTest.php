<?php

namespace Tests\Feature;

use App\Jobs\SavePageviewJob;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
// use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PageviewCollectorTest extends TestCase
{
    use RefreshDatabase;

    public function test_that_pageview_gets_collected(): void
    {
        $this->withoutExceptionHandling();

        $user = User::factory()->create(['country' => 'India', 'region' => 'Maharashtra', 'city' => 'Mumbai']);

        $site = $user->sites()->create([
            'name' => "Test Website",
            'domain' => "laraveltest.test",
            "user_id" => $user->id,
            "tracking_code" => Str::random(32),
        ]);

        Queue::fake();

        $data = [
            "trackingCode" => $site->tracking_code,
            "path" => '/',
            'title' => 'Home Page',
            'duration' => 4000,
            'scrollDepth' => 45,
            "referrer" => '',
        ];
        $response = $this->postJson("/api/collect-pageview", $data);
        $response->assertSuccessful();

        Queue::assertPushed(SavePageviewJob::class);

        $data = [
            "trackingCode" => $site->tracking_code,
            "path" => '/pricing',
            'title' => 'Our Pricing',
            'duration' => 6000,
            'scrollDepth' => 65,
            "referrer" => '',
        ];
        $this->postJson("/api/collect-pageview", $data);
        $this->postJson("/api/collect-pageview", $data);

    }
}
