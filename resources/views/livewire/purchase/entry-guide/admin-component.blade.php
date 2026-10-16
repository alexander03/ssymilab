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
                    <label for="document_type_id" class="font-semibold text-gray-600">Tipo Doc.: </label>
                    <x-general.select id="document_type_id" name="document_type_id" wire:model.defer="document_type_id" wire:change="$refresh">
                        <option value="">TODOS</option>
                        @foreach ($document_types as $item)
                            <option value="{{ $item->id }}">{{ $item->name }}</option>
                        @endforeach
                    </x-general.select>
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

    @if (in_array('delete_entry_guide', $arr_actions))
        @livewire('purchase.entry-guide.delete-component')
    @endif

    @if (in_array('dispatched_entry_guide', $arr_actions))
        @livewire('purchase.entry-guide.dispatched-component')
    @endif

    <x-general.card-table>
        @if (in_array('create_entry_guide', $arr_actions))
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
                                @endphp
                                <tr>
                                    <td class="px-6 py-4 w-4 text-center text-sm text-gray-500">
                                        {{ $init }}
                                    </td>
                                    <td class="px-6 py-4 text-center uppercase text-sm text-gray-500">
                                        {{ $item->document_type->name }}
                                    </td>
                                    <td class="px-6 py-4 text-center uppercase text-sm text-gray-500">
                                        {{ $item->number }}
                                    </td>
                                    <td class="px-6 py-4 text-center uppercase text-sm text-gray-500">
                                        {{ $item->document->number }}
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
                                        @if ($item->dispatched == "Y")
                                        <span
                                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            COMPLETO
                                        </span>
                                        @elseif ($item->dispatched == "E")
                                        <span
                                            class="pr-2 pl-4 inline-flex text-xs w-32 text-center leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                            CON DIFERENCIA
                                        </span>
                                        @elseif ($item->dispatched == "N")
                                        <span
                                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                            PENDIENTE
                                        </span>
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-center uppercase text-sm text-gray-500">
                                        @if ($item->status == "Y")
                                        <span
                                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            ACTIVO
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
                                    @if (in_array('show_entry_guide', $arr_actions))
                                        <td class="px-3 py-2 w-4 text-center text-base font-medium">
                                            <a title="Ver" href="{{ route('show-entry-guide', ['entry_guide' => $item->id]) }}"
                                                class="text-blue-400 hover:text-blue-700"><i class="fas fa-eye"></i></a>
                                        </td>
                                    @endif


                                    {{-- @if (in_array('edit_entry_guide', $arr_actions))
                                        @if ($item->status == "P")
                                        <td class="px-3 py-2 w-4 text-center text-base font-medium">
                                            <a title="Editar" href="{{ route('edit-entry-guide', ['entry_guide' => $item->id]) }}"
                                                class="text-indigo-600 hover:text-indigo-900"><i class="fas fa-pen"></i></a>
                                        </td>
                                        @else
                                        <td class="px-3 py-2 w-4 text-center text-base font-medium">

                                        </td>
                                        @endif
                                    @endif --}}

                                    @if (in_array('dispatched_entry_guide', $arr_actions))
                                        <td class="px-3 py-2 w-4 text-center text-base font-medium">
                                            <a title="Confirmar Ingreso" href="javascript:void(0)" wire:click="$emit('toggle-modal-dispatched', '{{ $item->id }}')"
                                                class="text-green-600 hover:text-green-800"><i class="fas fa-box-open"></i></a>
                                        </td>
                                    @endif

                                    @if (in_array('pdf_entryguide', $arr_actions))
                                        <td class="px-3 py-2 w-4 text-center text-base font-medium">
                                            <a title="Ver Recepcion" target="_blank" href="{{ route('pdf-entryguide', ['entryguide' => $item->id]) }}"
                                                class="text-red-500 hover:text-red-800"><i class="fas fa-file-pdf"></i></a>
                                        </td>
                                    @endif

                                    @if (in_array('pdf2_entryguide', $arr_actions))
                                        <td class="px-3 py-2 w-4 text-center text-base font-medium">
                                            <a title="Ver Formato Liberacion" target="_blank" href="{{ route('pdf2-entryguide', ['entryguide' => $item->id]) }}"
                                                class="text-blue-500 hover:text-blue-800"><i class="fas fa-file-pdf"></i></a>
                                        </td>
                                    @endif
                                    <td class="px-3 py-2 w-4 text-center text-base font-medium">
                                        <a title="Ver PDF" target="_blank" href="{{ route('pdf3-entryguide', ['entryguide' => $item->id]) }}"
                                            class="text-green-500 hover:text-blue-800"><i class="fas fa-file-pdf"></i></a>
                                    </td>

                                    @if (in_array('delete_entry_guide', $arr_actions))
                                        @if ($item->status <> "P")
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
