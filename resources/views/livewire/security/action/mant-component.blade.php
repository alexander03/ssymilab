<div>
    @if (!$modal)
        <div class="bg-white rounded-md py-8 px-8 shadow-md">
    @endif

        <div class="p-3">
            <div class="flex justify-between">
                <label for="menu_option_id" class="font-semibold text-gray-600">Opción: </label>
                @if (in_array('create_menu_option', $arr_actions))
                    <button type="button" class="text-blue-500 hover:underline" wire:click="toggle_menu_option">Nuevo</button>
                @endif

            </div>
            <x-general.select id="menu_option_id" wire:model.defer="menu_option_id">
                @foreach ($menu_options as $item)
                    <option value="{{ $item->id }}"> {{ $item->name }} </option>
                @endforeach
            </x-general.select>
            <x-jet-input-error for="menu_option_id" />
        </div>


        <div class="p-3">
            <label for="name">Nombre: </label>
            <x-general.input type="text" id="name" placeholder="Nombre" wire:model.defer="name" />
            <x-jet-input-error for="name" />
        </div>

        <div class="p-3">
            <label for="code">Código: </label>
            <x-general.input type="text" id="code" placeholder="Código" wire:model.defer="code" />
            <x-jet-input-error for="code" />
        </div>

        <div class="p-3">
            <label for="route">Ruta: </label>
            <x-general.input type="text" id="route" placeholder="Ruta" wire:model.defer="route" />
            <x-jet-input-error for="route" />
        </div>

        <div class="p-3">
            <label for="description">Descripción: </label>
            <x-general.textarea id="description" placeholder="Descripción" wire:model.defer="description"></x-general.textarea>
            <x-jet-input-error for="description" />
        </div>

        <div class="mt-4 flex justify-end">
            <x-general.button x-on:click="show=false" class="bg-gray-200 text-black hover:bg-gray-400 active:bg-gray-500 mx-2" wire:click="back_button">
                <i class="fas fa-arrow-left mr-2" aria-hidden="true"></i> {{ __(' Atrás') }}
            </x-general.button>
            @if ($action->id)
                <x-general.button class="bg-yellow-500 text-white hover:bg-yellow-600 active:bg-yellow-700 mx-2" wire:click="update"
                    wire:loading.attr="disabled" wire:target="update" wire:key="update-action">
                    <i class="fas fa-edit mr-2" aria-hidden="true"></i> {{ __(' Editar') }}
                </x-general.button>
            @else
                <x-general.button class="bg-blue-500 text-white hover:bg-blue-600 active:bg-blue-700 mx-2" wire:click="save"
                    wire:loading.attr="disabled" wire:target="save" wire:key="save-action">
                    <i class="fas fa-save mr-2" aria-hidden="true"></i> {{ __(' Guardar') }}
                </x-general.button>
            @endif

        </div>
    @if (!$modal)
        </div>
    @endif

    @if (in_array('create_menu_option', $arr_actions))
        <x-jet-dialog-modal wire:model="modal_menu_option">
            <x-slot name="title">
                CREAR MENU OPCIÓN
            </x-slot>

            <x-slot name="content">
                @livewire('security.menu-option.mant-component', ["menu_option" => $menu_option, "modal" => true, "event_modal" => "toggle-modal-menu-option"])
            </x-slot>
        </x-jet-dialog-modal>
    @endif
</div>
