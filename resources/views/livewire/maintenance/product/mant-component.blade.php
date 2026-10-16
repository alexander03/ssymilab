<div>
    @if (!$modal)
        <div class="bg-white rounded-md py-8 px-8 shadow-md mb-2">
    @endif
        <div x-data="{ type: @entangle('type').defer }">
            <div class="grid grid-cols-1 sm:grid-cols-2">
                <span class="text-base font-bold mt-2 col-span-1 sm:col-span-2"> Detalles del Producto: </span>

                @if ($imageUploaded)
                    <div class="col-span-1">
                        Previsualizacion:
                        <img src="{{ route('product-file', ["id" => $product->id]) }}" height="100px">
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

                <div class="p-2 col-span-1">
                    <label for="type" class="font-semibold text-gray-600">Tipo: </label>
                    <x-general.select :disabled="($product->id)" id="type" wire:model="type" x-model="type">
                        @foreach ($types as $key => $item)
                            <option value="{{ $key }}"> {{ $item }} </option>
                        @endforeach
                    </x-general.select>
                    <x-jet-input-error for="type" />
                </div>

                <div class="p-2 col-span-1">
                    <div class="flex justify-between">
                        <label for="category_id" class="font-semibold text-gray-600">Categoría: </label>
                        @if (in_array('create_category', $arr_actions))
                            <button type="button" class="text-blue-500 hover:underline" wire:click="toggle_category">Nuevo</button>
                        @endif
                    </div>
                    <x-general.select id="category_id" wire:model.defer="category_id">
                        @foreach ($categories as $item)
                            <option value="{{ $item->id }}"> {{ $item->name }} </option>
                        @endforeach
                    </x-general.select>
                    <x-jet-input-error for="category_id" />
                </div>

                <div class="p-2 col-span-1">
                    <div class="flex justify-between">
                        <label for="brand_id" class="font-semibold text-gray-600">Marca: </label>
                        @if (in_array('create_brand', $arr_actions))
                            <button type="button" class="text-blue-500 hover:underline" wire:click="toggle_brand">Nuevo</button>
                        @endif
                    </div>
                    <x-general.select id="brand_id" class="select2"  wire:model.defer="brand_id">
                        @foreach ($brands as $item)
                            <option value="{{ $item->id }}" @if ($item->id == $brand_id) selected @endif > {{ $item->name }} </option>
                        @endforeach
                    </x-general.select>
                    <x-jet-input-error for="brand_id" />
                </div>

                <div class="p-2 col-span-1">
                    <div class="flex justify-between">
                        <label for="supplier_id" class="font-semibold text-gray-600">Proveedor: </label>
                        @if (in_array('create_person', $arr_actions))
                            <button type="button" class="text-blue-500 hover:underline" wire:click="toggle_supplier">Nuevo</button>
                        @endif
                    </div>
                    <x-general.select id="supplier_id" class="select2" wire:model.defer="supplier_id">
                        @foreach ($suppliers as $item)
                            <option value="{{ $item->id }}" @if ($item->id == $supplier_id) selected @endif > {{ $item->getNameComplete() }} </option>
                        @endforeach
                    </x-general.select>
                    <x-jet-input-error for="supplier_id" />
                </div>

                <div class="p-2 col-span-1">
                    <label for="code">Código: </label>
                    <x-general.input type="text" id="code" placeholder="Código" wire:model.defer="code" readonly="" />
                    <x-jet-input-error for="code" />
                </div>

                <div class="p-2 col-span-1">
                    <label for="barcode">Código de Barra: </label>
                    <x-general.input type="text" id="barcode" placeholder="Código de Barra" wire:model.defer="barcode" />
                    <x-jet-input-error for="barcode" />
                </div>

                <div class="p-2 col-span-1 sm:col-span-2">
                    <label for="name">Nombre: </label>
                    <x-general.input type="text" id="name" placeholder="Nombre" wire:model.defer="name" />
                    <x-jet-input-error for="name" />
                </div>

                <div x-show="type!='S'" wire:ignore  class="p-2 col-span-1 sm:col-span-2">
                    <label for="specifications">Especificaciones: </label>
                    <div id="classic-editor">
                        {!! $specifications !!}
                    </div>
                </div>
                <x-general.textarea class="hidden" id="specifications" placeholder="Especificaciones" wire:model.defer="specifications"></x-general.textarea>

                <div x-show="type=='R' || type=='O' || type=='M'" class="p-2 col-span-1">
                    <label for="presentation">Presentación: </label>
                    <x-general.input type="text" id="presentation" placeholder="Presentación" wire:model.defer="presentation" />
                    <x-jet-input-error for="presentation" />
                </div>
                <div x-show="type=='R'" class="p-2 col-span-1">
                    <label for="condition">Cond. Almacenamiento: </label>
                    <x-general.input type="text" id="condition" placeholder="Cond. Almacenamiento" wire:model.defer="condition" />
                    <x-jet-input-error for="condition" />
                </div>
                <div x-show="type=='R'" class="p-2 col-span-1">
                    <label for="rs">R.S.: </label>
                    <x-general.input type="text" id="rs" placeholder="R.S." wire:model.defer="rs" />
                    <x-jet-input-error for="rs" />
                </div>
                <div x-show="type=='M' || type=='E' || type=='C' || type=='O' " class="p-2 col-span-1">
                    <label for="model">Modelo: </label>
                    <x-general.input type="text" id="model" placeholder="Modelo" wire:model.defer="model" />
                    <x-jet-input-error for="model" />
                </div>
                <div class="p-2 col-span-1">
                    <label for="source">Procediencia: </label>
                    <x-general.input type="text" id="source" placeholder="Procediencia" wire:model.defer="source" />
                    <x-jet-input-error for="source" />
                </div>
            </div>
            <div x-show="type=='Z'" >
                <div  x-data="{ open: false }" class="grid grid-cols-1 mt-0">
                    <div @click.away="open = false" class="p-2 col-span-1 pb-0">
                        <div class="flex justify-between">
                            <label for="search_ingredient" class="font-semibold text-gray-600">Principio Activo: </label>
                            @if (in_array('create_active_ingredient', $arr_actions))
                                <button type="button" class="text-blue-500 hover:underline" wire:click="toggle_active_ingredient">Nuevo</button>
                            @endif
                        </div>
                        <x-general.input type="text" id="search_ingredient"
                            x-on:click="open = true"
                            placeholder="Principio Activo"
                            wire:model="search_ingredient"
                            autocomplete="off"
                            />
                        <x-jet-input-error for="search_ingredient" />
                    </div>
                    <div x-show="open" class="col-span-1 px-2 pt-0 relative w-full">
                        <ul class="absolute bg-gray-100 shadow-md max-w-full right-2 left-2">
                            @foreach ($search_list_ingredient as $ingredient)
                                <li wire:click="select_active_ingredient('{{ $ingredient->id }}')" class="py-2 px-2 hover:bg-blue-100 cursor-pointer">{{ $ingredient->name}}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                <div class="grid grid-cols-1 p-2">
                    <table class="min-w-full divide-y divide-gray-200 shadow">
                        <thead class="bg-gray-200">
                            <tr>
                                <th colspan="2" class="py-1 text-center text-xs font-bold text-gray-600 uppercase tracking-wider">Agregados</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @if (count($active_ingredients) == 0)
                                <tr>
                                    <td colspan="2" class="px-3 py-2 text-center text-xs text-gray-500"> No ha agregado ningún registro</td>
                                </tr>
                            @endif
                            @foreach ($active_ingredients as $item)
                                <tr>
                                    <td class="px-3 py-2 text-center text-xs text-gray-500">{{ $item["name"] }}</td>
                                    <td class="px-3 py-2 text-center text-sm text-gray-500"><button
                                        wire:click="quit_active_ingredient('{{ $item['id'] }}')"
                                        wire:key="quit-ingredient-{{$item['id']}}"><i class="fas fa-times text-red-600"></i></button></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <x-jet-input-error for="active_ingredients" />
                    <x-jet-input-error for="active_ingredients.*.id" />
                </div>
            </div>
        </div>
        <div x-data="{ affects_stock: @entangle('affects_stock').defer, type: @entangle('type').defer  }" class="grid grid-cols-1">
            <span class="text-base font-bold mt-3"> Detalles Presentaciones: </span>
            <div class="grid grid-cols-1 sm:grid-cols-2">
                <div class="p-2 col-span-1">
                    <div class="flex justify-between">
                        <label for="base_unit_id" class="font-semibold text-gray-600">Unidad Base: </label>
                        @if (in_array('create_unit', $arr_actions) && count($product_units) == 0 && !$product->id)
                            <button type="button" class="text-blue-500 hover:underline" wire:click="toggle_unit(null, 'base_unit')">Nuevo</button>
                        @endif
                    </div>
                    <x-general.select id="base_unit_id" wire:model.defer="base_unit_id"><?php // :disabled="count($product_units)>0 || ($product->id)"  ?>
                        @foreach ($units as $item)
                            <option value="{{ $item->id }}"> {{ $item->name }} </option>
                        @endforeach
                    </x-general.select>
                    <x-jet-input-error for="base_unit_id" />
                </div>

                <div class="p-2 col-span-1">
                    <label for="currency_id" class="font-semibold text-gray-600">Moneda Base: </label>
                    <x-general.select :disabled="count($product_units)>0 || ($product->id)" id="currency_id" wire:model.defer="currency_id">
                        @foreach ($currencies as $item)
                            <option value="{{ $item->id }}"> {{ $item->name }} </option>
                        @endforeach
                    </x-general.select>
                    <x-jet-input-error for="currency_id" />
                </div>

                <div class="p-2 col-span-1" @if($show_purchase_price==false) style="display: none;" @endif>
                    <label for="purchase_price">Precio de Compra: </label>
                    <x-general.input type="number" class="text-right"  id="purchase_price" wire:model.defer="purchase_price" wire:keyup="calcularPrecioVenta"/>
                    <x-jet-input-error for="purchase_price" />
                </div>
                <div class="p-2 col-span-1" @if($show_purchase_price==false) style="display: none;" @endif>
                    <label for="currency2_id" class="font-semibold text-gray-600">Moneda Precio Compra: </label>
                    <x-general.select id="currency2_id" wire:model.defer="currency2_id">
                        @foreach ($currencies as $item)
                            <option value="{{ $item->id }}"> {{ $item->name }} </option>
                        @endforeach
                    </x-general.select>
                    <x-jet-input-error for="currency2_id" />
                </div>

                <div class="p-2 col-span-1">
                    <label for="sell_price">% de Ganancia: </label>
                    <x-general.input type="number" class="text-right" id="win_percentage" wire:model.defer="win_percentage" wire:keyup="calcularPrecioVenta"/>
                    <x-jet-input-error for="win_percentage" />
                </div>

                <div class="p-2 col-span-1">
                    <label for="sell_price">Precio de Venta: </label>
                    <x-general.input type="number" class="text-right" id="sell_price" wire:model.defer="sell_price" />
                    <x-jet-input-error for="sell_price" />
                </div>

                <div class="p-2 col-span-1">
                    <label for="sell_price">% de Ganancia Min: </label>
                    <x-general.input type="number" class="text-right" id="win_percentage_min" wire:model.defer="win_percentage_min" wire:keyup="calcularPrecioVenta"/>
                    <x-jet-input-error for="win_percentage_min" />
                </div>

                <div class="p-2 col-span-1">
                    <label for="sell_price">Precio de Venta Min: </label>
                    <x-general.input type="number" class="text-right" id="sell_price_min" wire:model.defer="sell_price_min" />
                    <x-jet-input-error for="sell_price_min" />
                </div>

                {{-- <div x-bind:class="'p-2 col-span-1 ' + ( affects_stock == 'Y' ? '' : 'sm:col-span-2')">
                    <label for="affects_stock" class="font-semibold text-gray-600">Afecta stock: </label>
                    <x-general.select id="affects_stock" wire:model.defer="affects_stock" x-model="affects_stock">
                        <option value="Y"> CON STOCK </option>
                        <option value="N"> SIN STOCK </option>
                    </x-general.select>
                    <x-jet-input-error for="affects_stock" />
                </div> --}}

                <div class="p-2 col-span-1">
                    <label for="igv" class="font-semibold text-gray-600">Afectación IGV: </label>
                    <x-general.select id="igv" wire:model.defer="igv">
                        <option value="10"> GRAVADA </option>
                        <option value="20"> EXONERADA </option>
                        <option value="30"> INAFECTA </option>
                    </x-general.select>
                    <x-jet-input-error for="igv" />
                </div>

                <div x-show="type!='S'" class="p-2 col-span-1">
                    <label for="security_stock">Stock mínimo: </label>
                    <x-general.input type="number" class="text-right"  id="security_stock" wire:model.defer="security_stock" />
                    <x-jet-input-error for="security_stock" />
                </div>
                <div  x-show="type=='R'" class="m-2 p-1 col-span-1 sm:col-span-2 grid grid-cols-1 sm:grid-cols-12 border-1 border-gray-200 rounded-md">
                    <div class="p-2 col-span-1 sm:col-span-5">
                        <div class="flex justify-between">
                            <label for="presentation_unit_id" class="font-semibold text-gray-600">Nueva Unidad: </label>
                            @if (in_array('create_unit', $arr_actions))
                                <button type="button" class="text-blue-500 hover:underline" wire:click="toggle_unit(null, 'presentation_unit')">Nuevo</button>
                            @endif
                        </div>
                        <x-general.select id="presentation_unit_id" wire:model.defer="presentation_unit_id">
                            @foreach ($units as $item)
                                <option value="{{ $item->id }}"> {{ $item->name }} </option>
                            @endforeach
                        </x-general.select>
                        <x-jet-input-error for="presentation_unit_id" />
                    </div>

                    <div class="p-2 col-span-1 sm:col-span-5">
                        <label for="presentation_currency_id" class="font-semibold text-gray-600">Nueva Moneda: </label>
                        <x-general.select id="presentation_currency_id" wire:model.defer="presentation_currency_id">
                            @foreach ($currencies as $item)
                                <option value="{{ $item->id }}"> {{ $item->name }} </option>
                            @endforeach
                        </x-general.select>
                        <x-jet-input-error for="presentation_currency_id" />
                    </div>
                    <div class="p-2 col-span-1 sm:col-span-2 flex items-center mt-0 sm:mt-3">
                        <div class="w-full text-center">
                            <button class="text-green-600 hover:underline" wire:click="add_product_unit"> <i class="fas fa-check"></i> Agregar</button>
                        </div>
                    </div>
                </div>

                <div  x-show="type=='R'" class="col-span-1 sm:col-span-2 grid grid-cols-1 p-2 overflow-x-scroll">
                    <table class="min-w-full divide-y divide-gray-200 shadow">
                        <thead class="bg-gray-200">
                            <tr>
                                <th class="py-1 text-center text-xs font-bold text-gray-600 uppercase tracking-wider">Unidad</th>
                                <th class="py-1 text-center text-xs font-bold text-gray-600 uppercase tracking-wider">Moneda</th>
                                <th class="py-1 text-center text-xs font-bold text-gray-600 uppercase tracking-wider">Equivale Unidad</th>
                                <th class="py-1 text-center text-xs font-bold text-gray-600 uppercase tracking-wider">Precio</th>
                                <th class="py-1 text-center text-xs font-bold text-gray-600 uppercase tracking-wider"></th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @if (count($product_units) == 0)
                                <tr>
                                    <td colspan="5" class="px-3 py-2 text-center text-xs text-gray-500"> No ha agregado ningúna presentación</td>
                                </tr>
                            @endif
                            @foreach ($product_units as $key => $item)
                                <tr>
                                    <td class="px-3 py-2 text-center text-xs text-gray-500">{{ $item["unit_name"] }}</td>
                                    <td class="px-3 py-2 text-center text-xs text-gray-500">{{ $item["currency_name"] }}</td>
                                    <td align="center" class="px-3 py-2 text-center text-xs text-gray-500">
                                        <x-general.input :full="false" :block="false" type="number" class="w-20" class="text-right" id="formula_{{$key}}" wire:model.defer="product_units.{{$key}}.formula" />
                                        <x-jet-input-error for="product_units.{{$key}}.formula" />
                                    </td>
                                    <td align="center" class="px-3 py-2 text-center text-xs text-gray-500">
                                        <x-general.input :full="false" :block="false" type="number" class="w-20" class="text-right" id="sell_price_{{$key}}" wire:model.defer="product_units.{{$key}}.sell_price" />
                                        <x-jet-input-error for="product_units.{{$key}}.sell_price" />
                                    </td>
                                    <td class="px-3 py-2 text-center text-sm text-gray-500"><button
                                        wire:click="quit_product_unit({{$key}})"
                                        wire:key="quit-product-unit-{{$key}}"><i class="fas fa-times text-red-600"></i></button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <x-jet-input-error for="product_units" />
                    <x-jet-input-error for="product_units.*.unit_id" />
                    <x-jet-input-error for="product_units.*.currency_id" />
                </div>

            </div>
        </div>
        <div class="mt-4 flex justify-end">
            <x-general.button class="bg-gray-200 text-black hover:bg-gray-400 active:bg-gray-500 mx-2" wire:click="back_button">
                <i class="fas fa-arrow-left mr-2" aria-hidden="true"></i> {{ __(' Atrás') }}
            </x-general.button>
            @if ($product->id)
                <x-general.button class="bg-yellow-500 text-white hover:bg-yellow-600 active:bg-yellow-700 mx-2" wire:click="update(document.querySelector('#specifications').value)"
                    wire:loading.attr="disabled" wire:target="update" wire:key="update-product">
                    <i class="fas fa-edit mr-2" aria-hidden="true"></i> {{ __(' Editar') }}
                </x-general.button>
            @else
                <x-general.button class="bg-blue-500 text-white hover:bg-blue-600 active:bg-blue-700 mx-2" wire:click="save(document.querySelector('#specifications').value)"
                    wire:loading.attr="disabled" wire:target="save" wire:key="save-product">
                    <i class="fas fa-save mr-2" aria-hidden="true"></i> {{ __(' Guardar') }}
                </x-general.button>
            @endif

        </div>
    @if (!$modal)
        </div>
    @endif
    @if (in_array('create_category', $arr_actions))
        @php
            $category_width = "lg";
            $modal_category_id = "modal-category-id";
        @endphp
        <x-jet-dialog-modal wire:model="modal_category" :id="$modal_category_id" :maxWidth="$category_width">
            <x-slot name="title">
                CREAR CATEGORIA
            </x-slot>

            <x-slot name="content">
                @livewire('maintenance.category.mant-component', ["category" => $category, "modal" => true, "event_modal" => "toggle-modal-category"])
            </x-slot>
        </x-jet-dialog-modal>
    @endif
    @if (in_array('create_brand', $arr_actions))
        @php
            $brand_width = "lg";
            $modal_brand_id = "modal-brand-id";
        @endphp
        <x-jet-dialog-modal wire:model="modal_brand" :id="$modal_brand_id" :maxWidth="$brand_width">
            <x-slot name="title">
                CREAR MARCA
            </x-slot>

            <x-slot name="content">
                @livewire('maintenance.brand.mant-component', ["brand" => $brand, "modal" => true, "event_modal" => "toggle-modal-brand"])
            </x-slot>
        </x-jet-dialog-modal>
    @endif

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

    @if (in_array('create_active_ingredient', $arr_actions))
        @php
            $person_width = "lg";
            $modal_active_ingredient_id = "modal-ingredient-id";
        @endphp
        <x-jet-dialog-modal wire:model="modal_active_ingredient" :id="$modal_active_ingredient_id" :maxWidth="$person_width">
            <x-slot name="title">
                CREAR P. ACTIVO
            </x-slot>

            <x-slot name="content">
                @livewire('maintenance.active-ingredient.mant-component', ["active_ingredient" => $active_ingredient, "modal" => true, "event_modal" => "toggle-modal-active-ingredient"])
            </x-slot>
        </x-jet-dialog-modal>
    @endif

    @if (in_array('create_unit', $arr_actions))
        @php
            $unit_width = "lg";
            $modal_unit_id = "modal-unit-id";
        @endphp
        <x-jet-dialog-modal wire:model="modal_unit" :id="$modal_unit_id" :maxWidth="$unit_width">
            <x-slot name="title">
                CREAR UNIDAD
            </x-slot>

            <x-slot name="content">
                @livewire('maintenance.unit.mant-component', ["unit" => $unit, "modal" => true, "event_modal" => "toggle-modal-unit"])
            </x-slot>
        </x-jet-dialog-modal>
    @endif

    @push("js")
        <!-- ckeditor -->
        <script src="{{ asset('assets/libs/@ckeditor/ckeditor5-build-classic/build/ckeditor.js') }}"></script>

        <script>
            $(".select2").select2();

            $(document).ready(function () {
                $('#supplier_id').on('change', function (e) {
                    var data = $('#supplier_id').select2("val");
                    @this.set('supplier_id', data);
                });
                $('#brand_id').on('change', function (e) {
                    var data = $('#brand_id').select2("val");
                    @this.set('brand_id', data);
                });
            });
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
                            document.querySelector('#specifications').value = editor.getData()
                        })
                    }
                )
                .catch( error => {
                    console.error( error );
                } );
            })
            function clearImage(){
                document.getElementById("image").value = "";
                @this.set('image', null);
            }
            function clearImageUploaded(){
                @this.set('imageUploaded', null);
            }
        </script>
    @endpush

</div>
