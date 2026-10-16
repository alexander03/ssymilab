<div>
    <div class="bg-white rounded-md py-8 px-8 shadow-md">
        <div class="grid grid-cols-1 gap-3">
            <div class="col-span-1">
                <span class="uppercase text-lg font-bold">Tipo: {{ $user_type->name }} </span>
            </div>
            @foreach ($all_categories as $category)
                <div class="col-span-1 ml-3">
                    <span class="uppercase font-bold text-base mt-2"> {{ $category->name }} </span>
                    @foreach ($category->menu_options as $option)
                        <div class="mt-1 ml-3">
                            <label class="flex items-center mt-3 mb-2">
                                <input type="checkbox" class="form-checkbox rounded h-4 w-4 text-blue-600"
                                    id="menu_option_{{$option->id}}"
                                    wire:key="menu_option_{{$option->id}}"
                                    wire:model.defer="menu_options"
                                    wire:change="change_menu_option('{{ $option->id }}')"
                                    value="{{ $option->id }}">
                                <span class="ml-2 text-gray-700 text-sm font-semibold">{{ $option->name }}</span>
                            </label>
                            @foreach ($option->actions as $action)
                                <div class="mt-1 ml-4">
                                    <label class="flex items-center my-2">
                                        <input type="checkbox" class="form-checkbox rounded h-4 w-4 text-blue-600"
                                            id="action_{{$action->id}}"
                                            wire:key="action_{{$action->id}}"
                                            wire:model.defer="actions"
                                            wire:change="change_action('{{ $action->id }}')"
                                            wire:target="change_action"
                                            wire:loading.attr="disabled"
                                            value="{{ $action->id }}">
                                        <span class="ml-2 text-gray-500 text-sm">{{ $action->name }}</span>
                                    </label>
                                </div>
                            @endforeach
                            @if (count($option->actions) == 0)
                                <div class="mt-1 ml-4">
                                    <span class="uppercase text-sm text-gray-400"> Sin acciones </span>
                                </div>
                            @endif
                        </div>
                    @endforeach
                    @if (count($category->menu_options) == 0)
                        <div class="mt-1 ml-3">
                            <span class="uppercase text-sm text-gray-400"> Sin opciones de menú </span>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>

        <div class="mt-4 flex justify-end">
            <x-general.button class="bg-gray-200 text-black hover:bg-gray-400 active:bg-gray-500 mx-2" wire:click="back_button">
                <i class="fas fa-arrow-left mr-2" aria-hidden="true"></i> {{ __(' Atrás') }}
            </x-general.button>

            <x-general.button class="bg-blue-500 text-white hover:bg-blue-600 active:bg-blue-700 mx-2" wire:click="save"
                wire:loading.attr="disabled" wire:target="save" wire:key="save-permissions">
                <i class="fas fa-save mr-2" aria-hidden="true"></i> {{ __(' Guardar') }}
            </x-general.button>
        </div>

    </div>
</div>
