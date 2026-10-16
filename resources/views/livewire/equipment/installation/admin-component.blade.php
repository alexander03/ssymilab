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
                <x-general.button class="bg-green-600 text-white hover:bg-green-700 active:bg-green-800 rounded-md mr-2" wire:click="$refresh">
                    <i class="fas fa-search" aria-hidden="true"></i> {{ __(' Buscar') }}
                </x-general.button>
            </div>
        </x-slot>
        <x-slot name="body">
            <div class="grid grid-cols-1 sm:grid-cols-12 gap-4">
                <div class="mb-3 space-y-1 w-full text-xs col-span-1 sm:col-span-4 md:col-span-4 lg:col-span-3 xl:col-span-2">
                    <label for="date_start" class="font-semibold text-gray-600">Desde: </label>
                    <x-general.input type="date" name="date_start" id="date_start"
                        wire:model.defer="date_start" wire:keydown.enter="$refresh"/>
                </div>
                <div class="mb-3 space-y-1 w-full text-xs col-span-1 sm:col-span-4 md:col-span-4 lg:col-span-3 xl:col-span-2">
                    <label for="date_end" class="font-semibold text-gray-600">Hasta: </label>
                    <x-general.input type="date" name="date_end" id="date_end"
                        wire:model.defer="date_end" wire:keydown.enter="$refresh"/>
                </div>
                <div class="mb-3 space-y-1 w-full text-xs col-span-1 sm:col-span-4 md:col-span-4 lg:col-span-3 xl:col-span-2">
                    <label for="product_name" class="font-semibold text-gray-600">Producto: </label>
                    <x-general.input type="text" name="product_name" placeholder="Producto" id="product_name"
                        wire:model.defer="product_name" wire:keydown.enter="$refresh"/>
                </div>
                <div class="mb-3 space-y-1 w-full text-xs col-span-1 sm:col-span-4 md:col-span-4 lg:col-span-3 xl:col-span-2">
                    <label for="product_model" class="font-semibold text-gray-600">Modelo: </label>
                    <x-general.input type="text" name="product_model" placeholder="Modelo" id="product_model"
                        wire:model.defer="product_model" wire:keydown.enter="$refresh"/>
                </div>
                <div class="mb-3 space-y-1 w-full text-xs col-span-1 sm:col-span-4 md:col-span-4 lg:col-span-3 xl:col-span-2">
                    <label for="product_serie" class="font-semibold text-gray-600">Serie: </label>
                    <x-general.input type="text" name="product_serie" placeholder="Serie" id="product_serie"
                        wire:model.defer="product_serie" wire:keydown.enter="$refresh"/>
                </div>
                <div class="mb-3 space-y-1 w-full text-xs col-span-1 sm:col-span-4 md:col-span-4 lg:col-span-3 xl:col-span-2">
                    <label for="customer" class="font-semibold text-gray-600">Cliente: </label>
                    <x-general.input type="text" name="customer" placeholder="Cliente" id="customer"
                        wire:model.defer="customer" wire:keydown.enter="$refresh"/>
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
                    <label for="status" class="font-semibold text-gray-600">Estado: </label>
                    <x-general.select name="status" wire:model.defer="status" wire:change="$refresh" id="status_id">
                        <option value="">Todos</option>
                        <option value="C">CESION DE USO</option>
                        <option value="V">VENTA</option>
                        <option value="P">PRESTAMO</option>
                    </x-general.select>
                </div>
            </div>
        </x-slot>
    </x-general.card-form>
    @if (in_array('delete_installation', $arr_actions))
        @livewire('equipment.installation.delete-component')
    @endif
    @if (in_array('change_status_installation', $arr_actions))
        @livewire('equipment.installation.change-status-component')
    @endif
    @if (in_array('upload_file_installation', $arr_actions))
        @livewire('equipment.installation.upload-files-component')
    @endif
     @if (in_array('files_installation', $arr_actions))
        @livewire('equipment.installation.files-component')
    @endif

    <x-general.card-table>
        <x-slot name="head">
            <x-general.button class="bg-blue-600 text-white hover:bg-blue-700 active:bg-blue-800 rounded-md mr-2" wire:click="create_route">
                <i class="fas fa-plus" aria-hidden="true"></i> {{ __(' Nuevo') }}
            </x-general.button>
        @if (in_array('pdf_list_installation', $arr_actions))
            <button class="items-center px-3 py-2 border border-transparent rounded-md font-semibold text-xs uppercase tracking-widest focus:outline-none focus:ring active:bg-gray-900 focus:border-gray-900 focus:ring-gray-300  disabled:opacity-25 transition bg-red-600 text-white hover:bg-red-700 active:bg-red-800 rounded-md mr-2" onclick="window.open('{{ route('pdf-list-installation', ['installation' => 1]) }}?date_start='+$('#date_start').val()+'&date_end='+$('#date_end').val()+'&product_name='+$('#product_name').val()+'&product_serie='+$('#product_serie').val()+'&product_model='+$('#product_model').val()+'&customer='+$('#customer').val()+'&brand_id='+$('#brand_id').val()+'&status='+$('#status_id').val(),'_blank');">
                <i class="fas fa-file-pdf" aria-hidden="true"></i> {{ __(' PDF') }}
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
                                    $product = \App\Models\Product::where('id', $item->product_id)->withTrashed()->first();
                                @endphp
                                <tr>
                                    <td class="px-6 py-4 w-4 text-center text-sm text-gray-500">
                                        {{ $init }}
                                    </td>
                                    <td class="px-6 py-4 text-center uppercase text-sm text-gray-500">
                                        {{ date("d/m/Y",strtotime($item->installed_date)) }}
                                    </td>
                                    <td class="px-6 py-4 text-center uppercase text-sm text-gray-500">
                                        {{ $product->code}}
                                    </td>
                                    <td class="px-6 py-4 text-center uppercase text-sm text-gray-500">
                                        {{ $product->name}}
                                    </td>
                                    <td class="px-6 py-4 text-center uppercase text-sm text-gray-500">
                                        {{$product->model }}
                                    </td>
                                    <td class="px-6 py-4 text-center uppercase text-sm text-gray-500">
                                        {{ ($product->brand) ? $product->brand->name : "[ELIMINADO]" }}
                                    </td>
                                    <td class="px-6 py-4 text-center uppercase text-sm text-gray-500">
                                        {{ $item->product_serie->serie }}
                                    </td>
                                    <td class="px-6 py-4 text-center uppercase text-sm text-gray-500">
                                        {{ $item->customer_name}}
                                    </td>
                                    <td class="px-6 py-4 text-center uppercase text-sm text-gray-500">
                                        {{ $item->product_place}}
                                    </td>
                                    <td class="px-6 py-4 text-center uppercase text-sm text-gray-500">
                                        {{ $item->product_location}}
                                    </td>
                                    <td class="px-6 py-4 text-center uppercase text-sm text-gray-500">
                                        {{ $item->status=='C'?'CESION DE USO':($item->status=='V'?'VENDIDO':'PRESTAMO') }}
                                    </td>
                                    @if (in_array('change_status_installation', $arr_actions))
                                        <td class="px-6 py-4 text-center uppercase text-sm text-gray-500">
                                            @if ($item->product_status == "Y")
                                            <span
                                                class="cursor-pointer px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800"
                                                wire:key="status-enabled-{{ $item->id }}" wire:click="$emit('toggle-modal-status', '{{ $item->id }}')">
                                                HABILITADO
                                            </span>
                                            @elseif ($item->product_status == "N")
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
                                    @if (in_array('edit_installation', $arr_actions))
                                        <td class="px-3 py-2 w-4 text-center text-base font-medium">
                                            <a title="Editar" href="{{ route('edit-installation', ['installation' => $item->id]) }}"
                                                class="text-indigo-600 hover:text-indigo-900"><i class="fas fa-pen"></i></a>
                                        </td>
                                    @endif
                                    @if (in_array('upload_file_installation', $arr_actions))
                                        <td class="px-3 py-2 w-4 text-center text-base font-medium">
                                            <a title="Subir Archivo" href="javascript:void(0)" wire:click="$emit('toggle-modal-upload', '{{ $item->id }}')"
                                                class="text-green-500 hover:text-green-800"><i class="fas fa-file"></i></a>
                                        </td>
                                    @endif
                                    @if (in_array('files_installation', $arr_actions))
                                        <td class="px-3 py-2 w-4 text-center text-base font-medium">
                                            <a title="Archivos" href="javascript:void(0)" wire:click="$emit('toggle-modal-files', '{{ $item->id }}')"
                                                class="text-orange-500 hover:text-orange-800"><i class="fas fa-list"></i></a>
                                        </td>
                                    @endif
                                    @if (in_array('pdf_installation', $arr_actions))
                                        <td class="px-3 py-2 w-4 text-center text-base font-medium">
                                            <select name="anio" wire:model.defer="anio" wire:change="$refresh">
                                                <option value="2022">2022</option>
                                                <option value="2023">2023</option>
                                                <option value="2024">2024</option>
                                                <option value="2025">2025</option>
                                            </select>
                                            <a title="Ver" target="_blank" href="{{ route('pdf-installation', ['installation' => $item->id,'anio'=>$anio]) }}"
                                                class="text-red-500 hover:text-red-800"><i class="fas fa-file-pdf"></i></a>
                                        </td>
                                    @endif
                                    @if (in_array('pdf_follow', $arr_actions))
                                        <td class="px-3 py-2 w-4 text-center text-base font-medium">
                                            <a title="Ver" target="_blank" href="{{ route('pdf-follow', ['installation' => $item->id]) }}"
                                                class="text-blue-500 hover:text-blue-800"><i class="fas fa-file-pdf"></i></a>
                                        </td>
                                    @endif
                                    @if (in_array('delete_installation', $arr_actions))
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
