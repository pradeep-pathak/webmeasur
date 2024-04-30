<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Profile') }}
        </h2>
    </x-slot>

    <div class="pt-12">
        <div class="max-w-[85rem] mx-auto px-4 space-y-6">
            <div class="p-4 sm:p-8 bg-white shadow-[rgba(149,157,165,0.1)_0px_8px_24px] max-w-6xl">
                <div class="max-w-xl">
                    @include('profile.partials.update-account-information-form')
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white shadow-[rgba(149,157,165,0.1)_0px_8px_24px] max-w-6xl">
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white shadow-[rgba(149,157,165,0.1)_0px_8px_24px] max-w-6xl">
                <div class="max-w-xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
