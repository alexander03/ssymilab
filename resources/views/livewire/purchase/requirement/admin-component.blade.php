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
                    <label for="number" class="font-semibold text-gray-600">Número: </label>
                    <x-general.input type="text" name="number" placeholder="Número" id="number"
                        wire:model.defer="number" wire:keydown.enter="$refresh"/>
                </div>
                <div class="mb-3 space-y-1 w-full text-xs col-span-1 sm:col-span-4 md:col-span-4 lg:col-span-3 xl:col-span-2">
                    <label for="supplier" class="font-semibold text-gray-600">Proveedor: </label>
                    <x-general.input type="text" name="supplier" placeholder="Proveedor" id="supplier"
                        wire:model.defer="supplier" wire:keydown.enter="$refresh"/>
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

    @if (in_array('delete_requirement', $arr_actions))
        @livewire('purchase.requirement.delete-component')
    @endif

    <x-general.card-table>
        @if (in_array('create_requirement', $arr_actions))
            <x-slot name="head">
                <x-general.button class="bg-blue-600 text-white hover:bg-blue-700 active:bg-blue-800 rounded-md mr-2" wire:click="create_route">
                    <i class="fas fa-plus" aria-hidden="true"></i> {{ __(' Nuevo') }}
                </x-general.button>
            </x-slot>
        @endif

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
                                    if($item->status == "Y"){
                                        $color = "border-green-400";
                                    }elseif ($item->status == "P") {
                                        $color = "border-yellow-400";
                                    }elseif ($item->status == "A") {
                                        $color = "border-red-400";
                                    }
                                @endphp
                                <tr>
                                    <td class="px-6 py-4 w-4 text-center text-sm text-gray-500">
                                        {{ $init }}
                                    </td>
                                    <td class="px-6 py-4 text-center uppercase text-sm text-gray-500">
                                        {{ $item->number }}
                                    </td>
                                    <td class="px-6 py-4 text-center uppercase text-sm text-gray-500">
                                        {{ date_format(new DateTime($item->date_time), 'd/m/Y') }}
                                    </td>
                                    <td class="px-6 py-4 text-center uppercase text-sm text-gray-500">
                                        {{ $item->supplier_document }}
                                    </td>
                                    <td class="px-6 py-4 text-center uppercase text-sm text-gray-500">
                                        {{ $item->supplier_name }}
                                    </td>
                                    @php
                                        $comment = json_decode($item->detail)->comment;
                                    @endphp
                                    <td class="px-6 py-4 text-center uppercase text-sm text-gray-500">
                                        {{ $comment }}
                                    </td>
                                    <td class="px-6 py-4 text-center uppercase text-sm text-gray-500">
                                        @if ($item->status == "Y")
                                        <span
                                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            CON FACTURA
                                        </span>
                                        @elseif ($item->status == "P")
                                        <span
                                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                            SIN FACTURA
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
                                    @if (in_array('show_requirement', $arr_actions))
                                        <td class="px-3 py-2 w-4 text-center text-base font-medium">
                                            <a title="Ver" href="{{ route('show-requirement', ['requirement' => $item->id]) }}"
                                                class="text-blue-400 hover:text-blue-700"><i class="fas fa-eye"></i></a>
                                        </td>
                                    @endif

                                    @if (in_array('edit_requirement', $arr_actions))
                                        @if ($item->status == "P")
                                        <td class="px-3 py-2 w-4 text-center text-base font-medium">
                                            <a title="Editar" href="{{ route('edit-requirement', ['requirement' => $item->id]) }}"
                                                class="text-indigo-600 hover:text-indigo-900"><i class="fas fa-pen"></i></a>
                                        </td>
                                        @else
                                        <td class="px-3 py-2 w-4 text-center text-base font-medium">

                                        </td>
                                        @endif
                                    @endif

                                    @if (in_array('pdf_requirement', $arr_actions))
                                        <td class="px-3 py-2 w-4 text-center text-base font-medium">
                                            <a title="Ver" target="_blank" href="{{ route('pdf-requirement', ['requirement' => $item->id]) }}"
                                                class="text-red-500 hover:text-red-800"><i class="fas fa-file-pdf"></i></a>
                                        </td>
                                    @endif

                                    @if (in_array('delete_requirement', $arr_actions))
                                        @if ($item->status == "P")
                                        <td class="px-3 py-2 w-4 text-center text-base font-medium">
                                            <a title="Anular" href="javascript:void(0)" wire:click="$emit('toggle-modal', '{{ $item->id }}')"
                                                class="text-red-600 hover:text-red-800"><i class="fas fa-trash"></i></a>
                                        </td>
                                        @else
                                        <td class="px-3 py-2 w-4 text-center text-base font-medium">

                                        </td>
                                        @endif

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
