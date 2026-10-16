<x-layouts.master>
    <x-slot name="content">
        <x-general.breadcrumb-header>
            <x-slot name="title_admin"><span class="uppercase">{{ $title_edit }}</span></x-slot>

            <x-slot name="links">
                <li class="breadcrumb-item"><a href="{{ route('user') }}">{{ $option_menu }}</a></li>
                <li class="breadcrumb-item active">{{ $option_edit }}</li>
            </x-slot>
        </x-general.breadcrumb-header>
        <div class="max-w-2xl mx-auto py-2 sm:px-2 lg:px-4">
            @livewire('users.user.mant-component', ["user" => $user, "modal" => false, "event_modal" => ""])
        </div>
    </x-slot>
</x-layouts.master>
