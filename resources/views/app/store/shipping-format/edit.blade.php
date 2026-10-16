<x-layouts.master>
    <x-slot name="content">
        <x-general.breadcrumb-header>
            <x-slot name="title_admin"><span class="uppercase">{{ $title_edit }}</span></x-slot>

            <x-slot name="links">
                <li class="breadcrumb-item"><a href="{{ route('person') }}">{{ $option_menu }}</a></li>
                <li class="breadcrumb-item active">{{ $option_edit }}</li>
            </x-slot>
        </x-general.breadcrumb-header>
        <div class="max-w-3xl mx-auto py-2 sm:px-2 lg:px-4">
            @livewire('store.shipping-format.mant-component', ["person" => $person, "modal" => false, "event_modal" => "", "role_id" =>""])
        </div>
    </x-slot>
</x-layouts.master>
