<div>
    @if (!$modal)
        <div class="bg-white rounded-md py-8 px-8 shadow-md">
    @endif
        <div class="grid grid-cols-1 sm:grid-cols-3">
            <div class="p-3 col-span-1">
                <label for="installed_date">Fecha Instalacion: </label>
                <x-general.input type="date" id="installed_date"  placeholder="Fecha" wire:model.defer="installed_date" />
                <x-jet-input-error for="installed_date" />
            </div>
            <div class="p-3 col-span-1 sm:col-span-2">
                <div x-data="{ open: false }">
                    <div @click.away="open = false" class="py-2 sm:mr-2 col-span-1 pb-0">
                        <div class="flex justify-between">
                            <x-jet-label for="product_name" class="font-semibold text-gray-600">Producto: </x-jet-label>
                        </div>
                        <x-general.input type="text" id="product_name"
                            x-on:click="open = true;"
                            x-on:keyup="open = true;"
                            onclick="this.select()"
                            placeholder="Producto"
                            wire:model.debounce.400ms="product_name"
                            autocomplete="off"
                            class="select-all"
                            />
                    </div>
                    <div x-show="open" class="col-span-1 pr-2 pt-0 relative w-full">
                        <ul class="absolute bg-gray-100 shadow-md max-w-full right-2 left-0 z-50">
                            @foreach ($search_list_product as $product)
                                <li wire:click="select_active_product('{{ $product["id"] }}')" class="py-2 px-2 hover:bg-blue-100 cursor-pointer">{{ $product["product_name"] . " | " . $product["serie"]}}</li>
                            @endforeach
                        </ul>
                    </div>
                    <x-jet-input-error for="product_name" />
                </div>
            </div>
            <div class="p-3 col-span-1 sm:col-span-2">
                <div x-data="{ open: false }">
                    <div @click.away="open = false" class="py-2 sm:mr-2 col-span-1 pb-0">
                        <div class="flex justify-between">
                            <x-jet-label for="customer_name" class="font-semibold text-gray-600">Cliente: </x-jet-label>
                        </div>
                        <x-general.input type="text" id="customer_name"
                            x-on:click="open = true;"
                            x-on:keyup="open = true;"
                            onclick="this.select()"
                            placeholder="Cliente"
                            wire:model.debounce.400ms="customer_name"
                            autocomplete="off"
                            class="select-all"
                            />
                    </div>
                    <div x-show="open" class="col-span-1 pr-2 pt-0 relative w-full">
                        <ul class="absolute bg-gray-100 shadow-md max-w-full right-2 left-0 z-50">
                            @foreach ($search_list_people as $person)
                                <li wire:click="select_active_person('{{ $person["id"] }}')" class="py-2 px-2 hover:bg-blue-100 cursor-pointer">{{ $person["name_complete"] . " - " . $person["document_number"]}}</li>
                            @endforeach
                        </ul>
                    </div>
                    <x-jet-input-error for="customer_name" />
                </div>
            </div>
            <div class="p-3 col-span-1">
                <label for="product_place">Lugar: </label>
                <x-general.input type="text" id="product_place"  placeholder="Lugar" wire:model.defer="product_place" />
                <x-jet-input-error for="product_place" />
            </div>
            <div class="p-3 col-span-1">
                <label for="product_location">Ubicacion: </label>
                <x-general.input type="text" id="product_location"  placeholder="Ubicacion" wire:model.defer="product_location" />
                <x-jet-input-error for="product_location" />
            </div>
            <div class="p-3 col-span-1">
                <label for="status">Estado: </label>
                <x-general.select  wire:model="status" x-model="status">
                    @foreach ($list_status as $key => $item)
                        <option value="{{ $key }}"> {{ $item }} </option>
                    @endforeach
                </x-general.select>
                <x-jet-input-error for="status" />
            </div>
        </div>
        <div class="mt-4 flex justify-end">
            <x-general.button x-on:click="show=false"  class="bg-gray-200 text-black hover:bg-gray-400 active:bg-gray-500 mx-2" wire:click="back_button">
                <i class="fas fa-arrow-left mr-2" aria-hidden="true"></i> {{ __(' Atrás') }}
            </x-general.button>
            @if ($schedule->id)
                <x-general.button class="bg-yellow-500 text-white hover:bg-yellow-600 active:bg-yellow-700 mx-2" wire:click="save"
                    wire:loading.attr="disabled" wire:target="update" wire:key="update-brand">
                    <i class="fas fa-edit mr-2" aria-hidden="true"></i> {{ __(' Editar') }}
                </x-general.button>
            @else
                <x-general.button class="bg-blue-500 text-white hover:bg-blue-600 active:bg-blue-700 mx-2" wire:click="save"
                    wire:loading.attr="disabled" wire:target="save" wire:key="save-brand">
                    <i class="fas fa-save mr-2" aria-hidden="true"></i> {{ __(' Guardar') }}
                </x-general.button>
            @endif
        </div>
        @if (!$modal)
            </div>
        @endif

</div>
@push("js")
    <script>
        $(".select2").select2();
    </script>
@endpush