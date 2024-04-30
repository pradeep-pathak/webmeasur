<?php

namespace App\Http\Controllers;

use App\Jobs\SavePageviewJob;
use App\Jobs\UpdateSiteStatsJob;
use App\Models\Site;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PageviewController extends Controller
{
    private function getBrowser(string $userAgent): string
    {
        $browser = "Unknown";
        $browserVersion = "Unknown";

        if (preg_match('/(MSIE|Trident)/i', $userAgent)) {
            $browser = "Internet Explorer";
            preg_match('/(?:MSIE |rv:)(\d+(\.\d+)?)/i', $userAgent, $matches);
            if (isset($matches[1])) {
                $browserVersion = $matches[1];
            }
        } elseif (preg_match('/Firefox/i', $userAgent)) {
            $browser = "Firefox";
            preg_match('/Firefox\/(\d+(\.\d+)?)/i', $userAgent, $matches);
            if (isset($matches[1])) {
                $browserVersion = $matches[1];
            }
        } elseif (preg_match('/Chrome/i', $userAgent)) {
            $browser = "Chrome";
            preg_match('/Chrome\/(\d+(\.\d+)?)/i', $userAgent, $matches);
            if (isset($matches[1])) {
                $browserVersion = $matches[1];
            }
        } elseif (preg_match('/Safari/i', $userAgent)) {
            $browser = "Safari";
            preg_match('/Version\/(\d+(\.\d+)?)/i', $userAgent, $matches);
            if (isset($matches[1])) {
                $browserVersion = $matches[1];
            }
        } elseif (preg_match('/Opera|OPR/i', $userAgent)) {
            $browser = "Opera";
            preg_match('/(?:Opera|OPR)\/(\d+(\.\d+)?)/i', $userAgent, $matches);
            if (isset($matches[1])) {
                $browserVersion = $matches[1];
            }
        }
        if ($browserVersion != 'Unknown') {
            return $browser . ' ' . $browserVersion;
        }
        return $browser;
    }

    private function getOs(string $userAgent): string
    {
        if (strpos($userAgent, 'Windows') !== false) {
            return 'Windows';
        } elseif (strpos($userAgent, 'Mac') !== false) {
            return 'Mac OS';
        } elseif (strpos($userAgent, 'Linux') !== false) {
            return 'Linux';
        } else {
            return 'Unknown';
        }
    }

    public function savePageview(Request $request): JsonResponse
    {
        // Retrieve JSON data from the request
        $jsonData = $request->json()->all();

        // Check if tracking code is provided, return error if not
        if (empty($jsonData['trackingCode'])) {
            return response()->json(['error' => 'No tracking code provided'], 400);
        }

        // Extract data from JSON
        $trackingCode = $jsonData["trackingCode"];
        $path = $jsonData["path"] ?? '/';
        $duration = $jsonData["duration"] ?? null;
        $scrollDepth = $jsonData["scrollDepth"] ?? null;
        $referrer = !empty($jsonData["referrer"]) ? $jsonData['referrer'] : 'Direct / None';
        $title = $jsonData["title"] ?? '';

        // Get IP address, User Agent, and Host from the request
        $ipAddress = $request->ip();
        $userAgent = $request->userAgent();
        $host = $request->host();

        // create an anonymous user signature based on ip and user agent
        $userSignature = hash('sha256', $ipAddress . $userAgent . $host);

        // Get location information from the IP2Location database
        $db = new \IP2Location\Database(database_path('ip2location/IP2LOCATION-LITE-DB3.IPV6.BIN'));
        $records = $db->lookup($ipAddress);
        $country = $records['countryName'] == '-' ? 'Unknown' : $records['countryName'];
        $countryCode = $records['countryCode'] == '-' ? 'Unknown' : $records['countryCode'];
        $region = $records['regionName'] == '-' ? 'Unknown' : $records['regionName'];
        $city = $records['cityName'] == '-' ? 'Unknown' : $records['cityName'];

        // Determine device type (Mobile or Desktop)
        $device = (
            str_contains($userAgent, 'Mobile') ||
            str_contains($userAgent, 'Android') ||
            str_contains($userAgent, 'iPhone') ||
            str_contains($userAgent, 'iPad') ||
            str_contains($userAgent, 'Windows Phone')
        ) ? 'Mobile' : 'Desktop';

        // Get browser and operating system from User Agent
        $browser = $this->getBrowser($userAgent);
        $os = $this->getOs($userAgent);

        // Retrieve site ID based on the provided tracking code
        $siteId = Site::where('tracking_code', $trackingCode)->value('id');

        // Return error if site ID not found
        if ($siteId === null) {
            return response()->json(['error' => 'Site not found. Please ensure that the tracking code is correct.'], 400);
        }

        // Dispatch a job to save pageview data asynchronously
        dispatch(
            new SavePageviewJob(
                $path,
                $duration,
                $scrollDepth,
                $referrer,
                $title,
                $ipAddress,
                $userAgent,
                $host,
                $userSignature,
                $country,
                $countryCode,
                $region,
                $city,
                $siteId,
                $device,
                $browser,
                $os
            )
        );
        // Dispatch a job to update stats for the site asynchronously
        dispatch(new UpdateSiteStatsJob($siteId));

        // Return success message
        return response()->json(['message' => 'Pageview recorded successfully'], 200);
    }
}
