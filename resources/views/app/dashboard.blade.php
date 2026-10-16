<x-layouts.master>
    <div>
        <x-slot name="title">
            <h4 class="mb-0">Dashboardss</h4>
        </x-slot>

        <x-slot name="breadcrumb">
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Utility</a></li>
                    <li class="breadcrumb-item active">Starter Page</li>
                </ol>
            </div>
        </x-slot>

        <x-slot name="content">

            Contenido

            {{-- @livewire('profile.logout-other-browser-sessions-form') --}}
        </x-slot>
    </div>
</x-layouts.master>
