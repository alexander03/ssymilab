<div>
    @if (!$modal)
        <div class="bg-white rounded-md py-8 px-8 shadow-md">
    @endif
        <div class="p-3">
            <label for="name">Nombre: </label>
            <x-general.input type="text" id="name" placeholder="Nombre" wire:model.defer="name" />
            <x-jet-input-error for="name" />
        </div>
        <div class="p-3">
            <label for="icon">Ícono: </label>
            <div class="grid grid-cols-12 justify-items-center items-center">
                <div class="bg-gray-300 col-span-2 sm:col-span-1 w-full h-10 flex items-center mt-1 rounded-md rounded-tr-none rounded-br-none">
                    <div class="w-full text-center">
                        <i class="{{ $icon }} text-base "></i>
                    </div>
                </div>
                <x-general.input type="text" id="icon" placeholder="Ícono" class="col-span-10 sm:col-span-11 h-10 rounded-tl-none rounded-bl-none" wire:model.lazy="icon"/>
                <x-jet-input-error for="icon" />
            </div>
        </div>
        <div class="p-3">
            <label for="order">Orden: </label>
            <x-general.input type="number" id="order" placeholder="Orden" step="1" wire:model.defer="order" />
            <x-jet-input-error for="order" />
        </div>
        <div class="mt-4 flex justify-end">
            <x-general.button x-on:click="show=false" class="bg-gray-200 text-black hover:bg-gray-400 active:bg-gray-500 mx-2" wire:click="back_button">
                <i class="fas fa-arrow-left mr-2" aria-hidden="true"></i> {{ __(' Atrás') }}
            </x-general.button>
            @if ($menu_category->id)
                <x-general.button class="bg-yellow-500 text-white hover:bg-yellow-600 active:bg-yellow-700 mx-2" wire:click="update"
                    wire:loading.attr="disabled" wire:target="update" wire:key="update-menu-category">
                    <i class="fas fa-edit mr-2" aria-hidden="true"></i> {{ __(' Editar') }}
                </x-general.button>
            @else
                <x-general.button class="bg-blue-500 text-white hover:bg-blue-600 active:bg-blue-700 mx-2" wire:click="save"
                    wire:loading.attr="disabled" wire:target="save" wire:key="save-menu-category">
                    <i class="fas fa-save mr-2" aria-hidden="true"></i> {{ __(' Guardar') }}
                </x-general.button>
            @endif
        @if (!$modal)
            </div>
        @endif

    </div>
</div>
