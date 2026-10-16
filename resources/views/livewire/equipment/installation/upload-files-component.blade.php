<div>
    <x-jet-confirmation-modal wire:model="open">
        <x-slot name="title">
            @if(!is_null($schedule))
            SUBIR ARCHIVO {{ $schedule->product_serie->product->name.' '.$schedule->product_serie->product->model.' '.$schedule->product_serie->serie}}
            @endif
        </x-slot>
        <x-slot name="content">
            <label for="image" class="font-semibold text-gray-600">Archivo: </label>
            <x-general.input type="file" id="image" wire:model="image" acept="*" />
            <x-jet-input-error for="image" />
        </x-slot>

        <x-slot name="footer">
            <x-jet-secondary-button wire:click="toggle_modal_upload" wire:loading.attr="disabled">
                <i class="fas fa-arrow-left mr-2" aria-hidden="true"></i> CANCELAR
            </x-jet-secondary-button>
            <x-general.button class="bg-green-500 text-white hover:bg-green-600 active:bg-green-700 mx-2" wire:click="upload_files"
                wire:loading.attr="disabled" wire:target="upload_files" wire:key="enabled-product">
                <i class="fas fa-thumbs-up mr-2" aria-hidden="true"></i> {{ __(' Subir Archivo') }}
            </x-general.button>

        </x-slot>
    </x-jet-confirmation-modal>
</div>
