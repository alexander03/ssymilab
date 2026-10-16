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
                    <label for="branch_office_id" class="font-semibold text-gray-600">Sucursal: </label>
                    <x-general.select id="branch_office_id" name="branch_office_id" wire:model.defer="branch_office_id" wire:change="$refresh">
                        <option value="">TODOS</option>
                        @foreach ($branch_office as $item)
                            <option value="{{ $item->id }}">{{ $item->name }}</option>
                        @endforeach
                    </x-general.select>
                </div>
                <div class="mb-3 space-y-1 w-full text-xs col-span-1 sm:col-span-4 md:col-span-4 lg:col-span-3 xl:col-span-2">
                    <label for="product_name" class="font-semibold text-gray-600">Producto: </label>
                    <x-general.input type="text" name="product_name" placeholder="Producto" id="product_name"
                        wire:model.defer="product_name" wire:keydown.enter="$refresh"/>
                </div>
                <div class="mb-3 space-y-1 w-full text-xs col-span-1 sm:col-span-4 md:col-span-4 lg:col-span-3 xl:col-span-2">
                    <label for="lot" class="font-semibold text-gray-600">Lote: </label>
                    <x-general.input type="text" name="lot" placeholder="Lote" id="lot"
                        wire:model.defer="lot" wire:keydown.enter="$refresh"/>
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
                                        {{ $item->branch_office->name }}
                                    </td>
                                    <td class="px-6 py-4 text-center uppercase text-sm text-gray-500">
                                        {{ $item->product_name.' '.$item->presentation }}
                                    </td>
                                    <td class="px-6 py-4 text-center uppercase text-sm text-gray-500">
                                        {{ ($item->product->brand) ? $item->product->brand->name : "[ELIMINADO]" }}
                                    </td>
                                    <td class="px-6 py-4 text-center uppercase text-sm text-gray-500">
                                        {{ $item->lot }}
                                    </td>
                                    <td class="px-6 py-4 text-center uppercase text-sm text-gray-500">
                                        {{ date_format(new \DateTime($item->due_date), 'd/m/Y' )  }}
                                    </td>
                                    <td class="px-6 py-4 text-center uppercase text-sm text-gray-500">
                                        {{ $item->barcode }}
                                    </td>
                                    <td class="px-6 py-4 text-center uppercase text-sm text-gray-500">
                                        {{ sprintf('%g', $item->quantity) }}
                                    </td>
                                    <td class="px-6 py-4 text-center uppercase text-sm text-gray-500">
                                        {{ sprintf('%g', $item->remain) }}
                                    </td>
                                    <td class="px-6 py-4 text-center uppercase text-sm text-gray-500">
                                        @if ($item->status == "Y")
                                        <span
                                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            ACTIVO
                                        </span>
                                        @elseif ($item->status == "N")
                                        <span
                                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                            NO INGRESADO
                                        </span>
                                        @elseif ($item->status == "P")
                                        <span
                                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-purple-100 text-purple-800">
                                            SIN GUIA
                                        </span>
                                        @elseif ($item->status == "A")
                                        <span
                                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                            ANULADO
                                        </span>
                                        @else
                                            -
                                        @endif
                                    </td>
                                    @if (in_array('pdf_kardex_lot', $arr_actions))
                                        <td class="px-3 py-2 w-4 text-center text-base font-medium">
                                            <a title="Ver" target="_blank" href="{{ route('pdf-report-kardex-lot', ['lot' => $item->id]) }}"
                                                class="text-red-500 hover:text-red-800"><i class="fas fa-file-pdf"></i></a>
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
