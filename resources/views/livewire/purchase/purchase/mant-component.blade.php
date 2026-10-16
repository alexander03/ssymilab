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
                        <p class="text-base mb-2"> <x-general.input class="text-center" type="date" id="date_time" wire:model.defer="date_time"/> </p>
                        <p class="text-lg font-bold"> {{ $document_type_name }}</p>
                        {{-- <p class="text-base font-bold">RUC: {{ $branch_office["company_ruc"] }}</p> --}}
                        <p class="text-base font-bold"> {{ ($number && $number != "") ? $number : "-" }} </p>
                    </div>
                </div>
            </div>
            <div class="grid grid-cols-12 gap-4">
                <div class="col-span-12 lg:col-span-7 shadow-md rounded-md p-3" style="background-color: #f2f5f9">
                    <div class="grid grid-cols-12">
                        <div class="col-span-12 grid grid-cols-12 gap-2">
                            <div class="col-span-12 sm:col-span-4">
                                <div class="py-1">
                                    <x-jet-label for="document_type_id" class="font-semibold text-gray-600">Tipo Doc.: </x-jet-label>
                                    <x-general.select name="document_type_id" wire:model="document_type_id">
                                        @foreach ($document_types as $item)
                                            <option value="{{ $item->id }}"> {{ $item->name }} </option>
                                        @endforeach
                                    </x-general.select>
                                    <x-jet-input-error for="document_type_id" />
                                </div>
                            </div>
                            <div class="col-span-12 sm:col-span-4">
                                <div class="py-1">
                                    <x-jet-label for="number" class="font-semibold text-gray-600">Número: </x-jet-label>
                                    <x-general.input type="text" name="number" wire:model.lazy="number"/>
                                    <x-jet-input-error for="number" />
                                </div>
                            </div>
                            <div class="col-span-12 sm:col-span-4">
                                <div x-data="{ open_requirement: false }" class="py-1 pr-1">
                                    <div class="grid grid-cols-12">
                                        <div @click.away="open_requirement = false;" class="pb-0 col-span-11">
                                            <x-jet-label for="search_document" class="font-semibold text-gray-600">Requerimiento: </x-jet-label>
                                            @if ($document_id)
                                            <x-general.input type="text" disabled name="search_document"
                                                x-on:click="open_requirement = true;"
                                                x-on:keyup="open_requirement = true;"
                                                onclick="this.select()"
                                                placeholder="Requerimiento"
                                                wire:model.debounce.400ms="search_document"
                                                autocomplete="off"
                                                class="select-all"
                                                wire:key="disabled-search-document"
                                            />
                                            @else
                                            <x-general.input type="text" name="search_document"
                                                x-on:click="open_requirement = true;"
                                                x-on:keyup="open_requirement = true;"
                                                onclick="this.select()"
                                                placeholder="Requerimiento"
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
                                    <div x-show="open_requirement" class="pt-0 relative w-full">
                                        <ul class="absolute bg-gray-100 shadow-md max-w-full right-4 left-0 z-50">
                                            @foreach ($search_list_document as $document)
                                                <li wire:click="select_active_document('{{ $document["id"] }}')" class="py-2 px-2 hover:bg-blue-100 cursor-pointer">{{ $document["number"] }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                    <x-jet-input-error for="search_document" />
                                </div>
                            </div>
                        </div>
                        <div class="col-span-12 sm:col-span-9">
                            <div x-data="{ open: false }">
                                <div @click.away="open = false" class="py-2 sm:mr-2 col-span-1 pb-0">
                                    <div class="flex justify-between">
                                        <x-jet-label for="search_person" class="font-semibold text-gray-600">Proveedor: </x-jet-label>
                                        @if (in_array('create_person', $arr_actions))
                                            <button type="button" class="text-blue-500 hover:underline" wire:click="toggle_supplier">Nuevo</button>
                                        @endif
                                    </div>
                                    <x-general.input type="text" id="search_person"
                                        x-on:click="open = true;"
                                        x-on:keyup="open = true;"
                                        onclick="this.select()"
                                        placeholder="Proveedor"
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
                        <div class="col-span-12 grid grid-cols-12 gap-2">
                            <div class="col-span-12 sm:col-span-4">
                                <div class="py-1">
                                    <x-jet-label for="schedule_time" class="font-semibold text-gray-600">Entrega: </x-jet-label>
                                    <x-general.input type="date" name="schedule_time" wire:model.defer="schedule_time"/>
                                    <x-jet-input-error for="schedule_time" />
                                </div>
                            </div>
                            <div class="col-span-12 sm:col-span-4">
                                <div class="py-1">
                                    <x-jet-label for="currency_id" class="font-semibold text-gray-600">Moneda: </x-jet-label>
                                    <x-general.select name="currency_id" wire:model="currency_id">
                                        @foreach ($currencies as $item)
                                            <option value="{{ $item->id }}"> {{ $item->name }} </option>
                                        @endforeach
                                    </x-general.select>
                                    <x-jet-input-error for="currency_id" />
                                </div>
                            </div>
                            <div class="col-span-12 sm:col-span-4">
                                <div class="py-1">
                                    <x-jet-label for="exchange_rate" class="font-semibold text-gray-600">T. Cambio: </x-jet-label>
                                    <x-general.input type="number" name="exchange_rate" wire:model.defer="exchange_rate"/>
                                    <x-jet-input-error for="exchange_rate" />
                                </div>
                            </div>
                        </div>
                        <div class="col-span-12">
                            <div class="py-1">
                                <x-jet-label for="comment" class="font-semibold text-gray-600">Comentario: </x-jet-label>
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
                            <x-general.input type="text" name="search_product" placeholder="Producto" wire:model.defer="search_product" wire:keydown.enter="search_products" wire:keydown="search_products" autocomplete="off" />
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
                                            <p class="text-sm text-gray-700 font-bold">{{ $product["name"].' '.$product["model"].' '.$product["presentation"].' '.$product["brand_name"] }}</p>
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
                    <div class="overflow-auto rounded shadow-md mt-3" style="max-height: 500px">
                        <x-jet-input-error for="shopping_car" />
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-200">
                                <tr>
                                    <th colspan="1" class="sticky top-0 py-2 w-7/12 text-center text-xs font-bold bg-blue-900 text-white uppercase tracking-wider">Descripción</th>
                                    <th colspan="1" class="sticky top-0 py-2 w-1/12 text-center text-xs font-bold bg-blue-900 text-white uppercase tracking-wider">Und.</th>
                                    <th colspan="1" class="sticky top-0 py-2 w-1/12 text-center text-xs font-bold bg-blue-900 text-white uppercase tracking-wider">Cant.</th>
                                    <th colspan="1" class="sticky top-0 py-2 w-1/12 text-center text-xs font-bold bg-blue-900 text-white uppercase tracking-wider">P. Unit</th>
                                    <th colspan="1" class="sticky top-0 py-2 w-1/12 text-right text-xs font-bold bg-blue-900 text-white uppercase tracking-wider">Subt.</th>
                                    <th colspan="1" class="sticky top-0 py-2 w-1/12 text-center text-xs font-bold bg-blue-900 text-white uppercase tracking-wider"></th>
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
                                            <td class="px-3 py-2 text-left text-sm text-gray-500"> {{ $item["product_name"] }} </td>
                                            <td class="px-3 py-2 text-center text-xs text-gray-500">
                                                <x-general.select :full="false" :block="false" type="number" class="w-28"
                                                wire:model.defer="shopping_car.{{ $key }}.unit_id"
                                                autocomplete="off">
                                                @foreach ($item["units"] as $unit)
                                                    <option value="{{$unit["id"]}}">{{$unit["name"]}}</option>
                                                @endforeach
                                                </x-general.select>
                                            </td>
                                            <td class="px-3 py-2 text-center text-xs text-gray-500">
                                                <x-general.input :full="false" :block="false" :id="'quantity_'.$item['product_id']" type="number" class="w-28 text-right" autocomplete="off" wire:model.lazy="shopping_car.{{ $key }}.quantity"  />
                                                <x-jet-input-error for="shopping_car.{{ $key }}.quantity" />
                                            </td>
                                            <td class="px-3 py-2 text-center text-xs text-gray-500">
                                                <x-general.input :full="false" :block="false" type="number" class="w-28 text-right" autocomplete="off" wire:model.lazy="shopping_car.{{ $key }}.purchase_price"  />
                                                <x-jet-input-error for="shopping_car.{{ $key }}.purchase_price" />
                                            </td>
                                            <td class="px-3 py-2 text-right text-xs text-gray-500">
                                                {{ $currency["symbol"] . " " . number_format($item["purchase_price"] * $item["quantity"], 2) }}
                                            </td>
                                            <td class="px-3 py-2 text-center text-xs text-gray-500">
                                                <button
                                                wire:click="quit_car_item({{ $key }})"
                                                wire:key="quit-product-unit-{{ $item["product_id"] }}"><i class="fas fa-trash-alt text-red-600 text-base"></i></button>
                                            </td>
                                        </tr>
                                    @endforeach
                                    @endif
                            </tbody>
                            <tfoot class="bg-white divide-y divide-gray-200">
                                <tr>
                                    <td colspan="4" class="px-3 py-2 text-right text-lg text-gray-500 font-bold">OTROS ({{ $currency["symbol"] }})</td>
                                    <td colspan="1" class="px-3 py-2 text-right text-lg text-gray-500 min-w-max">
                                        <x-general.input :full="false" :block="false" type="number" class="w-28 text-right" autocomplete="off" wire:model.lazy="other" name="other" />
                                    </td>
                                    <td colspan="1"></td>
                                </tr>
                                <tr>
                                    <td colspan="4" class="px-3 py-2 text-right text-lg text-gray-500 font-bold">FLETE ({{ $currency["symbol"] }})</td>
                                    <td colspan="1" class="px-3 py-2 text-right text-lg text-gray-500 min-w-max">
                                        <x-general.input :full="false" :block="false" type="number" class="w-28 text-right" autocomplete="off" wire:model.lazy="flete" name="flete"  />
                                    </td>
                                    <td colspan="1"></td>
                                </tr>
                                <tr>
                                    <td colspan="4" class="px-3 py-2 text-right text-lg text-gray-500 font-bold">SEGURO ({{ $currency["symbol"] }})</td>
                                    <td colspan="1" class="px-3 py-2 text-right text-lg text-gray-500 min-w-max">
                                        <x-general.input :full="false" :block="false" type="number" class="w-28 text-right" autocomplete="off" wire:model.lazy="seguro" name="seguro" />
                                    </td>
                                    <td colspan="1"></td>
                                </tr>
                                <tr>
                                    <td colspan="4" class="px-3 py-2 text-right text-lg text-gray-500 font-bold">TOTAL ({{ $currency["symbol"] }})</td>
                                    <td colspan="1" class="px-3 py-2 text-right text-lg text-gray-500 min-w-max">
                                        {{ number_format($total, 2) }}
                                    </td>
                                    <td colspan="1"></td>
                                </tr>
                                @if ($type == "D")
                                    <tr>
                                        <td colspan="4" class="px-3 py-2 text-right text-md text-gray-500 font-bold">PENDIENTE DE PAGO ({{ $currency["symbol"] }})</td>
                                        <td colspan="1" class="px-3 py-2 text-right text-md text-gray-500 min-w-max">
                                            {{ number_format($pending_amount, 2) }}
                                            <x-jet-input-error for="pending_amount" />
                                        </td>
                                        <td colspan="1" class="px-3 py-2 text-right text-md text-gray-500 font-bold">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="1"></td>
                                        <td colspan="5" class="px-3 py-2 text-right text-md text-gray-500 min-w-max">
                                            <table class="min-w-full divide-y divide-gray-200 uppercase">
                                                <thead>
                                                    <tr>
                                                        <th class="px-3 py-2 text-center text-xs text-gray-500">Cuota</th>
                                                        <th class="px-3 py-2 text-center text-xs text-gray-500">Monto ({{ $currency["symbol"] }})</th>
                                                        <th class="px-3 py-2 text-center text-xs text-gray-500">Fecha de pago</th>
                                                        <th class="px-3 py-2 text-center text-xs text-gray-500"><button wire:click="add_document_payment"><i class="fas fa-plus"></i></button></th>
                                                    </tr>
                                                </thead>
                                                <tbody class="divide-y divide-gray-200">
                                                    <tr>
                                                        <td class="px-3 py-2 text-center text-xs text-gray-500">PAGO INICIAL</td>
                                                        <td class="px-3 py-2 text-center text-xs text-gray-500">{{ number_format($paided_amount, 2) }}</td>
                                                        <td class="px-3 py-2 text-center text-xs text-gray-500">{{ date_format(new DateTime($date_time), 'd/m/Y') }}</td>
                                                        <td class="px-3 py-2 text-center text-xs text-gray-500"></td>
                                                    </tr>
                                                    @foreach ($document_payments as $key => $document_payment)
                                                    <tr>
                                                        <td class="px-3 py-2 text-center text-xs text-gray-500">{{ $document_payment["payment_name"] }}</td>
                                                        <td class="px-3 py-2 text-center text-xs text-gray-500">
                                                            <x-general.input :full="false" :block="false" :id="'installment_total_amount_'.$key" type="number" class="w-32 text-right" autocomplete="off" wire:model.lazy="document_payments.{{ $key }}.total_amount"  />
                                                            <x-jet-input-error for="document_payments.{{ $key }}.total_amount" />
                                                        </td>
                                                        <td class="px-3 py-2 text-center text-xs text-gray-500">
                                                            <x-general.input :full="false" :block="false" :id="'installment_schedule_date_'.$key" type="date" class="w-40 text-right" autocomplete="off" wire:model.lazy="document_payments.{{ $key }}.schedule_date"  />
                                                            <x-jet-input-error for="document_payments.{{ $key }}.schedule_date" />
                                                        </td>
                                                        <td class="px-3 py-2 text-center text-xs text-gray-500"><button wire:key="quit-installments-{{$key}}" wire:click="quit_installments({{$key}})"><i class="fas fa-trash"></i></button></td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </td>
                                    </tr>
                                @endif
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
            <div class="grid grid-cols-12">
                <div class="order-last xl:order-first col-span-12 xl:col-span-6">
                @if ($imageUploaded)
                    <div class="col-span-1">
                        Previsualizacion:
                        <img src="{{ route('purchase-file', ["id" => $purchase->id]) }}" height="100px">
                    </div>
                    <div class="p-2 col-span-1">
                        <x-general.button class="bg-red-400 text-white hover:bg-red-600 active:bg-red-700 my-4" onclick="clearImageUploaded()">
                            <i class="fas fa-trash mr-2" aria-hidden="true"></i> {{ __(' Eliminar') }}
                        </x-general.button>
                    </div>
                @else
                    <div wire:loading wire:target="image" wire:loading.class="text-gray-400">
                        Cargando imagen ...
                    </div>
                    {{-- @if ($image && str_contains($image->getMimeType(), 'image') )
                    <div wire:loading.remove wire:target="image">
                        <div class="col-span-1">
                            Previsualizacion:
                        </div>
                    </div>
                    @endif --}}

                    @if ($image && str_contains($image->getMimeType(), 'image') )
                    <div class="p-2 col-span-1">
                    @else
                    <div class="p-2 col-span-1 md:col-span-2">
                    @endif
                        <label for="image" class="font-semibold text-gray-600">Imagen: </label>
                        <x-general.input type="file" id="image" wire:model="image" accept="image/*" />
                        <x-jet-input-error for="image" />
                        @error('image') <script>clearImage();</script> @enderror
                    </div>
                    @if ($image && str_contains($image->getMimeType(), 'image') )
                    <div class="p-2 col-span-1 mt-3">
                        <x-general.button class="bg-red-400 text-white hover:bg-red-600 active:bg-red-700 my-4" onclick="clearImage()">
                            <i class="fas fa-trash mr-2" aria-hidden="true"></i> {{ __(' Eliminar') }}
                        </x-general.button>
                    </div>
                    @endif
                @endif
                </div>
                <div class="col-span-12 xl:col-span-6 divide-y divide-gray-200 grid grid-cols-1">
                    <div class="overflow-x-auto divide-gray-200 divide-y">
                        <x-jet-input-error for="payments" />
                        <div class="grid justify-items-end w-full pr-6 md:pr-11">
                            <div class="grid sm:flex items-center p-2 ">
                                <x-jet-label for="barcode" class="text-xs min-w-max mt-1 mr-3">FORMA PAGO: </x-jet-label>
                                <x-general.select name="type" wire:model="type" :full="false" :block="false" class="w-52 mt-0">
                                    <option value="C"> CONTADO </option>
                                    <option value="D"> CREDITO </option>
                                </x-general.select>
                            </div>
                        </div>
                        @foreach ($payments as $key => $item)
                        <div class="grid justify-items-end w-full md:pr-5">
                            <div class="grid sm:flex items-center p-2">
                                <x-jet-label for="barcode" class="text-xs min-w-max mt-1 mr-3">{{ $item["name"] }}: </x-jet-label>
                                <div class="flex">
                                    <x-general.input type="number" wire:model.lazy="payments.{{ $key }}.amount" :full="false" :block="false" class="w-30 mt-0 text-right"/>
                                    @if($item["name"]!="EFECTIVO")
                                    <x-general.input type="date" wire:model.lazy="payments.{{ $key }}.date" :full="false" :block="false" class="w-30 mt-0 text-right"/>
                                    <x-general.input type="text" wire:model.lazy="payments.{{ $key }}.comment" :full="false" :block="false" class="w-30 mt-0 text-right" placeholder="Nro OP."/>
                                    @endif
                                    <button class="ml-3" wire:click="quit_method_item('{{ $key }}')" wire:loading.attr="disabled" wire:target="quit_method_item('{{ $key }}')"
                                            wire:key="quit-payment-{{ $key }}"><i class="fas fa-times text-red-600 text-base"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <div class="grid justify-items-end w-full pr-6 md:pr-11 p-2">
                        <div x-data="{ open_method: false }">
                            <div x-show="open_method" class="col-span-1 pr-2 pt-0 relative w-full">
                                <ul class="absolute bg-gray-100 shadow-md max-w-full right-2 left-0 z-50 bottom-full">
                                    @foreach ($payment_methods as $key => $payment_method)
                                        <li wire:click="select_payment_method('{{ $key }}')"  class="py-2 px-2 hover:bg-blue-100 cursor-pointer text-center">{{ $payment_method["name"]}}</li>
                                    @endforeach
                                </ul>
                            </div>
                            <div @click.away="open_method = false" class="py-2 sm:mr-2 col-span-1">
                                <x-general.button class="bg-blue-300 text-white hover:bg-blue-400 active:bg-blue-500 mx-2"
                                x-on:click="open_method = !open_method;"><i class="fas fa-plus mr-2" aria-hidden="true"></i> Método de pago </x-general.button>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
        <div class="mt-4 flex justify-end">
            <x-general.button x-on:click="show=false"  class="bg-gray-200 text-black hover:bg-gray-400 active:bg-gray-500 mx-2" wire:click="back_button">
                <i class="fas fa-arrow-left mr-2" aria-hidden="true"></i> {{ __(' Atrás') }}
            </x-general.button>
            @if ($purchase->id)
                <x-general.button class="bg-yellow-500 text-white hover:bg-yellow-600 active:bg-yellow-700 mx-2" wire:click="update"
                    wire:loading.attr="disabled" wire:target="update" wire:key="update-purchase">
                    <i class="fas fa-edit mr-2" aria-hidden="true"></i> {{ __(' Editar') }}
                </x-general.button>
            @else
                <x-general.button class="bg-blue-500 text-white hover:bg-blue-600 active:bg-blue-700 mx-2" wire:click="save"
                    wire:loading.attr="disabled" wire:target="save" wire:key="save-purchase">
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
</div>
@push("js")
<script>
function clearImage(){
    document.getElementById("image").value = "";
    @this.set('image', null);
}
function clearImageUploaded(){
    @this.set('imageUploaded', null);
}
</script>
@endpush