<x-layouts.master>
    <x-slot name="content">
        <x-general.breadcrumb-header>
            <x-slot name="title_admin"><span class="uppercase">{{ $title_admin }}</span></x-slot>
            <x-slot name="links">
                <li class="breadcrumb-item"><a href="javascript: void(0);">{{ $category_menu }}</a></li>
                <li class="breadcrumb-item active">{{ $option_menu }}</li>
            </x-slot>
        </x-general.breadcrumb-header>
        <div class="max-w-3xl mx-auto py-2 sm:px-2 lg:px-4">
            <div class="bg-white rounded-md py-8 px-8 shadow-md">
                <div class="font-bold text-lg py-4 px-1">{{ $company->business_name }} - {{ $company->ruc }}</div>

                <div class="px-3">
                    <label class="font-bold">RUC: </label>
                    <label>{{ $company->ruc }}</label>
                </div>

                <div class="px-3">
                    <label class="font-bold">Razón social: </label>
                    <label>{{ $company->business_name }}</label>
                </div>

                <div class="px-3">
                    <label class="font-bold">Nombre comercial: </label>
                    <label>{{ $company->trade_name }}</label>
                </div>

                <div class="px-3">
                    <label class="font-bold">Dirección fiscal: </label>
                    <label>{{ $company->address }}</label>
                </div>

                <div class="px-3">
                    <label class="font-bold">Departamento: </label>
                    <label>{{ mb_strtoupper($company->department->name) }}</label>
                </div>

                <div class="px-3">
                    <label class="font-bold">Provincia: </label>
                    <label>{{ mb_strtoupper($company->province->name) }}</label>
                </div>

                <div class="px-3">
                    <label class="font-bold">Distrito: </label>
                    <label>{{ mb_strtoupper($company->district->name) }}</label>
                </div>

                <div class="mt-4 flex justify-end">
                    @if (in_array('edit_company', $arr_actions))
                        <a href="{{ route('edit-company', ['company' => $company->id]) }}" class="no-underline hover:text-white">
                            <x-general.button class="bg-yellow-500 text-white hover:bg-yellow-600 active:bg-yellow-700 mx-2">
                                <i class="fas fa-edit mr-2" aria-hidden="true"></i> {{ __(' Editar') }}
                            </x-general.button>
                        </a>
                     @endif
                </div>
            </div>
        </div>
    </x-slot>
</x-layouts.master>
