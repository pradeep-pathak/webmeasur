<x-app-layout>

    <div class="pt-12 pb-8">
        <div class="max-w-[85rem] px-4 mx-auto">
           <div class="flex items-center justify-between">

               <h1 class="text-3xl font-semibold">My sites</h1>

               <button x-data="" @click="$dispatch('open-modal', 'add-new-site')" class="inline-block px-4 py-2 text-white bg-primary-500 hover:bg-primary-600 rounded">
                   Add New Site
               </button>

           </div>
        </div>
    </div>

    <div class="max-w-[85rem] px-4 pt-8 mx-auto border-t border-slate-300">
            @if ($sites->count() > 0)
            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">

                @foreach ($sites as $site)

                <div class="flex flex-col bg-white shadow-[rgba(149,157,165,0.1)_0px_8px_24px] rounded p-4 md:p-5 dark:bg-slate-900">
                    <h3 class="mb-2 text-2xl font-semibold">{{ $site->name }}</h3>
                    <p class="text-gray-600 text-base mb-4">{{ $site->domain }}</p>
                    <p class="mb-4"><strong>{{ $site->stats->total_visits_today }}</strong> visitors today</p>
                    <p class="mb-4"><strong>{{ $site->stats->total_visits_last_7_days }}</strong> visitors in last 7 days</p>
                    <p class="mb-4"><strong>{{ $site->stats->total_uniques }}</strong> total unique visitors</p>
                    <p class="mb-4"><strong>{{ $site->stats->bounce_rate }}%</strong> Bounce Rate</p>
                    <div class="mt-4">
                        <a href="/sites/{{ $site->domain }}" class="inline-block px-4 py-3 text-white bg-primary-500 hover:bg-primary-600 rounded">
                            See Data
                        </a>
                        <form action="/sites/{{ $site->domain }}" method="post" class="inline-block" x-data="{
                            confirmDeletion() {
                                if (confirm('Are you sure you delete site {{ $site->domain }}')) {
                                    this.submit()
                                } else {
                                    event.preventDefault();
                                }
                            }
                        }">
                            @csrf
                            @method('delete')
                            <button type="submit" class="inline-block px-4 py-3 text-black bg-slate-200 hover:bg-slate-300 rounded" @click="confirmDeletion">
                                Delete Site
                            </button>
                        </form>
                        <button x-data @click="$dispatch('open-change-access-modal', {
                            domain: '{{ $site->domain }}',
                            publicCode: '{{ $site->public_code }}'
                        })" class="inline-block px-4 py-3 text-black bg-slate-200 hover:bg-slate-300 rounded">
                            Change Access
                        </button>
                    </div>
                </div>

                @endforeach

            </div>

            @else
                <p class="text-gray-600 text-lg">You have not added a site. Please add one to start using WebMeasur on your site.</p>
            @endif

        </div>
    </div>

    <div
        x-data="{ show: false, site: {} }"
        x-on:open-change-access-modal.window="show = true; site = $event.detail"
        x-on:close-change-access-modal.window="show = false"
        x-on:close.stop="show = false"
        x-on:keydown.escape.window="show = false"
        x-show="show"
        class="fixed inset-0 overflow-y-auto px-4 py-6 sm:px-0 z-50"
        :style="{ display: show ? 'block' : 'none'  }"
    >
        <div
            x-show="show"
            class="fixed inset-0 transform transition-all"
            x-on:click="show = false"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
        >
            <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
        </div>

        <div
            x-show="show"
            class="mb-6 bg-white overflow-hidden shadow-xl transform transition-all sm:w-full sm:max-w-2xl sm:mx-auto"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
        >
            <form method="post" :action="`/sites/${site.domain}/change-access`" x-data="{
                access: 'private'
            }" x-init="$watch('access', value => {
                console.log(value);
                if (value == 'Public') {
                    site.publicCode = '{{ \Illuminate\Support\Str::random(16) }}'
                } else {
                    site.publicCode = ''
                }
            })" class="p-6">
                @csrf
                @method('patch')
                <h2 class="text-lg font-medium text-gray-900">
                    Change access for site <span x-text="site.domain"></span>
                </h2>

                <div class="mt-6">
                    <select
                        id="access"
                        name="access"
                        type="text"
                        class="mt-1 block w-full rounded"
                        placeholder="Name"
                        x-model="access"
                    >
                        <option :selected="site.publicCode == ''">Private</option>
                        <option :selected="site.publicCode != ''">Public</option>
                    </select>
                </div>

                <div class="mt-6" x-show="site.publicCode != ''">
                    <x-text-input
                        id="publicUrl"
                        name="publicUrl"
                        type="text"
                        class="mt-1 block w-full"
                        ::value="`{{ request()->getSchemeAndHttpHost() }}/sites/${site.domain}/${site.publicCode}`"
                    ></x-text-input>

                    <input type="hidden" name="publicCode" x-model="site.publicCode" />
                </div>

                <div class="mt-6">
                    <x-primary-button>
                        Save Changes
                    </x-primary-button>
                </div>
            </form>
        </div>
    </div>


    <div
        x-data="{ show: {{ $sites->count() > 0 ? 'false' : 'true' }} }"
        x-on:open-modal.window="$event.detail == 'add-new-site' ? show = true : null"
        x-on:close-modal.window="$event.detail == 'add-new-site' ? show = false : null"
        x-on:close.stop="show = false"
        x-on:keydown.escape.window="show = false"
        x-show="show"
        class="fixed inset-0 overflow-y-auto px-4 py-6 sm:px-0 z-50"
        :style="{ display: show ? 'block' : 'none'  }"
    >
        <div
            x-show="show"
            class="fixed inset-0 transform transition-all"
            x-on:click="show = false"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
        >
            <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
        </div>

        <div
            x-show="show"
            class="mb-6 bg-white overflow-hidden shadow-xl transform transition-all sm:w-full sm:max-w-2xl sm:mx-auto"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
        >
            <form method="post" action="{{ route('sites.store') }}" class="p-6">
                @csrf
                <h2 class="text-lg font-medium text-gray-900">
                    Add a new site
                </h2>

                <div class="mt-6">
                    <x-input-label for="name" value="Name" class="sr-only" />

                    <x-text-input
                        id="name"
                        name="name"
                        type="text"
                        class="mt-1 block w-full"
                        placeholder="Name"
                    />
                </div>
                <div class="mt-6">
                    <x-input-label for="domain" value="Domain" class="sr-only" />

                    <x-text-input
                        id="domain"
                        name="domain"
                        type="text"
                        class="mt-1 block w-full"
                        placeholder="Domain"
                    />
                </div>

                <div class="mt-6">
                    <x-primary-button>
                        Add Website
                    </x-primary-button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
