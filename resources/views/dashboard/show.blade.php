<x-app-layout>
    @if (isset($visits) && $site->visits_count > 0)
    <div class="max-w-[85rem] mx-auto px-4 mt-8 lg:mt-0">
        @if ($visits->count() === 0)
        <p class="font-medium mt-6">No visits {{ $activePeriod }}</p>
        @endif

        <div class="flex items-center justify-between py-6">
            <h1 class="text-2xl font-bold">{{ $site->name }}</h1>

            <form action="#" x-data>
                Period: <select @change="event.target.parentNode.submit()" name="period" class="ml-1 pr-12 py-1">
                    <option value="today"{{ $activePeriod == 'today' ? ' selected' : '' }}>Today</option>
                    <option value="last_7_days"{{ $activePeriod == 'last_7_days' ? ' selected' : '' }}>Last 7 Days</option>
                    <option value="last_30_days"{{ $activePeriod == 'last_30_days' ? ' selected' : '' }}>Last 30 Days</option>
                </select>
            </form>
        </div>

        <div class="pb-6 grid grid-cols-2 md:grid-cols-4 gap-4">

            <div class="p-4 bg-white rounded shadow-[rgba(149,157,165,0.1)_0px_8px_24px]">
                <div class="flex items-start">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-8 h-8 text-primary-600"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                    <div class="ml-2">
                        <h3 class="text-3xl font-bold mb-2">{{ $totalVisitsCount }}</h3>
                        <p class="text-gray-700 font-medium text-base">Total visitors</p>
                    </div>
                </div>
            </div>
            <div class="p-4 bg-white rounded shadow-[rgba(149,157,165,0.1)_0px_8px_24px]">
                <div class="flex items-start">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-8 h-8 text-primary-600"><line x1="4" y1="9" x2="20" y2="9"></line><line x1="4" y1="15" x2="20" y2="15"></line><line x1="10" y1="3" x2="8" y2="21"></line><line x1="16" y1="3" x2="14" y2="21"></line></svg>
                    <div class="ml-2">
                        <h3 class="text-3xl font-bold mb-2">{{ $uniqueVisitors }}</h3>
                        <p class="text-gray-700 font-medium text-base">Unique visitors</p>
                    </div>
                </div>
            </div>
            <div class="p-4 bg-white rounded shadow-[rgba(149,157,165,0.1)_0px_8px_24px]">
                <div class="flex items-start">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-8 h-8 text-primary-600"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path><polyline points="15 3 21 3 21 9"></polyline><line x1="10" y1="14" x2="21" y2="3"></line></svg>
                    <div class="ml-2">
                        <h3 class="text-3xl font-bold mb-2">{{ $bounceRate }}%</h3>
                        <p class="text-gray-700 font-medium text-base">Bounce rate</p>
                    </div>
                </div>
            </div>
            <div class="p-4 bg-white rounded shadow-[rgba(149,157,165,0.1)_0px_8px_24px]">
                <div class="flex items-start">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-8 h-8 text-primary-600"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                    <div class="ml-2">
                        <h3 class="text-3xl font-bold mb-2">{{ $avgVisitDuration > 0 ? formatSeconds($avgVisitDuration) : '0s' }}</h3>
                        <p class="text-gray-700 font-medium text-base">Avg Visit Duration</p>
                    </div>
                </div>
            </div>

        </div>

        @if (isset($chartData) && count($chartData) > 1)
        <div class="pb-6">
            <div x-data="{
                chart: null,
                initChart() {
                    this.chart = new ApexCharts(this.$refs.chartDiv, {
                        chart: {
                            type: 'bar',
                            height: 500,
                            toolbar: {
                                show: false
                            }
                        },
                        series: [{
                            name: 'Total sessions this period',
                            data: [
                                @foreach($chartData as $item)
                                {{ $item }},
                                @endforeach
                            ]
                        }],
                        xaxis: {
                            categories: [
                                @foreach($chartLabels as $label)
                                    '{{ \Carbon\Carbon::parse($label)->format("M d") }}',
                                @endforeach
                            ]
                        },
                        colors: ['#aa55f7'],
                    });
                    this.chart.render();
                }
            }" x-init="initChart" class="bg-white rounded shadow-[rgba(149,157,165,0.1)_0px_8px_24px]">
                <div x-ref="chartDiv"></div>
            </div>
        </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 pb-6">

            <div class="p-4 shadow-[rgba(149,157,165,0.1)_0px_8px_24px] bg-white">
                <h4 class="text-xl font-bold mb-4">Top sources</h4>
                <div x-data="{
                    chart: null,
                    initChart() {
                        this.chart = new ApexCharts(this.$refs.chartDiv, {
                            chart: {
                                type: 'pie',
                            },
                            series: [
                                @foreach($referrerCounts->pluck('count') as $count)
                                    {{ $count }},
                                @endforeach
                            ],
                            labels: [
                                @foreach ($referrerCounts->pluck('referrer') as $referrer)
                                    '{{ $referrer }}',
                                @endforeach
                            ],
                        });
                        this.chart.render();
                    }
                }" x-init="initChart" class="h-80">
                    <div x-ref="chartDiv"></div>
                </div>

            </div>

            <div class="p-4 shadow-[rgba(149,157,165,0.1)_0px_8px_24px] bg-white">
                <h4 class="text-xl font-bold mb-4">Top locations</h4>
                <table class="min-w-full text-left">
                    <thead>
                        <tr>
                            <th scope="col">City</th>
                            <th scope="col">Visitors</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($locationCounts as $city => $count)
                        <tr>
                            <td class="py-1"><span class="fi fi-{{ $count['country_code'] }} mr-1"></span> {{ $city }}</td>
                            <td class="py-1">{{ $count['count'] }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="p-4 shadow-[rgba(149,157,165,0.1)_0px_8px_24px] bg-white">
                <h4 class="text-xl font-bold mb-4">Top pages</h4>
                <table class="min-w-full text-left">
                    <thead>
                        <tr>
                            <th scope="col">Page</th>
                            <th scope="col">Pageviews</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($topPages as $page => $pageviews)
                        <tr>
                            <td class="py-1">{{ $page }}</td>
                            <td class="py-1">{{ $pageviews }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

        </div>

        @if ($visits->count() > 0)
        <div class="py-4 shadow-[rgba(149,157,165,0.1)_0px_8px_24px] bg-white" id="visit-logs">
            <div class="mb-4 px-6 pb-4 border-b border-slate-300">
                <h4 class="text-xl font-bold mb-2">Visit logs</h4>
                <p class="text-slate-600 text-sm">Here are some of your latest visits.</p>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full text-left text-base text-gray-700">
                    <thead>
                        <th class="whitespace-nowrap px-6 pb-2" scope="col">Location</th>
                        <th class="whitespace-nowrap px-6 pb-2" scope="col">Referrer</th>
                        <th class="whitespace-nowrap px-6 pb-2" scope="col">Duration</th>
                        <th class="whitespace-nowrap px-6 pb-2" scope="col">Pages</th>
                        <th class="whitespace-nowrap px-6 pb-2" scope="col">Device</th>
                        <th class="whitespace-nowrap px-6 pb-2" scope="col">Browser</th>
                    </thead>
                    <tbody class="divide-y divide-slate-300">
                        @foreach ($visits as $visit)
                        <tr>
                            <td class="whitespace-nowrap px-6 py-4 align-baseline"><span class="fi fi-{{ strtolower($visit->country_code) }} mr-1"></span> {{ $visit->city }}, {{ $visit->region }}, {{ $visit->country }}</td>
                            <td class="whitespace-nowrap px-6 py-4 align-baseline">{{ $visit->referrer }}</td>
                            <td class="whitespace-nowrap px-6 py-4 align-baseline">{{ formatSeconds($visit->duration) }}</td>
                            <td class="whitespace-nowrap px-6 py-4 align-baseline">
                                <ul class="space-y-1">
                                    @foreach ($visit->pageviews as $page)
                                    <li>{{ $page->path }}</li>
                                    @endforeach
                                </ul>
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 align-baseline">{{ $visit->device }}</td>
                            <td class="whitespace-nowrap px-6 py-4 align-baseline">{{ $visit->browser }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if ($visits->hasPages())
                <div class="px-4 pt-4 flex items-center justify-end border-t border-slate-300">
                    {{ $visits->fragment('visit-logs')->appends(request()->except('page'))->links() }}
                </div>
            @endif
        </div>
        @endif

    </div>

    @else
    <div class="max-w-[85rem] mx-auto px-4 py-12">
        <p class="text-lg text-gray-600 mb-4">Sorry, your website <strong>{{ $site->domain }}</strong> has no
            visitors. <br>If you haven't already, please add this code before the closing of the head tag of your
            website:</p>
        <pre><code class="language-html">&lt;script async src=&quot;{{ request()->getSchemeAndHttpHost() }}/script.js&quot; data-tracking-code=&quot;{{ $site->tracking_code }}&quot;&gt;&#x3C;/script&#x3E;</code></pre>

        <a href="#" type="button" class="inline-block px-4 py-3 text-white bg-primary-500 hover:bg-primary-600 rounded mt-6">
            Send To Developer
        </a>
    </div>
    @endif
</x-app-layout>
