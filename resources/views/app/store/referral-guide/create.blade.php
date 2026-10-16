<x-layouts.master>
    <x-slot name="content">
        <x-general.breadcrumb-header>
            <x-slot name="title_admin"><span class="uppercase">{{ $title_create }}</span></x-slot>

            <x-slot name="links">
                <li class="breadcrumb-item"><a href="{{ route('referral-guide') }}">{{ $option_menu }}</a></li>
                <li class="breadcrumb-item active">{{ $option_create }}</li>
            </x-slot>
        </x-general.breadcrumb-header>
        <div class="max-w-8xl mx-auto py-2 sm:px-2 lg:px-4">
            @livewire('store.referral-guide.mant-component', ["referral_guide" => $referral_guide])
        </div>
    </x-slot>
</x-layouts.master>
