<div>
    <x-jet-confirmation-modal wire:model="open">
        <x-slot name="title">
            @if ($status == "Y")
                DESHABILITAR REGISTRO
            @elseif ($status == "N")
                HABILITAR REGISTRO
            @endif
        </x-slot>

        <x-slot name="content">
            @if ($status == "Y")
                ¿Desea deshabilitar el registro? El registro seguirá existiendo, pero no será mostrado en otras funcionalidades.
            @elseif ($status == "N")
                ¿Desea habilitar el registro? El registro será mostrado en otras funcionalidades.
            @endif

        </x-slot>

        <x-slot name="footer">
            <x-jet-secondary-button wire:click="toggle_modal" wire:loading.attr="disabled">
                <i class="fas fa-arrow-left mr-2" aria-hidden="true"></i> CANCELAR
            </x-jet-secondary-button>
            @if ($status == "Y")
                <x-general.button class="bg-red-500 text-white hover:bg-red-600 active:bg-red-700 mx-2" wire:click="change_status"
                    wire:loading.attr="disabled" wire:target="change_status" wire:key="disabled-product">
                    <i class="fas fa-thumbs-down mr-2" aria-hidden="true"></i> {{ __(' Deshabilitar') }}
                </x-general.button>
            @elseif ($status == "N")
                <x-general.button class="bg-green-500 text-white hover:bg-green-600 active:bg-green-700 mx-2" wire:click="change_status"
                    wire:loading.attr="disabled" wire:target="change_status" wire:key="enabled-product">
                    <i class="fas fa-thumbs-up mr-2" aria-hidden="true"></i> {{ __(' Habilitar') }}
                </x-general.button>
            @endif

        </x-slot>
    </x-jet-confirmation-modal>
</div>
