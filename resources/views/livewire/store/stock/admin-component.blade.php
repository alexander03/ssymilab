<div>
    <x-general.card-form>
        <x-slot name="head">
            <div>
                <span class="font-bold ml-1 mr-4 text-base align-middle text-white">
                    BÚSQUEDA
                </span>
            </div>
            <x-general.button class="bg-green-600 text-white hover:bg-green-700 active:bg-green-800 rounded-md mr-2" wire:click="$refresh">
                <i class="fas fa-search" aria-hidden="true"></i> {{ __(' Buscar') }}
            </x-general.button>
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
                    <label for="product_name" class="font-semibold text-gray-600">Producto / Modelo: </label>
                    <x-general.input type="text" name="product_name" placeholder="Producto" id="product_name"
                        wire:model.defer="product_name" wire:keydown.enter="$refresh"/>
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
                    <label for="rows" class="font-semibold text-gray-600">Filas a Mostrar: </label>
                    <x-general.select id="rows" name="rows" wire:model.defer="rows" wire:change="$refresh">
                        <option value="10">10</option>
                        <option value="20">20</option>
                        <option value="30">30</option>
                    </x-general.select>
                </div>
            </div>
        </x-slot>
    </x-general.card-form>

    <x-general.card-table>
        <x-slot name="head">
        @if (in_array('pdf_stock', $arr_actions))
            <button class="items-center px-3 py-2 border border-transparent rounded-md font-semibold text-xs uppercase tracking-widest focus:outline-none focus:ring active:bg-gray-900 focus:border-gray-900 focus:ring-gray-300  disabled:opacity-25 transition bg-blue-600 text-white hover:bg-blue-700 active:bg-blue-800 rounded-md mr-2" onclick="window.open('{{ route('pdf-stock', ['product_name' => $this->product_name,'type' => $this->type, 'brand_id' => $this->brand_id]) }}','_blank');">
                    <i class="fas fa-file-pdf" aria-hidden="true"></i> {{ __(' PDF') }}
                </button>
            <a href="#" onclick="window.open('{{ route('excel-stock') }}?brand_id='+$('#brand_id').val()+'&product_name='+$('#product_name').val()+'&type='+$('#type').val(),'_blank')" >
                <x-general.button class="bg-green-600 text-white hover:bg-green-700 active:bg-green-800 rounded-md mr-2" >
                    <i class="fas fa-file-excel" aria-hidden="true"></i> {{ __(' EXCEL') }}
                </x-general.button>
            </a>
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
                                        {{ $item->product_name }}
                                    </td>
                                    @if($this->type=='' || $this->type=='R')
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
                                        {{ ($item->product->brand) ? $item->product->brand->name : "[ELIMINADO]" }}
                                    </td>
                                    <td class="px-6 py-4 text-center uppercase text-sm text-gray-500">
                                        {{ sprintf('%g', $item->real) }}
                                    </td>
                                    <td class="px-6 py-4 text-center uppercase text-sm text-gray-500">
                                        {{ sprintf('%g', $item->separated) }}
                                    </td>
                                    <td class="px-6 py-4 text-center uppercase text-sm text-gray-500">
                                        {{ sprintf('%g', $item->available) }}
                                    </td>
                                </tr>
                            @endforeach
                        </x-general.table>
                    </div>

                @endif
            </div>
        </x-slot>
    </x-general.card-form>

</div>
