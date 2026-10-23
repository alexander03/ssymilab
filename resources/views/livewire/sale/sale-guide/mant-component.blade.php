<div>
    <div class="bg-white rounded-md py-8 px-8 shadow-md">
        <div class="p-2">
            <div class="grid grid-cols-12 mb-4 gap-2">
                <div class="col-span-12 order-last md:order-first md:col-span-9 flex items-center uppercase p-3">
                    <div class="w-full text-center">
                        <p class="font-bold text-lg"> {{ $branch_office["company_name"] }} </p>
                        <p class="font-bold"> {{ $branch_office["name"] }} </p>
                        <p> {{ $branch_office["address"] . " - " . $branch_office["department_name"]. " - " . $branch_office["province_name"]. " - " . $branch_office["district_name"] }} </p>

                    </div>
                </div>
                <div class="col-span-12 md:col-span-3">
                    <div class="border-1 border-gray-500 rounded-md text-center p-3">
                        <p class="text-base"> {{ date_format(new DateTime($date_time), 'd/m/Y') }} </p>
                        <p class="text-lg font-bold"> GUIA DE REMISION</p>
                        <p class="text-base font-bold">RUC: {{ $branch_office["company_ruc"] }}</p>
                        <p class="text-base font-bold"> {{ ($number && $number != "") ? $number : "-" }} </p>
                    </div>
                </div>
            </div>
            <div class="grid grid-cols-12 gap-4">
                <div class="col-span-12 lg:col-span-7 shadow-md rounded-md p-3 "  style="background-color: #f2f5f9">
                    <div class="grid grid-cols-12">
                        <div class="col-span-12 md:col-span-4 mr-2">
                            <div class="py-1">
                                <x-jet-label for="reason_id" class="font-semibold text-gray-600">Motivo: </x-jet-label>
                                <x-general.select name="reason_id" wire:model.change="reason_id">
                                    @foreach ($reasons as $item)
                                        <option value="{{ $item->id }}"> {{ $item->name }} </option>
                                    @endforeach
                                </x-general.select>
                                <x-jet-input-error for="reason_id" />
                            </div>
                        </div>
                        <div class="col-span-12 md:col-span-4 mr-2">
                            <div class="py-1">
                                <x-jet-label for="transfer_date" class="font-semibold text-gray-600">Fecha Traslado: </x-jet-label>
                                <x-general.input type="date" name="transfer_date" wire:model.defer="transfer_date"/>
                                <x-jet-input-error for="transfer_date" />
                            </div>
                        </div>
                        <div class="col-span-12 sm:col-span-4">
                            <div x-data="{ open_purchase: false }" class="py-1 pr-1">
                                <div class="grid grid-cols-12">
                                    <div @click.away="open_purchase = false;" class="pb-0 col-span-11">
                                        <x-jet-label for="search_document" class="font-semibold text-gray-600">Venta: </x-jet-label>
                                        @if ($document_id)
                                        <x-general.input type="text" disabled name="search_document"
                                            x-on:click="open_purchase = true;"
                                            x-on:keyup="open_purchase = true;"
                                            onclick="this.select()"
                                            placeholder="Venta"
                                            wire:model.debounce.400ms="search_document"
                                            autocomplete="off"
                                            class="select-all"
                                            wire:key="disabled-search-document"
                                        />
                                        @else
                                        <x-general.input type="text" name="search_document"
                                            x-on:click="open_purchase = true;"
                                            x-on:keyup="open_purchase = true;"
                                            onclick="this.select()"
                                            placeholder="Venta"
                                            wire:model.debounce.400ms="search_document"
                                            autocomplete="off"
                                            class="select-all"
                                            wire:key="enabled-search-document"
                                        />
                                        @endif
                                    </div>
                                    <div class="pb-0 col-span-1">
                                        <button type="button" class=" text-red-500 hover:text-red-700" wire:click="quit_document">
                                            <i class="fas fa-times pt-10 pl-2"></i>
                                        </button>
                                    </div>
                                </div>
                                <div x-show="open_purchase" class="pt-0 relative w-full">
                                    <ul class="absolute bg-gray-100 shadow-md max-w-full right-4 left-0 z-50">
                                        @foreach ($search_list_document as $document)
                                            <li wire:click="select_active_document('{{ $document["id"] }}')" class="py-2 px-2 hover:bg-blue-100 cursor-pointer">{{ $document["number"] }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                                <x-jet-input-error for="search_document" />
                            </div>
                        </div>
                        <div class="col-span-12 sm:col-span-9">
                            <div x-data="{ open: false }">
                                <div @click.away="open = false" class="py-2 sm:mr-2 col-span-1 pb-0">
                                    <div class="flex justify-between">
                                        <x-jet-label for="search_person" class="font-semibold text-gray-600">Cliente: </x-jet-label>
                                        @if (in_array('create_person', $arr_actions))
                                            <button type="button" class="text-blue-500 hover:underline" wire:click="toggle_supplier">Nuevo</button>
                                        @endif
                                    </div>
                                    <x-general.input type="text" id="search_person"
                                        x-on:click="open = true;"
                                        x-on:keyup="open = true;"
                                        onclick="this.select()"
                                        placeholder="Cliente"
                                        wire:model.debounce.400ms="search_person"
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
                                <x-jet-input-error for="client_id" />
                            </div>
                        </div>
                        <div class="col-span-12 sm:col-span-3 mt-1">
                            <div class="py-2 sm:p-2">
                                <x-jet-label for="supplier_document" class="font-semibold text-gray-600"> {{ ($supplier["type"] == "1") ? "DNI" : "RUC" }}: </x-jet-label>
                                <x-general.input type="number" disabled class="disabled:bg-red-50" id="supplier_document" wire:model.defer="supplier.document_number" />
                                <x-jet-input-error for="supplier_document" />
                            </div>
                        </div>
                        <div class="col-span-4 mr-2">
                            <div class="py-1">
                                <x-jet-label for="reason_id" class="font-semibold text-gray-600">Modalidad: </x-jet-label>
                                <x-general.select name="modalidad" id="modalidad" wire:model.change="modalidad">
                                        <option value="01">TRANSPORTE PUBLICO</option>
                                        <option value="02">TRANSPORTE PRIVADO</option>
                                </x-general.select>
                                <x-jet-input-error for="modalidad" />
                            </div>
                        </div>
                        <div class="col-span-4 mr-2">
                            <div class="py-1">
                                <x-jet-label for="transbordo" class="font-semibold text-gray-600">Transbordo: </x-jet-label>
                                <x-general.select name="transbordo" id="transbordo" wire:model.change="transbordo">
                                        <option value="NO">NO</option>
                                        <option value="SI">SI</option>
                                </x-general.select>
                                <x-jet-input-error for="modalidad_id" />
                            </div>
                        </div>
                        <div class="col-span-4 mr-2">
                            <div class="py-1">
                                <x-jet-label for="orden_compra" class="font-semibold text-gray-600">Orden Compra: </x-jet-label>
                                <x-general.input type="text" class="disabled:bg-red-50" id="orden_compra" wire:model.defer="orden_compra" />
                                <x-jet-input-error for="orden_compra" />
                            </div>
                        </div>
                        <div class="col-span-12 sm:col-span-9">
                            <div x-data="{ open: false }">
                                <div @click.away="open = false" class="py-2 sm:mr-2 col-span-1 pb-0">
                                    <div class="flex justify-between">
                                        <x-jet-label for="search_transport" class="font-semibold text-gray-600">Transportista: </x-jet-label>
                                        @if (in_array('create_person', $arr_actions))
                                            <button type="button" class="text-blue-500 hover:underline" wire:click="toggle_supplier">Nuevo</button>
                                        @endif
                                    </div>
                                    <x-general.input type="text" id="search_transport"
                                        x-on:click="open = true;"
                                        x-on:keyup="open = true;"
                                        onclick="this.select()"
                                        placeholder="Transportista"
                                        wire:model.debounce.400ms="search_transport"
                                        autocomplete="off"
                                        class="select-all"
                                        />
                                </div>
                                <div x-show="open" class="col-span-1 pr-2 pt-0 relative w-full">
                                    <ul class="absolute bg-gray-100 shadow-md max-w-full right-2 left-0 z-50">
                                        @foreach ($search_list_transport as $person)
                                            <li wire:click="select_active_transport('{{ $person["id"] }}')" class="py-2 px-2 hover:bg-blue-100 cursor-pointer">{{ $person["name_complete"] . " - " . $person["document_number"]}}</li>
                                        @endforeach
                                    </ul>
                                </div>
                                <x-jet-input-error for="client_id" />
                            </div>
                        </div>
                        <div class="col-span-12 sm:col-span-3 mt-1">
                            <div class="py-2 sm:p-2">
                                <x-jet-label for="transport_document" class="font-semibold text-gray-600"> {{ ($supplier["type"] == "1") ? "DNI" : "RUC" }}: </x-jet-label>
                                <x-general.input type="number" disabled class="disabled:bg-red-50" id="transport_document" wire:model.defer="transport.document_number" />
                                <x-jet-input-error for="transport_document" />
                            </div>
                        </div>
                        <div class="col-span-3 mr-2">
                            <div class="py-1">
                                <x-jet-label for="transport_plate" class="font-semibold text-gray-600">Placa Transporte: </x-jet-label>
                                <x-general.input type="text" name="transport_plate" wire:model.defer="transport_plate"/>
                                <x-jet-input-error for="transport_plate" />
                            </div>
                        </div>
                        <div class="col-span-3 mr-2">
                            <div class="py-1">
                                <x-jet-label for="transport_licencia" class="font-semibold text-gray-600">Nro. Licencia: </x-jet-label>
                                <x-general.input type="text" name="transport_licencia" wire:model.defer="transport_licencia"/>
                                <x-jet-input-error for="transport_licencia" />
                            </div>
                        </div>
                        <div class="col-span-3 mr-2">
                            <div class="py-1">
                                <x-jet-label for="number_packages" class="font-semibold text-gray-600">Nro Paquetes: </x-jet-label>
                                <x-general.input type="text" name="number_packages" wire:model.defer="number_packages"/>
                                <x-jet-input-error for="number_packages" />
                            </div>
                        </div>
                        <div class="col-span-3 mr-2">
                            <div class="py-1">
                                <x-jet-label for="total_weight" class="font-semibold text-gray-600">Peso Total (KGM): </x-jet-label>
                                <x-general.input type="number" name="total_weight" wire:model.defer="total_weight"/>
                                <x-jet-input-error for="total_weight" />
                            </div>
                        </div>
                        <div class="col-span-12 sm:col-span-12">
                            <div x-data="{ open: false }">
                                <div @click.away="open = false" class="py-2 sm:mr-2 col-span-1 pb-0">
                                    <div class="flex justify-between">
                                        <x-jet-label for="search_start" class="font-semibold text-gray-600">Ubigeo de Partida: </x-jet-label>
                                    </div>
                                    <x-general.input type="text" id="search_start"
                                        x-on:click="open = true;"
                                        x-on:keyup="open = true;"
                                        onclick="this.select()"
                                        placeholder="Ubigeo de Partida"
                                        wire:model.debounce.400ms="search_start"
                                        autocomplete="off"
                                        class="select-all"
                                        />
                                </div>
                                <div x-show="open" class="col-span-1 pr-2 pt-0 relative w-full">
                                    <ul class="absolute bg-gray-100 shadow-md max-w-full right-2 left-0 z-50" style="max-height: 400px; overflow-y: auto;">
                                        @foreach ($search_list_district as $person)
                                            <li wire:click="select_active_start('{{ $person["code"] }}')" class="py-2 px-2 hover:bg-blue-100 cursor-pointer">{{ $person["name_complete"]}}</li>
                                        @endforeach
                                    </ul>
                                </div>
                                <x-jet-input-error for="client_id" />
                            </div>
                        </div>
                        <div class="col-span-12">
                            <div class="py-1">
                                <x-jet-label for="start_address" class="font-semibold text-gray-600">Punto de partida: </x-jet-label>
                                <x-general.input type="text" name="start_address" wire:model.defer="start_address"/>
                                <x-jet-input-error for="start_address" />
                            </div>
                        </div>
                        <div class="col-span-12 sm:col-span-12">
                            <div x-data="{ open: false }">
                                <div @click.away="open = false" class="py-2 sm:mr-2 col-span-1 pb-0">
                                    <div class="flex justify-between">
                                        <x-jet-label for="search_arrival" class="font-semibold text-gray-600">Ubigeo de Llegada: </x-jet-label>
                                    </div>
                                    <x-general.input type="text" id="search_arrival"
                                        x-on:click="open = true;"
                                        x-on:keyup="open = true;"
                                        onclick="this.select()"
                                        placeholder="Ubigeo de Llegada"
                                        wire:model.debounce.400ms="search_arrival"
                                        autocomplete="off"
                                        class="select-all"
                                        />
                                </div>
                                <div x-show="open" class="col-span-1 pr-2 pt-0 relative w-full">
                                    <ul class="absolute bg-gray-100 shadow-md max-w-full right-2 left-0 z-50" style="max-height: 400px; overflow-y: auto;">
                                        @foreach ($search_list_district2 as $person)
                                            <li wire:click="select_active_arrival('{{ $person["code"] }}')" class="py-2 px-2 hover:bg-blue-100 cursor-pointer">{{ $person["name_complete"]}}</li>
                                        @endforeach
                                    </ul>
                                </div>
                                <x-jet-input-error for="client_id" />
                            </div>
                        </div>
                        <div class="col-span-12">
                            <div class="py-1">
                                <x-jet-label for="final_address" class="font-semibold text-gray-600">Punto de llegada: </x-jet-label>
                                <x-general.input type="text" name="final_address" wire:model.defer="final_address"/>
                                <x-jet-input-error for="final_address" />
                            </div>
                        </div>

                        <div class="col-span-12">
                            <div class="py-1">
                                <x-jet-label for="comment" class="font-semibold text-gray-600">Observacion: </x-jet-label>
                                <x-general.textarea name="comment" wire:model.defer="comment"></x-general.textarea>
                                <x-jet-input-error for="comment" />
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-span-12 lg:col-span-5 shadow-md rounded-md p-3" style="background-color: #f2f5f9">
                    <div class="grid grid-cols-12 gap-2">
                        <div class="col-span-12 lg:col-span-4 max-h-20">
                            <x-jet-label for="barcode">C. Barra: </x-jet-label>
                            <x-general.input type="text" id="barcode" placeholder="Código de Barra" wire:model.defer="barcode" wire:keydown.enter="select_bar_code" onclick="this.select()" />
                            <x-jet-input-error for="barcode" />
                        </div>
                        <div class="col-span-12 lg:col-span-8 max-h-20">
                            <x-jet-label for="name">Producto: </x-jet-label>
                            <x-general.input type="text" name="search_product" placeholder="Producto" wire:model.defer="search_product" wire:keydown.enter="search_products" autocomplete="off" wire:keyup="search_products" />
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
                                            <p class="text-sm text-gray-700 font-bold">{{ $product["name"].' '.$product["model"].' '.$product["presentation"] }}</p>
                                            <div class="lg:block lg:visible">
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
            </div>
            <div class="grid grid-cols-1 my-3 gap-5">
                <div class="col-span-1">
                    <div class="overflow-auto rounded shadow-md mt-3" style="max-height: 650px">
                        <x-jet-input-error for="shopping_car" />
                        <table class="min-w-full divide-y divide-x divide-gray-200">
                            <thead class="bg-gray-200">
                                <tr>
                                    <th colspan="2" class="sticky top-0 py-2 w-9/12 text-center text-xs font-bold bg-blue-900 text-white uppercase tracking-wider">Descripción</th>
                                    <th colspan="1" class="sticky top-0 py-2 w-1/12 text-center text-xs font-bold bg-blue-900 text-white uppercase tracking-wider">Und. Guia</th>
                                    <th colspan="1" class="sticky top-0 py-2 w-1/12 text-center text-xs font-bold bg-blue-900 text-white uppercase tracking-wider">Cant. Guia</th>
                                    <th colspan="1" class="sticky top-0 py-2 w-1/12 text-center text-xs font-bold bg-blue-900 text-white uppercase tracking-wider">Und. Stock</th>
                                    <th colspan="1" class="sticky top-0 py-2 w-1/12 text-center text-xs font-bold bg-blue-900 text-white uppercase tracking-wider">Cant. Stock</th>
                                    <th colspan="2" class="sticky top-0 py-2 w-1/12 text-center text-xs font-bold bg-blue-900 text-white uppercase tracking-wider"></th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @if (count($shopping_car) == 0)
                                <tr>
                                    <td colspan="6" class="px-3 py-2 text-center text-xs text-gray-500"> No ha seleccionado ningún producto. </td>
                                </tr>
                                @else
                                    @foreach ($shopping_car as $key => $item)
                                        <tr>
                                            <td colspan="2" class="px-3 py-2 text-left text-sm text-gray-500"> {{ $item["product_name"] }} </td>
                                            <td class="px-3 py-2 text-center text-xs text-gray-500">
                                                <x-general.select :full="false" :block="false" type="number" class="w-28"
                                                wire:model.defer="shopping_car.{{ $key }}.unit_idguide"
                                                autocomplete="off">
                                                @foreach ($item["units"] as $unit)
                                                    <option value="{{$unit["id"]}}">{{$unit["name"]}}</option>
                                                @endforeach
                                                </x-general.select>
                                            </td>
                                            <td class="px-3 py-2 text-center text-xs text-gray-500">
                                                <x-general.input :full="false" :block="false" :id="'quantityguide_'.$item['product_id']" type="number" class="w-28 text-right" autocomplete="off" wire:model.lazy="shopping_car.{{ $key }}.quantityguide"/>
                                                <x-jet-input-error for="shopping_car.{{ $key }}.quantityguide" />
                                            </td>
                                            <td colspan="1" class="px-3 py-2 text-center text-xs text-gray-500">
                                                {{ $item["unit_name"] }}
                                            </td>
                                            <td colspan="1" class="px-3 py-2 text-center text-xs text-gray-500">
                                                {{ $item["quantity"] }}
                                            </td>
                                            <td colspan="1" class="px-3 py-2 text-center text-xs text-gray-500">
                                                <button
                                                wire:click="add_product_move({{ $key }})"
                                                wire:key="add-product-move-{{ $item["product_id"] }}"><i class="fas fa-edit text-blue-600 text-base"></i></button>
                                            </td>
                                            <td colspan="1" class="px-3 py-2 text-center text-xs text-gray-500">
                                                <button
                                                wire:click="quit_car_item({{ $key }})"
                                                wire:key="quit-product-car-{{ $item["product_id"] }}"><i class="fas fa-trash-alt text-red-600 text-base"></i></button>
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
                                                            @if ($item["product_type"] == "R" || $item["product_type"] == "O")
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

        </div>
        <div class="mt-4 flex justify-end">
            <x-general.button x-on:click="show=false"  class="bg-gray-200 text-black hover:bg-gray-400 active:bg-gray-500 mx-2" wire:click="back_button">
                <i class="fas fa-arrow-left mr-2" aria-hidden="true"></i> {{ __(' Atrás') }}
            </x-general.button>
            @if ($sale_guide->id)
                <x-general.button class="bg-yellow-500 text-white hover:bg-yellow-600 active:bg-yellow-700 mx-2" wire:click="update"
                    wire:loading.attr="disabled" wire:target="update" wire:key="update-store-document">
                    <i class="fas fa-edit mr-2" aria-hidden="true"></i> {{ __(' Editar') }}
                </x-general.button>
            @else
                <x-general.button class="bg-blue-500 text-white hover:bg-blue-600 active:bg-blue-700 mx-2" wire:click="save"
                    wire:loading.attr="disabled" wire:target="save" wire:key="save-store-document">
                    <i class="fas fa-save mr-2" aria-hidden="true"></i> {{ __(' Guardar') }}
                </x-general.button>
            @endif
        </div>
    </div>
    @if (in_array('create_person', $arr_actions))
        @php
            $person_width = "2xl";
            $modal_supplier_id = "modal-supplier-id";
        @endphp
        <x-jet-dialog-modal wire:model="modal_supplier" :id="$modal_supplier_id" :maxWidth="$person_width">
            <x-slot name="title">
                CREAR PROVEEDOR
            </x-slot>

            <x-slot name="content">
                @livewire('maintenance.person.mant-component', ["person" => $person, "modal" => true, "event_modal" => "toggle-modal-supplier", "role_id" => $role_id])
            </x-slot>
        </x-jet-dialog-modal>
    @endif
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
                @if ($shopping_car[$document_detail_key]["product_type"] == "M" || $shopping_car[$document_detail_key]["product_type"] == "E" || $shopping_car[$document_detail_key]["product_type"] == "C")
                <div class="m-2 p-1 col-span-1 sm:col-span-2 grid grid-cols-1 sm:grid-cols-12">
                    <div class="p-2 col-span-1 sm:col-span-10">
                        <x-jet-label for="product_serie">Serie</x-jet-label>
                        <x-general.input type="text" id="product_serie" wire:model.defer="product_serie" autocomplete="off"/>
                    </div>
                    <div class="p-2 col-span-1 sm:col-span-2 flex items-center mt-0 sm:mt-3">
                        <div class="w-full text-center">
                            <button class="text-green-600 hover:underline" wire:click="add_product_serie"> <i class="fas fa-check"></i> Agregar</button>
                        </div>
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
                @if ($shopping_car[$document_detail_key]["product_type"] == "R" || $shopping_car[$document_detail_key]["product_type"] == "O")

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
                            <x-jet-label for="search_lot">Buscar Lote</x-jet-label>
                            <x-general.input type="text" id="search_lot" wire:model.defer="search_lot" autocomplete="off"/>
                        </div>
                        <div class="p-2 col-span-1 sm:col-span-2 flex items-center mt-0 sm:mt-3">
                            <div class="w-full text-center">
                                <button class="text-green-600 hover:underline" wire:click="search_product_lot"> <i class="fas fa-search"></i> Buscar</button>
                            </div>
                        </div>
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
