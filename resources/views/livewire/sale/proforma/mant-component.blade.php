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
                        <p class="text-lg font-bold"> PROFORMA</p>
                        <p class="text-base font-bold">RUC: {{ $branch_office["company_ruc"] }}</p>
                        <p class="text-base font-bold"> {{ $number }} </p>
                    </div>
                </div>
            </div>
            <div class="grid grid-cols-12 gap-4">
                <div class="col-span-12 lg:col-span-7 shadow-md rounded-md p-3" style="background-color: #f2f5f9">
                    <div class="grid grid-cols-12">
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
                        <div class="col-span-12 grid grid-cols-12 gap-2">
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
                                    <x-jet-label for="validez" class="font-semibold text-gray-600"> Validez Oferta: </x-jet-label>
                                    <x-general.input type="text" id="validez" wire:model.defer="validez" />
                                    <x-jet-input-error for="validez" />
                                </div>
                            </div>
                            <div class="col-span-12 sm:col-span-4">
                                <div class="py-1">
                                    <x-jet-label for="condicion" class="font-semibold text-gray-600"> Cond. Pago: </x-jet-label>
                                    <x-general.input type="text" id="condicion" wire:model.defer="condicion" />
                                    <x-jet-input-error for="condicion" />
                                </div>
                            </div>
                        </div>
                        <div class="col-span-12">
                            <div wire:ignore   class="py-1">
                                <x-jet-label for="comment" class="font-semibold text-gray-600">Comentario: </x-jet-label>
                                <div id="classic-editor">
                                    {!! $comment !!}
                                </div>
                                <x-general.textarea class="hidden" id="comment" wire:model.defer="comment"></x-general.textarea>
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
                            <x-general.input type="text" name="search_product" placeholder="Producto" wire:model.defer="search_product" wire:keyup="search_products" wire:keydown.enter="search_products" />
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
                                            <p class="text-sm text-gray-700 font-bold">{{ $product["name"].' '.$product["presentation"] }}</p>
                                            <div class="lg:block lg:visible">
                                                <p class="pl-1"><span class="font-bold">CÓDIGO: </span><span>{{ $product["code"] }}</span></p>
                                                <p class="pl-1"><span class="font-bold">CATEGORÍA: </span><span>{{ $product["category_name"] }}</span></p>
                                                {{-- <p class="pl-1"><span class="font-bold">DISTRIBUIDOR: </span><span>{{ $product["supplier_name"] }}</span></p> --}}
                                                <p class="pl-1"><span class="font-bold">MARCA: </span><span>{{ $product["brand_name"] }}</span></p>
                                                @if($product["type"]=="M")
                                                <p class="pl-1"><span class="font-bold">MODELO: </span><span>{{ $product["model"] }}</span></p>
                                                @endif
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
                                    <th colspan="1" class="sticky top-0 py-2 w-7/12 text-center text-xs font-bold bg-blue-900 text-white uppercase tracking-wider">F.V.</th>
                                    <th colspan="1" class="sticky top-0 py-2 w-7/12 text-center text-xs font-bold bg-blue-900 text-white uppercase tracking-wider">Plazo de Entrega</th>
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
                                            <td class="px-3 py-2 text-left text-sm text-gray-500"> {{ $item["product_name"].' '.$item['presentation'] }} </td>
                                            <td class="px-3 py-2 text-center text-xs text-gray-500">
                                                <x-general.select :full="false" :block="false" type="number" class="w-28"
                                                wire:model.defer="shopping_car.{{ $key }}.unit_id"
                                                autocomplete="off">
                                                @foreach ($item["units"] as $unit)
                                                    <option value="{{$unit["id"]}}">{{$unit["name"]}}</option>
                                                @endforeach
                                                </x-general.select>
                                            </td>
                                            @if(count($item["due_dates"])>0)
                                                <td class="px-3 py-2 text-center text-xs text-gray-500">
                                                    <x-general.select :full="false" :block="false" type="number" class="w-28"
                                                    wire:model.defer="shopping_car.{{ $key }}.due_date"
                                                    autocomplete="off">
                                                    @foreach ($item["due_dates"] as $unit)
                                                        <option value="{{$unit["due_date"]}}">{{$unit["due_date"]}}</option>
                                                    @endforeach
                                                    </x-general.select>
                                                </td>
                                            @else
                                                <td class="px-3 py-2 text-center text-xs text-gray-500">
                                                    <x-general.input :full="false" :block="false" :id="'due_date_'.$item['product_id']" type="date" class="w-28 text-right" autocomplete="off" wire:model.lazy="shopping_car.{{ $key }}.due_date"  />
                                                <x-jet-input-error for="shopping_car.{{ $key }}.due_date" />
                                                </td>
                                            @endif
                                            <td class="px-3 py-2 text-center text-xs text-gray-500">
                                                <x-general.input :full="false" :block="false" :id="'delivery_time_'.$item['product_id']" type="text" class="w-28 text-right" autocomplete="off" wire:model.lazy="shopping_car.{{ $key }}.delivery_time"  />
                                                <x-jet-input-error for="shopping_car.{{ $key }}.delivery_time" />
                                            </td>
                                            <td class="px-3 py-2 text-center text-xs text-gray-500">
                                                <x-general.input :full="false" :block="false" :id="'quantity_'.$item['product_id']" type="number" class="w-28 text-right" autocomplete="off" wire:model.lazy="shopping_car.{{ $key }}.quantity"  />
                                                <x-jet-input-error for="shopping_car.{{ $key }}.quantity" />
                                            </td>
                                            <td class="px-3 py-2 text-center text-xs text-gray-500">
                                                <x-general.input :full="false" :block="false" type="number" class="w-28 text-right" autocomplete="off" wire:model.lazy="shopping_car.{{ $key }}.sell_price"  min="{{$item['sell_price_min']}}" />
                                                <i class="fas fa-question text-red-600 text-base" style="float:inline-end;margin-top:-30px;margin-left: 130px;" title="Min: {{$item['sell_price_min']}} - Max: {{$item['sell_price']}}"></i>
                                                <x-jet-input-error for="shopping_car.{{ $key }}.sell_price" />
                                            </td>
                                            <td class="px-3 py-2 text-right text-xs text-gray-500">
                                                {{ $currency["symbol"] . " " . number_format($item["sell_price"] * $item["quantity"], 2) }}
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
                                    <td colspan="6" class="px-3 py-2 text-right text-lg text-gray-500 font-bold">TOTAL ({{ $currency["symbol"] }})</td>
                                    <td colspan="1" class="px-3 py-2 text-right text-lg text-gray-500 min-w-max">
                                        {{ number_format($total, 2) }}
                                    </td>
                                    <td colspan="1"></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

        </div>
        <div class="mt-4 flex justify-end">
            <x-general.button x-on:click="show=false"  class="bg-gray-200 text-black hover:bg-gray-400 active:bg-gray-500 mx-2" wire:click="back_button">
                <i class="fas fa-arrow-left mr-2" aria-hidden="true"></i> {{ __(' Atrás') }}
            </x-general.button>
            @if ($proforma->id)
                <x-general.button class="bg-yellow-500 text-white hover:bg-yellow-600 active:bg-yellow-700 mx-2" wire:click="update(document.querySelector('#comment').value)"
                    wire:loading.attr="disabled" wire:target="update" wire:key="update-proforma">
                    <i class="fas fa-edit mr-2" aria-hidden="true"></i> {{ __(' Editar') }}
                </x-general.button>
            @else
                <x-general.button class="bg-blue-500 text-white hover:bg-blue-600 active:bg-blue-700 mx-2" wire:click="save(document.querySelector('#comment').value)"
                    wire:loading.attr="disabled" wire:target="save" wire:key="save-proforma">
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

    @push("js")
        <!-- ckeditor -->
        <script src="{{ asset('assets/libs/@ckeditor/ckeditor5-build-classic/build/ckeditor.js') }}"></script>

        <script>
            document.addEventListener('livewire:load', function () {
                ClassicEditor
                .create(
                    document.querySelector( '#classic-editor' ),
                    {
                        toolbar: [ 'Bold', 'italic', 'link', 'blockQuote', 'insertTable', 'undo', 'redo' ]
                    }
                )
                .then(
                    editor => {
                        editor.model.document.on('change:data', () => {
                            document.querySelector('#comment').value = editor.getData()
                        })
                    }
                )
                .catch( error => {
                    console.error( error );
                } );
            })
        </script>
    @endpush
</div>
