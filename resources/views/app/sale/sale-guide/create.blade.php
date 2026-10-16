<x-layouts.master>
    <x-slot name="content">
        <x-general.breadcrumb-header>
            <x-slot name="title_admin"><span class="uppercase">{{ $title_create }}</span></x-slot>

            <x-slot name="links">
                <li class="breadcrumb-item"><a href="{{ route('sale-guide') }}">{{ $option_menu }}</a></li>
                <li class="breadcrumb-item active">{{ $option_create }}</li>
            </x-slot>
        </x-general.breadcrumb-header>
        <div class="max-w-8xl mx-auto py-2 sm:px-2 lg:px-4">
            @livewire('sale.sale-guide.mant-component', ["sale_guide" => $sale_guide,"sale_id"=>$sale_id])
        </div>
    </x-slot>
</x-layouts.master>
