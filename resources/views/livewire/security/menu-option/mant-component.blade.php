<div>
    @if (!$modal)
        <div class="bg-white rounded-md py-8 px-8 shadow-md">
    @endif
        <div class="p-3">
            <div class="flex justify-between">
                <label for="menu_category_id" class="font-semibold text-gray-600">Categoría: </label>
                @if (in_array('create_menu_category', $arr_actions))
                    <button type="button" class="text-blue-500 hover:underline" wire:click="toggle_menu_category">Nuevo</button>
                @endif
            </div>
            <x-general.select id="menu_category_id" wire:model.defer="menu_category_id">
                @foreach ($menu_categories as $item)
                    <option value="{{ $item->id }}"> {{ $item->name }} </option>
                @endforeach
            </x-general.select>
            <x-jet-input-error for="menu_category_id" />
        </div>


        <div class="p-3">
            <label for="name">Nombre: </label>
            <x-general.input type="text" id="name" placeholder="Nombre" wire:model.defer="name" />
            <x-jet-input-error for="name" />
        </div>

        <div class="p-3">
            <label for="link">Link: </label>
            <x-general.input type="text" id="link" placeholder="Link" wire:model.defer="link" />
            <x-jet-input-error for="link" />
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
            @if ($menu_option->id)
                <x-general.button class="bg-yellow-500 text-white hover:bg-yellow-600 active:bg-yellow-700 mx-2" wire:click="update"
                    wire:loading.attr="disabled" wire:target="update" wire:key="update-menu-option">
                    <i class="fas fa-edit mr-2" aria-hidden="true"></i> {{ __(' Editar') }}
                </x-general.button>
            @else
                <x-general.button class="bg-blue-500 text-white hover:bg-blue-600 active:bg-blue-700 mx-2" wire:click="save"
                    wire:loading.attr="disabled" wire:target="save" wire:key="save-menu-option">
                    <i class="fas fa-save mr-2" aria-hidden="true"></i> {{ __(' Guardar') }}
                </x-general.button>
            @endif

        </div>
    @if (!$modal)
        </div>
    @endif
    @if (in_array('create_menu_category', $arr_actions))
        <x-jet-dialog-modal wire:model="modal_menu_category">
            <x-slot name="title">
                CREAR CATEGORIA
            </x-slot>

            <x-slot name="content">
                @livewire('security.menu-category.mant-component', ["menu_category" => $menu_category, "modal" => true, "event_modal" => "toggle-modal-menu-category"])
            </x-slot>
        </x-jet-dialog-modal>
    @endif
</div>
