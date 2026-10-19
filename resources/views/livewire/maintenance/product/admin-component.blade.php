<div>
    <x-general.card-form>
        <x-slot name="head">
            <div>
                <span class="font-bold ml-1 mr-4 text-base align-middle text-white">
                    BÚSQUEDA
                </span>
            </div>
            <div class="flex">
                <div class="space-y-1 text-xs col-span-1 sm:col-span-4 md:col-span-4 lg:col-span-3 xl:col-span-1 mr-4 h-9 mt-0">
                    <x-general.select id="rows" name="rows" class="mt-0" wire:model.defer="rows" wire:change="$refresh">
                        <option value="10">10</option>
                        <option value="20">20</option>
                        <option value="30">30</option>
                    </x-general.select>
                </div>
                <x-general.button class="bg-green-600 h-9 text-white hover:bg-green-700 active:bg-green-800 rounded-md mr-2" wire:click="$refresh">
                    <i class="fas fa-search" aria-hidden="true"></i> {{ __(' Buscar') }}
                </x-general.button>
            </div>
        </x-slot>
        <x-slot name="body">
            <div class="grid grid-cols-1 sm:grid-cols-12 gap-4">
                <div class="mb-3 space-y-1 w-full text-xs col-span-1 sm:col-span-4 md:col-span-4 lg:col-span-3 xl:col-span-2">
                    <label for="type" class="font-semibold text-gray-600">Tipo: </label>
                    <x-general.select id="type" wire:model.defer="type" wire:change="$refresh">
                        <option value="">TODOS</option>
                        @foreach ($types as $key => $item)
                            <option value="{{ $key }}"> {{ $item }} </option>
                        @endforeach
                    </x-general.select>
                </div>
                <div class="mb-3 space-y-1 w-full text-xs col-span-1 sm:col-span-4 md:col-span-4 lg:col-span-3 xl:col-span-2">
                    <label for="name" class="font-semibold text-gray-600">Nombre: </label>
                    <x-general.input type="text" name="name" placeholder="Nombre" id="name"
                        wire:model.defer="name" wire:keydown.enter="$refresh"/>
                </div>
                <div class="mb-3 space-y-1 w-full text-xs col-span-1 sm:col-span-4 md:col-span-4 lg:col-span-3 xl:col-span-1">
                    <label for="brand_id" class="font-semibold text-gray-600">Modelo: </label>
                    <x-general.input type="text" name="model" placeholder="Model" id="model"
                        wire:model.defer="model" wire:keydown.enter="$refresh"/>
                </div>
                <div class="mb-3 space-y-1 w-full text-xs col-span-1 sm:col-span-4 md:col-span-4 lg:col-span-3 xl:col-span-2">
                    <label for="category_id" class="font-semibold text-gray-600">Categoría: </label>
                    <x-general.select id="category_id" wire:model.defer="category_id" wire:change="$refresh">
                        <option value="">TODOS</option>
                        @foreach ($categories as $item)
                            <option value="{{ $item->id }}"> {{ $item->name }} </option>
                        @endforeach
                    </x-general.select>
                </div>
                <div class="mb-3 space-y-1 w-full text-xs col-span-1 sm:col-span-4 md:col-span-4 lg:col-span-3 xl:col-span-2">
                    <label for="brand_id" class="font-semibold text-gray-600">Marca: </label>
                    <x-general.select id="brand_id" wire:model.defer="brand_id" wire:change="$refresh">
                        <option value="">TODOS</option>
                        @foreach ($brands as $item)
                            <option value="{{ $item->id }}"> {{ $item->name }} </option>
                        @endforeach
                    </x-general.select>
                </div>
                <div class="mb-3 space-y-1 w-full text-xs col-span-1 sm:col-span-4 md:col-span-4 lg:col-span-3 xl:col-span-2">
                    <label for="supplier_id" class="font-semibold text-gray-600">Distribuidor: </label>
                    <x-general.select id="supplier_id" wire:model.defer="supplier_id" wire:change="$refresh">
                        <option value="">TODOS</option>
                        @foreach ($suppliers as $item)
                            <option value="{{ $item->id }}"> {{ $item->getNameComplete() }} </option>
                        @endforeach
                    </x-general.select>
                </div>
                <div class="mb-3 space-y-1 w-full text-xs col-span-1 sm:col-span-4 md:col-span-4 lg:col-span-3 xl:col-span-1">
                    <label for="status_product" class="font-semibold text-gray-600">Estado: </label>
                    <x-general.select id="status_product" wire:model.defer="status_product" wire:change="$refresh">
                        <option value="">TODOS</option>
                        <option value="Y">HABILITADO</option>
                        <option value="N">NO HABILITADO</option>
                    </x-general.select>
                </div>

            </div>
        </x-slot>
    </x-general.card-form>
    @if (in_array('delete_product', $arr_actions))
        @livewire('maintenance.product.delete-component')
    @endif
    @if (in_array('change_status_product', $arr_actions))
        @livewire('maintenance.product.change-status-component')
    @endif


    <x-general.card-table>
        <x-slot name="head">
        @if (in_array('create_product', $arr_actions))
            
                <x-general.button class="bg-blue-600 text-white hover:bg-blue-700 active:bg-blue-800 rounded-md mr-2" wire:click="create_route">
                    <i class="fas fa-plus" aria-hidden="true"></i> {{ __(' Nuevo') }}
                </x-general.button>
        @endif
        @if (in_array('pdf_product', $arr_actions))
                <button class="items-center px-3 py-2 border border-transparent rounded-md font-semibold text-xs uppercase tracking-widest focus:outline-none focus:ring active:bg-gray-900 focus:border-gray-900 focus:ring-gray-300  disabled:opacity-25 transition bg-red-600 text-white hover:bg-red-700 active:bg-red-800 rounded-md mr-2" onclick="window.open('{{ route('pdf-product', ['product' => 1]) }}?type='+$('#type').val()+'&name='+$('#name').val()+'&category_id='+$('#category_id').val()+'&brand_id='+$('#brand_id').val()+'&supplier_id='+$('#supplier_id').val()+'&status_product='+$('#status_product').val(),'_blank');">
                    <i class="fas fa-file-pdf" aria-hidden="true"></i> {{ __(' PDF') }}
                </button>
                <button class="items-center px-3 py-2 border border-transparent rounded-md font-semibold text-xs uppercase tracking-widest focus:outline-none focus:ring active:bg-gray-900 focus:border-gray-900 focus:ring-gray-300  disabled:opacity-25 transition bg-green-600 text-white hover:bg-green-700 active:bg-green-800 rounded-md mr-2" onclick="window.open('{{ route('excel-product', ['product' => 1]) }}?type='+$('#type').val()+'&name='+$('#name').val()+'&category_id='+$('#category_id').val()+'&brand_id='+$('#brand_id').val()+'&supplier_id='+$('#supplier_id').val()+'&status_product='+$('#status_product').val(),'_blank');">
                    <i class="fas fa-file-excel" aria-hidden="true"></i> {{ __(' EXCEL') }}
                </button>
        @endif
        @if (in_array('pdf_stock_product', $arr_actions))
                <button class="items-center px-3 py-2 border border-transparent rounded-md font-semibold text-xs uppercase tracking-widest focus:outline-none focus:ring active:bg-gray-900 focus:border-gray-900 focus:ring-gray-300  disabled:opacity-25 transition bg-green-600 text-white hover:bg-green-700 active:bg-green-800 rounded-md mr-2" onclick="window.open('{{ route('pdf-stock-product', ['product' => 1]) }}?type='+$('#type').val()+'&brand_id='+$('#brand_id').val(),'_blank')">
                    <i class="fas fa-file" aria-hidden="true"></i> {{ __(' STOCK') }}
                </button>
                <button class="items-center px-3 py-2 border border-transparent rounded-md font-semibold text-xs uppercase tracking-widest focus:outline-none focus:ring active:bg-gray-900 focus:border-gray-900 focus:ring-gray-300  disabled:opacity-25 transition bg-green-600 text-white hover:bg-green-700 active:bg-green-800 rounded-md mr-2" onclick="window.open('{{ route('pdf-price-product', ['product' => 1]) }}?type='+$('#type').val()+'&brand_id='+$('#brand_id').val(),'_blank')">
                    <i class="fas fa-file" aria-hidden="true"></i> {{ __(' PDF P. VENTA') }}
                </button>
        @endif
        </x-slot>
        <x-slot name="body">
            <div wire:loading wire:target="$refresh" wire:loading.class="text-gray-400">
                Cargando registros ...
            </div>
            <div wire:loading.remove wire:target="$refresh">
                @if (count($list_items) == 0)
                    <span class="text-2xl text-yellow-500 font-bold"> No se encontraron registros</span>
                @else
                    @if ($list_items->hasPages())
                        <div class="px-1 pb-3">
                            {{ $list_items->onEachSide(0)->links() }}
                        </div>
                    @endif
                    <div class="rounded shadow">
                        <x-general.table :thead="$thead" :sort="$sort" :direction="$direction">
                            @php
                                $init = $list_items->firstItem() - 1;
                            @endphp
                            @foreach ($list_items as $item)
                                @php
                                    $init = $init + 1;
                                @endphp
                                <tr>
                                    <td class="px-6 py-4 w-4 text-center text-sm text-gray-500">
                                        {{ $init }}
                                    </td>
                                    <td class="px-6 py-4 text-center uppercase text-sm text-gray-500">
                                        {{ $item->code }}
                                    </td>
                                    <td class="px-6 py-4 text-center uppercase text-sm text-gray-500">
                                        {{ $item->barcode }}
                                    </td>
                                    <td class="px-6 py-4 text-center uppercase text-sm text-gray-500">
                                        {{ $item->name }}
                                    </td>
                                    @if($this->type=='' || $this->type=='R' || $this->type=='M')
                                    <td class="px-6 py-4 text-center uppercase text-sm text-gray-500">
                                        {{ $item->presentation }}
                                    </td>
                                    @endif
                                    @if($this->type=='' || $this->type=='M')
                                    <td class="px-6 py-4 text-center uppercase text-sm text-gray-500">
                                        {{ $item->model }}
                                    </td>
                                    @endif
                                    <td class="px-6 py-4 text-center uppercase text-sm text-gray-500">
                                        {{ ($item->category) ? $item->category->name : "[ELIMINADO]" }}
                                    </td>
                                    <td class="px-6 py-4 text-center uppercase text-sm text-gray-500">
                                        {{ ($item->supplier) ? $item->supplier->getNameComplete() : "[ELIMINADO]" }}
                                    </td>
                                    <td class="px-6 py-4 text-center uppercase text-sm text-gray-500">
                                        {{ ($item->brand) ? $item->brand->name : "[ELIMINADO]" }}
                                    </td>
                                    <td class="px-6 py-4 text-center uppercase text-sm text-gray-500">
                                        @if ($item->type == "R")
                                        <span
                                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                            DISPOSITIVO MEDICO
                                        </span>
                                        @elseif ($item->type == "M")
                                        <span
                                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                            EQUIPO
                                        </span>
                                        @elseif ($item->type == "S")
                                        <span
                                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-purple-100 text-purple-800">
                                            SERVICIO
                                        </span>
                                        @elseif ($item->type == "E")
                                        <span
                                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            REPUESTOS
                                        </span>
                                        @elseif ($item->type == "C")
                                        <span
                                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            COMPUESTO
                                        </span>
                                        @elseif ($item->type == "O")
                                        <span
                                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            CONSUMIBLE
                                        </span>
                                        @else
                                            -
                                        @endif

                                    </td>
                                    @if (in_array('change_status_product', $arr_actions))
                                        <td class="px-6 py-4 text-center uppercase text-sm text-gray-500">
                                            @if ($item->status == "Y")
                                            <span
                                                class="cursor-pointer px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800"
                                                wire:key="status-enabled-{{ $item->id }}" wire:click="$emit('toggle-modal-status', '{{ $item->id }}')">
                                                HABILITADO
                                            </span>
                                            @elseif ($item->status == "N")
                                            <span
                                                class="cursor-pointer px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800"
                                                wire:key="status-disabled-{{ $item->id }}" wire:click="$emit('toggle-modal-status', '{{ $item->id }}')">
                                                DESHABILITADO
                                            </span>
                                            @else
                                                -
                                            @endif
                                        </td>
                                    @endif
                                    @if (in_array('edit_product', $arr_actions))
                                        <td class="px-3 py-2 w-4 text-center text-base font-medium">
                                            <a title="Editar" href="{{ route('edit-product', ['product' => $item->id]) }}"
                                                class="text-indigo-600 hover:text-indigo-900"><i class="fas fa-pen"></i></a>
                                        </td>
                                    @endif

                                    @if (in_array('delete_product', $arr_actions))
                                        <td class="px-3 py-2 w-4 text-center text-base font-medium">
                                            <a title="Eliminar" href="javascript:void(0)" wire:click="$emit('toggle-modal', '{{ $item->id }}')"
                                                class="text-red-600 hover:text-red-800"><i class="fas fa-trash"></i></a>
                                        </td>
                                    @endif


                                </tr>
                            @endforeach
                        </x-general.table>
                    </div>

                @endif
            </div>
        </x-slot>
    </x-general.card-form>

</div>
<script type="text/javascript">
    function generarPDF(ruta){
        window.open(ruta,'_blank');
    }
</script>