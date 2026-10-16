<div>
    @php
        $modal_width = "2xl";
        $id = "modal-lot";
    @endphp
    <x-jet-dialog-modal wire:model="open" :id="$id" :maxWidth="$modal_width">
        @if ($lot != null)
            <x-slot name="title">
                EDITAR LOTE
            </x-slot>
            <x-slot name="content">
                <div class="m-2 p-1 col-span-1 sm:col-span-2 grid grid-cols-1 sm:grid-cols-12">
                    <div class="p-2 col-span-1 sm:col-span-12">
                        <x-jet-label for="barcode">Codigo de barras</x-jet-label>
                        <x-general.input type="text" id="barcode" wire:model.defer="barcode"/>
                    </div>
                    <div class="p-2 col-span-1 sm:col-span-12">
                        <x-jet-label for="name_lot">Lote</x-jet-label>
                        <x-general.input type="text" id="name_lot" wire:model.defer="name_lot"/>
                    </div>
                    <div class="p-2 col-span-1 sm:col-span-12">
                        <x-jet-label for="duet_date">Fecha</x-jet-label>
                        <x-general.input type="date" id="due_date" wire:model.defer="due_date"/>
                    </div>
                </div>
            </x-slot>
        @else
            <x-slot name="title">
                EDITAR LOTE
            </x-slot>
            <x-slot name="content">
                NO SE HA SELECCIONADO LOTE
            </x-slot>
        @endif
        <x-slot name="footer">
            <x-jet-secondary-button x-on:click="show=false" wire:loading.attr="disabled">
                <i class="fas fa-arrow-left mr-2" aria-hidden="true"></i> REGRESAR
            </x-jet-secondary-button>
            @if ($lot)
                <x-general.button class="bg-blue-500 text-white hover:bg-blue-600 active:bg-blue-700 mx-2" wire:click="save"
                    wire:loading.attr="disabled" wire:target="save" wire:key="save-lot">
                    <i class="fas fa-save mr-2" aria-hidden="true"></i> {{ __(' GUARDAR') }}
                </x-general.button>
            @endif
        </x-slot>
    </x-jet-dialog-modal>
</div>
