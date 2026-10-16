<div>
    <x-jet-dialog-modal :maxWidth="$maxWidth" wire:model="open">
        <x-slot name="title">
            FILTRO AVANZADO
        </x-slot>
        <x-slot name="content">
            {{-- {{ json_encode($column) }} --}}
            {{ json_encode($array_filter) }}
            <div class="py-2 grid grid-cols-1 sm:grid-cols-12 gap-3">
                <div class="col-span-1 sm:col-span-3 md:col-span-2">
                    <x-general.select wire:model.defer="column" wire:change="change_column" id="column">
                        @foreach ($data as $item)
                            <option value="{{ $item["column"] }}">{{ $item["name"] }}</option>
                        @endforeach
                    </x-general.select>
                </div>
                <div class="col-span-1 sm:col-span-3">
                    <x-general.select wire:model.defer="operation" id="operation">
                        @foreach ($operations as $item)
                            <option value="{{ $item }}">{{ $dictionary[$item] }}</option>
                        @endforeach
                    </x-general.select>
                </div>
                <div class="col-span-1 sm:col-span-6 md:col-span-4">
                    @switch($data_type)
                        @case('string')
                            <x-general.input wire:key="string" wire:model.defer="value" type="text"/>
                            @break
                        @case('integer')
                            <x-general.input wire:key="integer" wire:model.defer="value" type="number" step="1"/>
                            @break
                        @case('decimal')
                            <x-general.input wire:key="decimal" wire:model.defer="value" type="number" step="0.1"/>
                            @break
                        @case('date')
                            <x-general.input wire:key="date" wire:model.defer="value" type="date"/>
                            @break
                        @default
                    @endswitch
                </div>
                <div class="col-span-1 sm:col-span-12 md:col-span-3">
                    <x-general.button wire:click="add_filter" class="w-full mt-2 bg-green-600 text-white hover:bg-green-700 active:bg-green-800 rounded-md mr-2"><i class="fa fa-check mr-1"></i>Agregar</x-general.button>
                </div>
            </div>
        </x-slot>
        <x-slot name="footer">
            <x-general.button class="bg-gray-200 text-black hover:bg-gray-400" wire:click="toggle_modal_filter">
                {{ __(' Cancelar') }}
            </x-general.button>
        </x-slot>
    </x-jet-dialog-modal>
</div>
