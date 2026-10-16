<x-layouts.master>
    <x-slot name="content">
        <x-general.breadcrumb-header>
            <x-slot name="title_admin"><span class="uppercase">{{ $title_admin }}</span></x-slot>
            <x-slot name="links">
                <li class="breadcrumb-item"><a href="javascript: void(0);">{{ $category_menu }}</a></li>
                <li class="breadcrumb-item active">{{ $option_menu }}</li>
            </x-slot>
        </x-general.breadcrumb-header>
        <div class="max-w-8xl mx-auto py-2 sm:px-2 lg:px-4">
            @livewire('store.store-document.admin-component')
        </div>
    </x-slot>
</x-layouts.master>
