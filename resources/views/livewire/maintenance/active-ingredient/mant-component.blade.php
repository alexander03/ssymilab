<div>
    @if (!$modal)
        <div class="bg-white rounded-md py-8 px-8 shadow-md">
    @endif
        <div class="p-3">
            <label for="name">Nombre: </label>
            <x-general.input type="text" id="name" placeholder="Nombre" wire:model.defer="name" />
            <x-jet-input-error for="name" />
        </div>
        <div class="mt-4 flex justify-end">
            <x-general.button x-on:click="show=false" class="bg-gray-200 text-black hover:bg-gray-400 active:bg-gray-500 mx-2" wire:click="back_button">
                <i class="fas fa-arrow-left mr-2" aria-hidden="true"></i> {{ __(' Atrás') }}
            </x-general.button>
            @if ($active_ingredient->id)
                <x-general.button class="bg-yellow-500 text-white hover:bg-yellow-600 active:bg-yellow-700 mx-2" wire:click="update"
                    wire:loading.attr="disabled" wire:target="update" wire:key="update-active-ingredient">
                    <i class="fas fa-edit mr-2" aria-hidden="true"></i> {{ __(' Editar') }}
                </x-general.button>
            @else
                <x-general.button class="bg-blue-500 text-white hover:bg-blue-600 active:bg-blue-700 mx-2" wire:click="save"
                    wire:loading.attr="disabled" wire:target="save" wire:key="save-active-ingredient">
                    <i class="fas fa-save mr-2" aria-hidden="true"></i> {{ __(' Guardar') }}
                </x-general.button>
            @endif
        @if (!$modal)
            </div>
        @endif

    </div>
</div>
