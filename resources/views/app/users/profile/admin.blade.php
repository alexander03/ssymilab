<x-layouts.master>
    <x-slot name="content">
        <x-general.breadcrumb-header>
            <x-slot name="title_admin"><span class="uppercase">{{ $title_admin }}</span></x-slot>
            <x-slot name="links">
                <li class="breadcrumb-item"><a href="javascript: void(0);">{{ $category_menu }}</a></li>
                <li class="breadcrumb-item active">{{ $option_menu }}</li>
            </x-slot>
        </x-general.breadcrumb-header>
        <div class="max-w-8xl mx-auto py-6 sm:px-4 lg:px-6">
            @if (Laravel\Fortify\Features::canUpdateProfileInformation())
                @livewire('users.profile.update-profile-information-form')

                <x-jet-section-border />
            @endif

            @if (Laravel\Fortify\Features::enabled(Laravel\Fortify\Features::updatePasswords()))
                <div class="mt-10 sm:mt-0">
                    @livewire('profile.update-password-form')
                </div>

                <x-jet-section-border />
            @endif

            @if (Laravel\Fortify\Features::canManageTwoFactorAuthentication())
                <div class="mt-10 sm:mt-0">
                    @livewire('profile.two-factor-authentication-form')
                </div>

                <x-jet-section-border />
            @endif

            <div class="mt-10 sm:mt-0">
                @livewire('profile.logout-other-browser-sessions-form')
            </div>

            @if (Laravel\Jetstream\Jetstream::hasAccountDeletionFeatures())
                <x-jet-section-border />

                <div class="mt-10 sm:mt-0">
                    @livewire('profile.delete-user-form')
                </div>
            @endif
        </div>
    </x-slot>
</x-layouts.master>
