<div>
    @if (!$modal)
        <div class="bg-white rounded-md py-8 px-8 shadow-md">
    @endif
    <div  x-data="{ type: @entangle('type').defer }">
        <div class="grid grid-cols-6 sm:grid-cols-6">
            <div class="p-3 col-span-2" style="display: none;">
                <label for="scheduled_date">Fecha Programada: </label>
                <x-general.input type="date" id="scheduled_date"  placeholder="Fecha" wire:model.defer="scheduled_date" />
                <x-jet-input-error for="scheduled_date" />
            </div>
            <div class="p-3 col-span-2">
                <label for="done_date">Fecha Realizada: </label>
                <x-general.input type="date" id="done_date"  placeholder="Fecha" wire:model.defer="done_date" />
                <x-jet-input-error for="done_date" />
            </div>
            <div class="p-3 col-span-2">
                <label for="type">Tipo: </label>
                <x-general.select id="type" wire:model="type" x-model="type">
                    @foreach ($types as $key => $item)
                        <option value="{{ $key }}"> {{ $item }} </option>
                    @endforeach
                </x-general.select>
                <x-jet-input-error for="type" />
            </div>
            <div class="p-3 col-span-3 sm:col-span-3">
                <div x-data="{ open: false }">
                    <div @click.away="open = false" class="py-2 sm:mr-2 col-span-1 pb-0">
                        <div class="flex justify-between">
                            <x-jet-label for="product_name" class="font-semibold text-gray-600">Producto / Serie: </x-jet-label>
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
                                <li wire:click="select_active_product('{{ $product["id"] }}')" class="py-2 px-2 hover:bg-blue-100 cursor-pointer">{{ $product["product_name"]}}</li>
                            @endforeach
                        </ul>
                    </div>
                    <x-jet-input-error for="product_name" />
                </div>
            </div>
            <div class="p-3 col-span-3 sm:col-span-3">
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
                            class="select-all" readonly=""
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
            <div class="p-3 col-span-2">
                <label for="head_name">Responsable Ing.: </label>
                <x-general.input type="text" id="head_name"  placeholder="Responsable Ingenieria" wire:model.defer="head_name" />
                <x-jet-input-error for="head_name" />
            </div>
            <div class="p-3 col-span-2">
                <label for="staff_name">Personal Usuario: </label>
                <x-general.input type="text" id="staff_name"  placeholder="Personal" wire:model.defer="staff_name" />
                <x-jet-input-error for="staff_name" />
            </div>
            <div class="p-3 col-span-2">
                <label for="observations">Observaciones: </label>
                <x-general.textarea type="text" id="observations"  placeholder="Observaciones" wire:model.defer="observations" ></x-general.textarea>
            </div>
        </div>
        <div class="grid grid-cols-12 p-3 gap-5" x-show="type=='C' || type=='A' || type=='I'">
            <div class="col-span-12 lg:col-span-5">
                <div class="grid grid-cols-12 gap-2">
                    <div class="col-span-12 lg:col-span-4 max-h-20">
                        <label for="barcode">C. Barra: </label>
                        <x-general.input type="text" id="barcode" placeholder="Código de Barra" wire:model.defer="barcode" wire:keydown.enter="select_bar_code" onclick="this.select()" />
                        <x-jet-input-error for="barcode" />
                    </div>
                    <div class="col-span-12 lg:col-span-8 max-h-20">
                        <label for="name">Producto: </label>
                        <x-general.input type="text" name="search_product" placeholder="Producto" wire:model.defer="search_product" wire:keyup="search_products" wire:keydown.enter="search_products"/>
                        <x-jet-input-error for="name" />
                    </div>
                </div>
                <div class="overflow-auto max-h-96 rounded shadow mt-2">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-200">
                            <tr>
                                <th colspan="1" class="sticky top-0 bg-blue-900 text-white py-1 w-4/5 text-center text-xs font-bold uppercase tracking-wider">Productos</th>
                                <th colspan="1" class="sticky top-0 bg-blue-900 text-white py-1 text-center text-xs font-bold uppercase tracking-wider">Stock</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @if (count($list_search_product) == 0)
                            <tr>
                                <td colspan="2" class="px-3 py-2 text-center text-xs text-gray-500"> No se encontraron coincidencias. </td>
                            </tr>
                            @else
                                @foreach ($list_search_product as $key => $product)
                                <tr class="hover:bg-blue-100 cursor-pointer" wire:click="select_products({{$key}})"
                                wire:key="select-product-{{$product["id"]}}">
                                    <td class="px-3 py-2 text-left text-xs text-gray-500">
                                        <p class="text-sm text-gray-700 font-bold">{{ $product["name"].' '.$product['model'] }}</p>
                                        <div class="hidden lg:block lg:visible">
                                            <p class="pl-1"><span class="font-bold">CÓDIGO: </span><span>{{ $product["code"] }}</span></p>
                                            <p class="pl-1"><span class="font-bold">CATEGORÍA: </span><span>{{ $product["category_name"] }}</span></p>
                                            {{-- <p class="pl-1"><span class="font-bold">DISTRIBUIDOR: </span><span>{{ $product["supplier_name"] }}</span></p> --}}
                                            <p class="pl-1"><span class="font-bold">MARCA: </span><span>{{ $product["brand_name"] }}</span></p>
                                            {{-- <p class="pl-1"><span class="font-bold">UNIDADES: </span><span>{{ implode(', ', $product["units_name"]) }}</span></p> --}}
                                        </div>
                                    </td>
                                    <td class="px-3 py-2 text-center text-xs text-gray-500">{{ $product["stock"] }} ({{ $product["base_unit_code"] }})</td>
                                </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="col-span-12 lg:col-span-7">
                <div class="overflow-auto rounded shadow" style="max-height: 500px">
                    <x-jet-input-error for="shopping_car" />
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-200">
                            <tr>
                                <th colspan="1" class="sticky top-0 py-1 w-7/12 text-center text-xs font-bold bg-blue-900 text-white uppercase tracking-wider">Descripción</th>
                                <th colspan="1" class="sticky top-0 py-1 w-2/12 text-center text-xs font-bold bg-blue-900 text-white uppercase tracking-wider">Und.</th>
                                <th colspan="1" class="sticky top-0 py-1 w-2/12 text-center text-xs font-bold bg-blue-900 text-white uppercase tracking-wider">Cant.</th>
                                <th colspan="1" class="sticky top-0 py-1 w-2/12 text-center text-xs font-bold bg-blue-900 text-white uppercase tracking-wider">Guia</th>
                                <th colspan="1" class="sticky top-0 py-2 w-1/12 text-center text-xs font-bold bg-blue-900 text-white uppercase tracking-wider"></th>
                                <th colspan="1" class="sticky top-0 py-1 w-1/12 text-center text-xs font-bold bg-blue-900 text-white uppercase tracking-wider">Quit.</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @if (count($shopping_car) == 0)
                            <tr>
                                <td colspan="4" class="px-3 py-2 text-center text-xs text-gray-500"> No ha seleccionado ningún producto. </td>
                            </tr>
                            @else
                                @foreach ($shopping_car as $key => $item)
                                    <tr>
                                        <td class="px-3 py-2 text-left text-sm text-gray-500"> {{ $item["product_name"].' '.$item["model"] }} </td>
                                        <td class="px-3 py-2 text-center text-xs text-gray-500">
                                            {{-- <x-general.select :full="false" :block="false" type="number" class="w-28"
                                            wire:model.defer="shopping_car.{{ $key }}.unit_id"
                                            autocomplete="off">
                                            @foreach ($item["units"] as $unit)
                                                <option value="{{$unit["id"]}}">{{$unit["name"]}}</option>
                                            @endforeach
                                            </x-general.select> --}}
                                            @foreach ($item["units"] as $unit)
                                                @if ($unit["id"] == $item["unit_id"])
                                                    {{ $unit["name"] }}
                                                @endif
                                            @endforeach
                                        </td>
                                        <td class="px-3 py-2 text-center text-xs text-gray-500">
                                            <x-general.input :full="false" :block="false" :id="'quantity_'.$item['product_id']" type="number" class="w-28" autocomplete="off" wire:model.defer="shopping_car.{{ $key }}.quantity" readonly="" />
                                            <x-jet-input-error for="shopping_car.{{ $key }}.quantity" />
                                        </td>
                                        <td class="px-3 py-2 text-center text-xs text-gray-500">
                                            <x-general.input :full="false" :block="false" :id="'guia_'.$item['product_id']" type="text" class="w-28" autocomplete="off" wire:model.defer="shopping_car.{{ $key }}.guia" />
                                            <x-jet-input-error for="shopping_car.{{ $key }}.guia" />
                                        </td>
                                        <td colspan="1" class="px-3 py-2 text-center text-xs text-gray-500">
                                            <button
                                            wire:click="add_product_move({{ $key }})"
                                            wire:key="add-product-move-{{ $item["product_id"] }}"><i class="fas fa-edit text-blue-600 text-base"></i></button>
                                        </td>
                                        <td class="px-3 py-2 text-center text-xs text-gray-500">
                                            <button
                                            wire:click="quit_car_item({{ $key }})"
                                            wire:key="quit-product-unit-{{ $item["product_id"] }}"><i class="fas fa-trash-alt text-red-600 text-base"></i></button>
                                        </td>
                                    </tr>
                                    @foreach ($item["product_moves"] as $key2 =>  $product_move)
                                        <tr>
                                            <td colspan="6" class="px-3 py-2 text-left text-sm text-gray-500">
                                                <div class="flex align-middle">
                                                    <div class="mr-3 w-5 flex items-center">
                                                        <i class="fas fa-angle-double-right"></i>
                                                    </div>
                                                    <div>
                                                        @if ($item["product_type"] == "M" || $item["product_type"] == "C" || $item["product_type"] == "E")
                                                            <p>Serie: {{ $product_move["product_serie"] }}</p>
                                                        @endif
                                                        @if ($item["product_type"] == "R")
                                                            <p>Lote: {{ $product_move["lot_name"] }}</p>
                                                            <p>Cod. Barra: {{ $product_move["lot_barcode"] }}</p>
                                                            <p>Fecha de Vencimiento: {{ $product_move["lot_due_date"] }}</p>
                                                            <p>Cantidad: {{ number_format($product_move["quantity"], 2) }}</p>
                                                        @endif
                                                    </div>

                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="mt-4 flex justify-end">
            <x-general.button x-on:click="show=false"  class="bg-gray-200 text-black hover:bg-gray-400 active:bg-gray-500 mx-2" wire:click="back_button">
                <i class="fas fa-arrow-left mr-2" aria-hidden="true"></i> {{ __(' Atrás') }}
            </x-general.button>
            @if ($productmaintenance->id)
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
    @php
        $document_detail_width = "2xl";
        $modal_document_detail_id = "modal-document-detail-id";
    @endphp
    <x-jet-dialog-modal wire:model="modal_document_detail" :id="$modal_document_detail_id" :maxWidth="$document_detail_width">
        @if ($document_detail_key != null)
            <x-slot name="title">
                {{ $shopping_car[$document_detail_key]["product_name"] }}
            </x-slot>
            <x-slot name="content">
                @if ($shopping_car[$document_detail_key]["product_type"] == "M" || $shopping_car[$document_detail_key]["product_type"] == "C" || $shopping_car[$document_detail_key]["product_type"] == "E")
                <!--div class="m-2 p-1 col-span-1 sm:col-span-2 grid grid-cols-1 sm:grid-cols-12">
                    <div class="p-2 col-span-1 sm:col-span-10">
                        <x-jet-label for="product_serie">Serie</x-jet-label>
                        <x-general.input type="text" id="product_serie" wire:model.defer="product_serie"/>
                    </div>
                    <div class="p-2 col-span-1 sm:col-span-2 flex items-center mt-0 sm:mt-3">
                        <div class="w-full text-center">
                            <button class="text-green-600 hover:underline" wire:click="add_product_serie"> <i class="fas fa-check"></i> Agregar</button>
                        </div>
                    </div>
                </div-->
                <div class="p-2 col-span-1 sm:col-span-10">
                    <div x-data="{ open: false }">
                        <div @click.away="open = false" class="py-2 sm:mr-2 col-span-1 pb-0">
                            <div class="flex justify-between">
                                <x-jet-label for="search_serie" class="font-semibold text-gray-600">Buscar Serie </x-jet-label>
                            </div>
                            <x-general.input type="text" id="search_serie"
                                x-on:click="open = true;"
                                x-on:keyup="open = true;"
                                onclick="this.select()"
                                placeholder="Serie"
                                wire:model.debounce.400ms="search_serie"
                                autocomplete="off"
                                class="select-all"
                                />
                        </div>
                        <div x-show="open" class="col-span-1 pr-2 pt-0 relative w-full">
                            <ul class="absolute bg-gray-100 shadow-md max-w-full right-2 left-0 z-50">
                                @foreach ($search_list_serie as $serie)
                                    <li wire:click="select_active_serie('{{ $serie["id"] }}')" class="py-2 px-2 hover:bg-blue-100 cursor-pointer">{{ $serie["serie"]  }}</li>
                                @endforeach
                            </ul>
                        </div>
                        <x-jet-input-error for="search_serie" />
                    </div>
                </div>
                <div class="overflow-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-200">
                            <tr>
                                <th colspan="1" class="sticky top-0 py-1 w-10/12 text-center text-xs font-bold bg-blue-900 text-white uppercase tracking-wider">Serie</th>
                                <th colspan="1" class="sticky top-0 py-1 w-2/12 text-center text-xs font-bold bg-blue-900 text-white uppercase tracking-wider"></th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @if (count($shopping_car[$document_detail_key]["product_moves"]) == 0)
                            <tr>
                                <td colspan="6" class="px-3 py-2 text-center text-xs text-gray-500"> No se ha agregado series. </td>
                            </tr>
                            @else
                                @foreach ($shopping_car[$document_detail_key]["product_moves"] as $key => $item)
                                    <tr>
                                        <td colspan="1" class="px-3 py-2 text-center text-sm text-gray-500"> {{ $item["product_serie"] }} </td>
                                        <td colspan="1" class="px-3 py-2 text-center text-xs text-gray-500">
                                            <button
                                            wire:click="quit_product_move({{ $key }})"
                                            wire:key="quit-product-serie-{{ $key }}"><i class="fas fa-trash-alt text-red-600 text-base"></i></button>
                                        </td>
                                    </tr>
                                @endforeach
                                @endif
                        </tbody>
                    </table>
                </div>
                @endif
                @if ($shopping_car[$document_detail_key]["product_type"] == "R")

                @if (false)
                    <div class="m-2 p-1 grid grid-cols-1 sm:grid-cols-12">
                        <div class="p-2 col-span-1 sm:col-span-4 md:col-span-3">
                            <x-jet-label for="product_lot">Lote</x-jet-label>
                            <x-general.input type="text" id="product_lot" wire:model.defer="product_lot"/>
                        </div>
                        <div class="p-2 col-span-1 sm:col-span-4 md:col-span-4">
                            <x-jet-label for="product_due_date">F. Venc.</x-jet-label>
                            <x-general.input type="date" id="product_due_date" wire:model.defer="product_due_date"/>
                        </div>
                        <div class="p-2 col-span-1 sm:col-span-4 md:col-span-3">
                            <x-jet-label for="lot_quantity">Cantidad</x-jet-label>
                            <x-general.input type="number" id="lot_quantity" wire:model.defer="lot_quantity"/>
                        </div>
                        <div class="p-2 col-span-1 sm:col-span-12 md:col-span-2 flex items-center mt-0 md:mt-3">
                            <div class="w-full text-center">
                                <button class="text-green-600 hover:underline" wire:click="add_product_lot"> <i class="fas fa-check"></i> Agregar</button>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="m-2 p-1 col-span-1 sm:col-span-2 grid grid-cols-1 sm:grid-cols-12">
                        <div class="p-2 col-span-1 sm:col-span-10">
                            <div x-data="{ open: false }">
                                <div @click.away="open = false" class="py-2 sm:mr-2 col-span-1 pb-0">
                                    <div class="flex justify-between">
                                        <x-jet-label for="search_lot" class="font-semibold text-gray-600">Buscar Lote </x-jet-label>
                                    </div>
                                    <x-general.input type="text" id="search_lot"
                                        x-on:click="open = true;"
                                        x-on:keyup="open = true;"
                                        onclick="this.select()"
                                        placeholder="Lote"
                                        wire:model.debounce.400ms="search_lot"
                                        autocomplete="off"
                                        class="select-all"
                                        />
                                </div>
                                <div x-show="open" class="col-span-1 pr-2 pt-0 relative w-full">
                                    <ul class="absolute bg-gray-100 shadow-md max-w-full right-2 left-0 z-50">
                                        @foreach ($search_list_lot as $lot)
                                            <li wire:click="select_active_lot('{{ $lot["id"] }}')" class="py-2 px-2 hover:bg-blue-100 cursor-pointer">{{ 'Lot: ' . $lot["lot"] . " | Cod: " . $lot["barcode"] . " | F.V.: " . date("d/m/Y",strtotime($lot["due_date"])) . ' | Queda: ' . number_format($lot["remain"],2,'.','') }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                                <x-jet-input-error for="search_lot" />
                            </div>
                        </div>

                        <?php /*
                            <x-jet-label for="search_lot">Buscar Lote</x-jet-label>
                            <x-general.input type="text" id="search_lot" wire:model.defer="search_lot"/>
                        </div>
                        <div class="p-2 col-span-1 sm:col-span-2 flex items-center mt-0 sm:mt-3">
                            <div class="w-full text-center">
                                <button class="text-green-600 hover:underline" wire:click="search_product_lot"> <i class="fas fa-search"></i> Buscar</button>
                            </div>
                        </div>*/ ?>
                    </div>
                    @if ($product_lot && $product_lot != "")
                        <div class="m-2 p-1 grid grid-cols-12 sm:grid-cols-12">
                            <div class="p-2 col-span-6 sm:col-span-4 md:col-span-4 pt-4">
                                <p>
                                    <span class="font-bold">Lote: </span> {{ $product_lot }}
                                </p>
                                <p>
                                    <span class="font-bold">Cod. Barra: </span> {{ $product_barcode }}
                                </p>
                                <p>
                                    <span class="font-bold">F. Venc: </span> {{ $product_due_date }}
                                </p>
                                <p>
                                    <span class="font-bold">Restante: </span> {{ $lot_remain }}
                                </p>
                            </div>
                            <div class="p-2 col-span-4 sm:col-span-4 md:col-span-4">
                                <x-jet-label for="lot_quantity">Cantidad</x-jet-label>
                                <x-general.input type="number" id="lot_quantity" wire:model.defer="lot_quantity"/>
                            </div>
                            <div class="p-2 col-span-2 sm:col-span-2 md:col-span-2 flex items-center mt-0 md:mt-3">
                                <div class="w-full text-center">
                                    <button class="text-green-600 hover:underline" wire:click="add_product_lot"> <i class="fas fa-check"></i> Agregar</button>
                                </div>
                            </div>
                        </div>
                    @endif

                @endif
                <div class="overflow-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-200">
                            <tr>
                                <th colspan="1" class="sticky top-0 py-1 w-4/12 text-center text-xs font-bold bg-blue-900 text-white uppercase tracking-wider">Lote</th>
                                <th colspan="1" class="sticky top-0 py-1 w-4/12 text-center text-xs font-bold bg-blue-900 text-white uppercase tracking-wider">Cod. Barra</th>
                                <th colspan="1" class="sticky top-0 py-1 w-3/12 text-center text-xs font-bold bg-blue-900 text-white uppercase tracking-wider">Fec. Venc.</th>
                                <th colspan="1" class="sticky top-0 py-1 w-3/12 text-center text-xs font-bold bg-blue-900 text-white uppercase tracking-wider">Cantidad</th>
                                <th colspan="1" class="sticky top-0 py-1 w-2/12 text-center text-xs font-bold bg-blue-900 text-white uppercase tracking-wider"></th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @if (count($shopping_car[$document_detail_key]["product_moves"]) == 0)
                            <tr>
                                <td colspan="6" class="px-3 py-2 text-center text-xs text-gray-500"> No se ha agregado series. </td>
                            </tr>
                            @else
                                @foreach ($shopping_car[$document_detail_key]["product_moves"] as $key => $item)
                                    <tr>
                                        <td colspan="1" class="px-3 py-2 text-center text-sm text-gray-500"> {{ $item["lot_name"] }} </td>
                                        <td colspan="1" class="px-3 py-2 text-center text-sm text-gray-500"> {{ $item["lot_barcode"] }} </td>
                                        <td colspan="1" class="px-3 py-2 text-center text-sm text-gray-500"> {{ $item["lot_due_date"] }} </td>
                                        <td colspan="1" class="px-3 py-2 text-center text-sm text-gray-500"> {{ $item["quantity"] }} </td>
                                        <td colspan="1" class="px-3 py-2 text-center text-xs text-gray-500">
                                            <button
                                            wire:click="quit_product_move({{ $key }})"
                                            wire:key="quit-product-serie-{{ $key }}"><i class="fas fa-trash-alt text-red-600 text-base"></i></button>
                                        </td>
                                    </tr>
                                @endforeach
                                @endif
                        </tbody>
                    </table>
                </div>
                @endif
            </x-slot>
        @else
            <x-slot name="title">
                DETALLES DE PRODUCTOS
            </x-slot>
            <x-slot name="content">
                NO SE HA SELECCIONADO DETALLE
            </x-slot>
        @endif
        <x-slot name="footer">
            <x-jet-secondary-button x-on:click="show=false" wire:loading.attr="disabled">
                <i class="fas fa-arrow-left mr-2" aria-hidden="true"></i> REGRESAR
            </x-jet-secondary-button>
        </x-slot>
    </x-jet-dialog-modal>
</div>
@push("js")
    <script>
        $(".select2").select2();
    </script>
@endpush