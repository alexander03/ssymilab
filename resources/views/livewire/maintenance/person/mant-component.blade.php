<div>
    @if (!$modal)
        <div class="bg-white rounded-md py-8 px-8 shadow-md">
    @endif
        <div x-data="{ type: @entangle('type').defer }" class="grid grid-cols-6 gap-6">

            <div class="col-span-6 md:col-span-1">
                <x-jet-label for="type" class="mb-3" value="{{ __('Tipo doc.') }}" />
                <x-general.select id="type"
                    class="mt-3 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 block w-full sm:text-sm border-gray-300 rounded-md h-10"
                    wire:model.defer="type"
                    x-model="type"
                    x-on:change="console.log(type)">
                    <option value="1">DNI</option>
                    <option value="6">RUC</option>
                    <option value="4">CARNET EXTRANJERIA</option>
                    <option value="0">OTROS</option>
                </x-general.select>
                <x-jet-input-error for="type" class="mt-2" />
            </div>

            <div class="col-span-6 md:col-span-3" x-show="type=='1'">
                <x-jet-label for="dni" value="{{ __('DNI') }}" />
                <div class="flex mt-1 ">
                    <x-general.input id="dni" type="number" class="block placeholder-gray-400 h-10 rounded-tr-none rounded-br-none w-full" placeholder="DNI" wire:model.defer="dni" autocomplete="dni" />
                    <button class="mt-1 bg-blue-500 text-white p-2 hover:bg-blue-600 focus:outline-none w-11 h-10 flex items-center justify-center rounded-md rounded-tl-none rounded-bl-none disabled:opacity-25 transition" wire:key="btn-dni" wire:target="search_document" wire:click="search_document" wire:loading.attr="disabled">
                        <i class="fas fa-search" aria-hidden="true"></i>
                    </button>
                </div>
                <x-jet-input-error for="dni" class="mt-2" />
            </div>
            <div class="col-span-6 md:col-span-3" x-show="type!='1'">
                <div x-show="type=='6'">
                    <x-jet-label for="ruc" value="RUC" />
                </div>
                <div x-show="type=='4'">
                    <x-jet-label for="ruc" value="Carnet de extranjería" />
                </div>
                <div x-show="type=='0'">
                    <x-jet-label for="ruc" value="Nro Documento" />
                </div>
                <div class="flex">
                    <x-general.input id="ruc" type="number" x-bind:class="(type == '6') ? 'block placeholder-gray-400 h-10 w-full rounded-tr-none rounded-br-none' : 'block placeholder-gray-400 h-10 w-full'" x-bind:placeholder="(type=='6') ? 'RUC' : 'Documento'" wire:model.defer="ruc" autocomplete="ruc"  />
                    <button x-show="type=='6'" class="mt-1 bg-blue-500 text-white p-2 hover:bg-blue-600 focus:outline-none w-11 h-10 flex items-center justify-center rounded-md rounded-tl-none rounded-bl-none disabled:opacity-25 transition" wire:key="btn-ruc" wire:target="search_document" wire:click="search_document" wire:loading.attr="disabled">
                        <i class="fas fa-search" aria-hidden="true"></i>
                    </button>
                </div>
                <x-jet-input-error for="ruc" class="mt-2" />
            </div>
            <div class="hidden md:visible md:col-span-2">

            </div>

            <div class="col-span-6 md:col-span-3" x-show="type=='1'">
                <x-jet-label for="name" value="{{ __('Nombres') }}" />
                <x-general.input id="name" type="text" class="mt-1 block w-full placeholder-gray-400" placeholder="Nombres" wire:model.defer="name" autocomplete="name" />
                <x-jet-input-error for="name" class="mt-2" />
            </div>

            <div class="col-span-6 md:col-span-3" x-show="type=='1'">
                <x-jet-label for="surname_father" value="{{ __('Apellido Paterno') }}" />
                <x-general.input id="surname_father" type="text" class="mt-1 block w-full placeholder-gray-400" placeholder="Apellido Paterno" wire:model.defer="surname_father" autocomplete="surname_father" />
                <x-jet-input-error for="surname_father" class="mt-2" />
            </div>

            <div class="col-span-6 md:col-span-3" x-show="type=='1'">
                <x-jet-label for="surname_mother" value="{{ __('Apellido Materno') }}" />
                <x-general.input id="surname_mother" type="text" class="mt-1 block w-full placeholder-gray-400" placeholder="Apellido Materno" wire:model.defer="surname_mother" autocomplete="surname_mother" />
                <x-jet-input-error for="surname_mother" class="mt-2" />
            </div>

            <div class="col-span-6 md:col-span-6" x-show="type!='1'">
                <x-jet-label for="business_name" value="{{ __('Razón Social / Nombres Completos') }}" />
                <x-general.input id="business_name" type="text" class="mt-1 block w-full placeholder-gray-400" placeholder="Razón Social / Nombres Completos" wire:model.defer="business_name" autocomplete="business_name" />
                <x-jet-input-error for="business_name" class="mt-2" />
            </div>

            <div class="col-span-6 md:col-span-3">
                <x-jet-label for="email" value="{{ __('Email') }}" />
                <x-general.input id="email" type="email" class="mt-1 block w-full placeholder-gray-400" placeholder="Email" wire:model.defer="email" />
                <x-jet-input-error for="email" class="mt-2" />
            </div>

            <div class="col-span-6 md:col-span-3">
                <x-jet-label for="phone" value="{{ __('Celular') }}" />
                <x-general.input id="phone" type="text" class="mt-1 block w-full placeholder-gray-400" placeholder="Celular" wire:model.defer="phone" autocomplete="phone" />
                <x-jet-input-error for="phone" class="mt-2" />
            </div>

            <div class="col-span-6 md:col-span-3">
                <x-jet-label for="code" value="{{ __('Codigo') }}" />
                <x-general.input id="code" type="text" class="mt-1 block w-full placeholder-gray-400" placeholder="Codigo" wire:model.defer="code" autocomplete="code" />
                <x-jet-input-error for="code" class="mt-2" />
            </div>

            <div class="col-span-6 md:col-span-6">
                <x-jet-label for="address" value="{{ __('Dirección') }}" />
                <x-general.input id="address" type="text" class="mt-1 block w-full placeholder-gray-400" placeholder="Dirección" wire:model.defer="address" autocomplete="address" />
                <x-jet-input-error for="address" class="mt-2" />
            </div>

            <div class="col-span-6 md:col-span-6">
                <label for="supervisor_id" class="font-semibold text-gray-600">Supervisor/Vendedor: </label>
                <x-general.select id="supervisor_id" wire:model.defer="supervisor_id">
                    <option value="0">NINGUNO</option>
                    @foreach ($supervisors as $key => $item)
                        <option value="{{ $item["id"] }}">{{ $item["name_complete"] }}</option>
                    @endforeach
                </x-general.select>
                <x-jet-input-error for="supervisor_id" class="mt-2"  />
            </div>

            <div class="col-span-6 md:col-span-6" id="divZona" <?php if(in_array('1', $roles)==false){ ?> style="display: none;" <?php } ?>>
                <label for="zone_id" class="font-semibold text-gray-600">Zona: </label>
                <x-general.select id="zone_id" wire:model.defer="zone_id">
                    <option value="">NINGUNA</option>
                    @foreach ($zones as $item)
                        <option value="{{ $item->id }}">{{ $item->name }}</option>
                    @endforeach
                </x-general.select>
                <x-jet-input-error for="zone_id" class="mt-2"  />
            </div>

            <div class="col-span-6 md:col-span-6" id="divFormaPago" <?php if(in_array('1', $roles)==false){ ?> style="display: none;" <?php } ?>>
                <label class="font-semibold text-gray-600">Forma de pago: </label>
                <div class="flex items-center mt-2">
                    <label class="flex items-center mr-6">
                        <input type="checkbox" class="form-checkbox rounded h-4 w-4 text-blue-600"
                            wire:key="payment_type_C" wire:model.defer="payment_types" value="C">
                        <span class="ml-2 text-gray-700 text-sm font-semibold">CONTADO</span>
                    </label>
                    <label class="flex items-center">
                        <input type="checkbox" class="form-checkbox rounded h-4 w-4 text-blue-600"
                            wire:key="payment_type_D" wire:model.defer="payment_types" value="D">
                        <span class="ml-2 text-gray-700 text-sm font-semibold">CRÉDITO</span>
                    </label>
                </div>
                <x-jet-input-error for="payment_types" class="mt-2"  />
            </div>

            <div class="col-span-6 md:col-span-2">
                <x-jet-label for="shipping_person_dni" value="{{ __('DNI Persona Envio') }}" />
                <x-general.input id="shipping_person_dni" type="number" class="mt-1 block w-full placeholder-gray-400" placeholder="DNI Persona de Envio" wire:model.defer="shipping_person_dni"  />
                <x-jet-input-error for="shipping_person_dni" class="mt-2" />
            </div>

            <div class="col-span-6 md:col-span-4">
                <x-jet-label for="shipping_person" value="{{ __('Persona Envio') }}" />
                <x-general.input id="shipping_person" type="text" class="mt-1 block w-full placeholder-gray-400" placeholder="Persona de Envio" wire:model.defer="shipping_person"  />
                <x-jet-input-error for="shipping_person" class="mt-2" />
            </div>

            <div class="col-span-6 md:col-span-6">
                <x-jet-label for="" value="{{ __('Roles:') }}" />
                @foreach ($arr_roles as $role)
                <div class="mt-1 ml-2">
                    <label class="flex items-center mt-3 mb-2">
                        <input type="checkbox" class="form-checkbox rounded h-4 w-4 text-blue-600"
                        id="role_{{$role->id}}"
                        wire:key="role_{{$role->id}}"
                        wire:model.defer="roles"
                        onclick="update_role(this.value,this.checked)" 
                        value="{{ $role->id }}">
                        <span class="ml-2 text-gray-700 text-sm font-semibold">{{ $role->name }}</span>
                    </label>
                </div>
                @endforeach
                <x-jet-input-error for="roles" class="mt-2" />
                <x-jet-input-error for="roles.*" class="mt-2" />
            </div>
        </div>
        <div class="grid grid-cols-12 p-3 gap-5" id="divProducto" <?php if(count($roles)==0 || in_array('7', $roles)==false){ ?> style="display: none;" <?php } ?>>
            <div class="col-span-12 lg:col-span-5">
                <div class="grid grid-cols-12 gap-2">
                    <div class="col-span-12 lg:col-span-4 max-h-20" style="display: none;">
                        <label for="barcode">C. Barra: </label>
                        <x-general.input type="text" id="barcode" placeholder="Código de Barra" wire:model.defer="barcode" wire:keydown.enter="select_bar_code_person" onclick="this.select()" />
                        <x-jet-input-error for="barcode" />
                    </div>
                    <div class="col-span-12 lg:col-span-8 max-h-20">
                        <label for="name">Producto: </label>
                        <x-general.input type="text" name="search_product_person" placeholder="Producto" wire:model.defer="search_product_person" wire:keyup="search_products_person" wire:keydown.enter="search_products_person"/>
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
                            @if (count($list_search_product_person) == 0)
                            <tr>
                                <td colspan="2" class="px-3 py-2 text-center text-xs text-gray-500"> No se encontraron coincidencias. </td>
                            </tr>
                            @else
                                @foreach ($list_search_product_person as $key => $product)
                                <tr class="hover:bg-blue-100 cursor-pointer" wire:click="select_products_person({{$key}})"
                                wire:key="select-product-person-{{$product["id"]}}">
                                    <td class="px-3 py-2 text-left text-xs text-gray-500">
                                        <p class="text-sm text-gray-700 font-bold">{{ $product["name"].' '.$product['model'].' '.$product['presentation'] }}</p>
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
                    <x-jet-input-error for="person_car" />
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-200">
                            <tr>
                                <th colspan="1" class="sticky top-0 py-1 w-7/12 text-center text-xs font-bold bg-blue-900 text-white uppercase tracking-wider">Descripción</th>
                                <th colspan="1" class="sticky top-0 py-1 w-2/12 text-center text-xs font-bold bg-blue-900 text-white uppercase tracking-wider">Und.</th>
                                <th colspan="1" class="sticky top-0 py-1 w-2/12 text-center text-xs font-bold bg-blue-900 text-white uppercase tracking-wider">Precio</th>
                                <th colspan="1" class="sticky top-0 py-1 w-1/12 text-center text-xs font-bold bg-blue-900 text-white uppercase tracking-wider">Quit.</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @if (count($person_car) == 0)
                            <tr>
                                <td colspan="4" class="px-3 py-2 text-center text-xs text-gray-500"> No ha seleccionado ningún producto. </td>
                            </tr>
                            @else
                                @foreach ($person_car as $key => $item)
                                    <tr>
                                        <td class="px-3 py-2 text-left text-sm text-gray-500"> {{ $item["product_name"].' '.$item["model"].' '.$item["presentation"] }} </td>
                                        <td class="px-3 py-2 text-center text-xs text-gray-500">
                                            @foreach ($item["units"] as $unit)
                                                @if ($unit["id"] == $item["unit_id"])
                                                    {{ $unit["name"] }}
                                                @endif
                                            @endforeach
                                        </td>
                                        <td class="px-3 py-2 text-center text-xs text-gray-500">
                                            <x-general.input :full="false" :block="false" :id="'price_'.$item['product_id']" type="number" class="w-28" autocomplete="off" wire:model.defer="person_car.{{ $key }}.price" />
                                            <x-jet-input-error for="person_car.{{ $key }}.price" />
                                        </td>
                                        
                                        <td class="px-3 py-2 text-center text-xs text-gray-500">
                                            <button
                                            wire:click="quit_car_item_person({{ $key }})"
                                            wire:key="quit-product-unit-person-{{ $item["product_id"] }}"><i class="fas fa-trash-alt text-red-600 text-base"></i></button>
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="mt-4 flex justify-end">
            <x-general.button x-on:click="show=false" class="bg-gray-200 text-black hover:bg-gray-400 active:bg-gray-500 mx-2" wire:click="back_button">
                <i class="fas fa-arrow-left mr-2" aria-hidden="true"></i> {{ __(' Atrás') }}
            </x-general.button>
            @if ($person->id)
                <x-general.button class="bg-yellow-500 text-white hover:bg-yellow-600 active:bg-yellow-700 mx-2" wire:click="update"
                    wire:loading.attr="disabled" wire:target="update" wire:key="update-person">
                    <i class="fas fa-edit mr-2" aria-hidden="true"></i> {{ __(' Editar') }}
                </x-general.button>
            @else
                <x-general.button class="bg-blue-500 text-white hover:bg-blue-600 active:bg-blue-700 mx-2" wire:click="save"
                    wire:loading.attr="disabled" wire:target="save" wire:key="save-person">
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
    function update_role(id,check){
        if(id==7){
            if(check){
                $("#divProducto").css("display","");
            }else{
                $("#divProducto").css("display","none");
            }
        }
        if(id==1){
            if(check){
                $("#divZona").css("display","");
                $("#divFormaPago").css("display","");
            }else{
                $("#divZona").css("display","none");
                $("#divFormaPago").css("display","none");
            }
        }

    }
</script>
@endpush