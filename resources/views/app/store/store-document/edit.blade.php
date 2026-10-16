<x-layouts.master>
    <x-slot name="content">
        <x-general.breadcrumb-header>
            <x-slot name="title_admin"><span class="uppercase">{{ $title_edit }}</span></x-slot>

            <x-slot name="links">
                <li class="breadcrumb-item"><a href="{{ route('store-document') }}">{{ $option_menu }}</a></li>
                <li class="breadcrumb-item active">{{ $option_edit }}</li>
            </x-slot>
        </x-general.breadcrumb-header>
        <div class="max-w-8xl mx-auto py-2 sm:px-2 lg:px-4">
            @livewire('store.store-document.mant-component', ["store_document" => $store_document])
        </div>
    </x-slot>
</x-layouts.master>
