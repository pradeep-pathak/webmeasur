<?php

namespace App\Http\Controllers;

use App\Models\Visit;
use App\Models\Pageview;
use App\Models\Site;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();

        $sites = $user->sites()->with('stats')->get();

        return view('dashboard.index', compact('sites'));
    }


    public function show(Request $request, $domain, $publicCode = null): View|RedirectResponse
    {
        $user = Auth::user();
        $site = null;
        if ($user === null) {
            if ($publicCode != null) {
                $site = Site::where(['public_code' => $publicCode, 'domain' => $domain])->withCount('visits')->firstOrFail();
            } else {
                return redirect('/login');
            }
        } else {
            $site = $user->sites()->where('domain', $domain)->withCount('visits')->firstOrFail();
        }

        $activePeriod = $request->input('period', 'last_7_days');

        $visits = $this->getVisitsByPeriod($site->id, $activePeriod)->orderBy('visited_at', 'desc');
        $totalVisitsCount = $visits->count();

        if ($totalVisitsCount > 0) {
            $data = $this->fetchSiteStats($site, $activePeriod, $totalVisitsCount);
        } else {
            $data = [
                'site' => $site,
                'activePeriod' => $activePeriod,
                'totalVisitsCount' => 0,
                'uniqueVisitors' => 0,
                'bounceRate' => 0,
                'avgVisitDuration' => 0,
                'referrerCounts' => collect(),
                'locationCounts' => collect(),
                'topPages' => collect()
            ];
        }

        if ($site->visits_count > 0) {
            $data['visits'] = $visits->with('pageviews')->paginate(10);
            if ($request->input('page') != 1 && $request->input('page', 1) > ceil($data['visits']->total() / 10)) {
                return redirect()->to(addGetParams('sites.show', ['domain' => $domain, 'page' => 1]));
            }
        }

        return view('dashboard.show', $data);
    }

    private function fetchSiteStats($site, $activePeriod, $totalVisitsCount)
    {
        $cacheKey = 'site_' . $site->id . '_data_' . $activePeriod;

        $siteStats = Cache::remember(
            $cacheKey,
            now()->addSeconds(10),
            function () use ($site, $activePeriod, $totalVisitsCount) {
                return $this->calculateSiteStats($site, $activePeriod, $totalVisitsCount);
            }
        );

        return $siteStats;
    }

    private function calculateSiteStats($site, $activePeriod, $totalVisitsCount)
    {
        $visits = $this->getVisitsByPeriod($site->id, $activePeriod)->orderBy('visited_at', 'desc');
        $data = [
            'site' => $site,
            'activePeriod' => $activePeriod,
            'totalVisitsCount' => $totalVisitsCount,
            'uniqueVisitors' => $visits->distinct('user_signature')->count(),
            'locationCounts' => $this->getLocationCounts($activePeriod),
            'referrerCounts' => $this->getReferrerCounts($site->id, $activePeriod),
            'topPages' => $this->getTopPagesByPeriod($site->id, $activePeriod),
            'bounceRate' => $this->calculateBounceRate($site->id, $totalVisitsCount, $activePeriod),
            'avgVisitDuration' => $this->getVisitsByPeriod($site->id, $activePeriod)->avg('duration')
        ];

        if ($activePeriod != 'today' && $totalVisitsCount > 0) {
            $chartData = $this->getChartDataForActivePeriod($activePeriod, $site->id);
            $data['chartData'] = $chartData['data'];
            $data['chartLabels'] = $chartData['labels'];
        }

        return $data;
    }

    private function getVisitsByPeriod($siteId, $activePeriod)
    {
        $startDate = Carbon::now()->startOfDay();
        $endDate = Carbon::now()->endOfDay();

        $visits = Visit::where('site_id', $siteId);

        switch ($activePeriod) {
            case 'today':
                $visits->whereBetween('visited_at', [$startDate, $endDate]);
            case 'last_7_days':
                $startDate = Carbon::now()->subDays(6)->startOfDay();
                $visits->whereBetween('visited_at', [$startDate, $endDate]);
                break;
            case 'last_30_days':
                $startDate = Carbon::now()->subDays(29)->startOfDay();
                $visits->whereBetween('visited_at', [$startDate, $endDate]);
                break;
        }

        return $visits;
    }

    private function getLocationCounts($activePeriod)
    {
        $startDate = Carbon::now()->startOfDay();
        $endDate = Carbon::now()->endOfDay();
        $locationCounts = Visit::select('city', 'country_code', DB::raw('count(*) as count'))->orderBy('count', 'desc')->limit(10);

        switch ($activePeriod) {
            case 'today':
                $locationCounts->whereBetween('visited_at', [$startDate, $endDate]);
                break;
            case 'last_7_days':
                $startDate = Carbon::now()->subDays(6)->startOfDay();
                $locationCounts = $locationCounts->whereBetween('visited_at', [$startDate, $endDate]);
                break;
            case 'last_30_days':
                $startDate = Carbon::now()->subDays(29)->startOfDay();
                $locationCounts = $locationCounts->whereBetween('visited_at', [$startDate, $endDate]);
                break;
        }

        return $locationCounts->groupBy('city')
            ->get()
            ->mapWithKeys(function ($visit) {
                return [$visit->city => [
                    'count' => $visit->count,
                    'country_code' => $visit->country_code
                ]];
            });
    }

    private function getReferrerCounts($siteId, $activePeriod)
    {
        $visits = $this->getVisitsByPeriod($siteId, $activePeriod);

        return $visits->select('referrer', DB::raw('count(*) as count'))
            ->groupBy('referrer')
            ->orderByDesc('count')
            ->limit(5)
            ->get();
    }

    private function getTopPagesByPeriod($siteId, $activePeriod)
    {
        $startDate = null;
        $endDate = Carbon::now()->endOfDay();

        switch ($activePeriod) {
            case 'today':
                $startDate = Carbon::now()->startOfDay();
                break;
            case 'last_7_days':
                $startDate = Carbon::now()->subDays(6)->startOfDay();
                break;
            case 'last_30_days':
                $startDate = Carbon::now()->subDays(29)->startOfDay();
                break;
        }

        $topPages = Pageview::where('site_id', $siteId)
            ->when(($startDate && $endDate), function ($query) use ($startDate, $endDate) {
                return $query->whereBetween('viewed_at', [$startDate, $endDate]);
            })
            ->pluck('path')
            ->countBy()
            ->sortDesc()
            ->take(5);

        return $topPages;
    }

    private function calculateBounceRate($siteId, $totalVisitsCount, $activePeriod) {
        $visits = $this->getVisitsByPeriod($siteId, $activePeriod);
        return $totalVisitsCount > 0 ? round($visits->has('pageviews', '=', 1)->count() / $totalVisitsCount * 100, 2) : 0;
    }

    private function getChartDataForActivePeriod($activePeriod, $siteId)
    {
        $startDate = Carbon::now()->subDays(29)->startOfDay();
        $endDate = Carbon::now()->endOfDay();

        if ($activePeriod == 'last_7_days') {
            $startDate = Carbon::now()->subDays(6)->startOfDay();
        }

        $data = Visit::where('site_id', $siteId)
            ->selectRaw("DATE(visited_at) as date, count(*) as count")
            ->whereBetween('visited_at', [$startDate, $endDate])
            ->groupBy('date')
            ->get();

        $chartData = $data->pluck('count');

        $chartLabels = $data->pluck('date');

        return ['labels' => $chartLabels, 'data' => $chartData];
    }

}
