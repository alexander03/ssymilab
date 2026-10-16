@php
    use App\Librerias\Libreria;
@endphp
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
                <div class="mb-3 space-y-1 w-full text-xs col-span-1 sm:col-span-4 md:col-span-4 lg:col-span-3 xl:col-span-2" style="display: none;">
                    <label for="date_start" class="font-semibold text-gray-600">Desde: </label>
                    <x-general.input type="date" name="date_start" id="date_start"
                        wire:model.defer="date_start" wire:keydown.enter="$refresh"/>
                </div>
                <div class="mb-3 space-y-1 w-full text-xs col-span-1 sm:col-span-4 md:col-span-4 lg:col-span-3 xl:col-span-2" style="display: none;">
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
                    <label for="supplier" class="font-semibold text-gray-600">Cliente: </label>
                    <x-general.input type="text" name="supplier" placeholder="Cliente" id="client"
                        wire:model.defer="client" wire:keydown.enter="$refresh"/>
                </div>
                <div class="mb-3 space-y-1 w-full text-xs col-span-1 sm:col-span-4 md:col-span-4 lg:col-span-3 xl:col-span-2">
                    <label for="supervisor" class="font-semibold text-gray-600">Supervisor: </label>
                    <x-general.input type="text" name="supervisor" placeholder="Supervisor" id="supervisor"
                        wire:model.defer="supervisor" wire:keydown.enter="$refresh"/>
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
    @if (in_array('paid', $arr_actions))
        @livewire('sale.comisionessupervisor.paid-component')
    @endif
    <x-general.card-table>
        <x-slot name="head">
            @if (in_array('pdf', $arr_actions))
            <div class="mb-3 space-y-1 w-full text-xs col-span-1 sm:col-span-4 md:col-span-4 lg:col-span-3 xl:col-span-2">
                <input class="mt-1 placeholder-gray-400 text-xs md:text-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm disabled:opacity-60" type="month" id="mes" value="<?=date("Y-m")?>">
            </div>
            <button class="items-center px-3 py-2 border border-transparent rounded-md font-semibold text-xs uppercase tracking-widest focus:outline-none focus:ring active:bg-gray-900 focus:border-gray-900 focus:ring-gray-300  disabled:opacity-25 transition bg-red-600 text-white hover:bg-red-700 active:bg-red-800 rounded-md mr-2" onclick="window.open('{{ route('pdf-comisionessupervisor') }}?mes='+$('#mes').val(),'_blank');">
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
                                @endphp
                                <tr>
                                    <td class="px-6 py-4 w-4 text-center text-sm text-gray-500">
                                        {{ $init }}
                                    </td>
                                    <td class="px-6 py-4 text-center uppercase text-sm text-gray-500">
                                        {{ $item->number }}
                                    </td>
                                    <td class="px-6 py-4 text-center uppercase text-sm text-gray-500">
                                        {{ date("d/m/Y",strtotime($item->date_time)) }}
                                    </td>
                                    <td class="px-6 py-4 text-center uppercase text-sm text-gray-500">
                                        {{ $item->supplier_name }}
                                    </td>
                                    <td class="px-6 py-4 text-center uppercase text-sm text-gray-500">
                                        {{ $item->person_name }}
                                    </td>
                                    <td class="px-6 py-4 text-center uppercase text-sm text-gray-500">
                                        {{ $item->supervisor_name }}
                                    </td>
                                    <td class="px-6 py-4 text-center uppercase text-sm text-gray-500">
                                        {{ number_format($item->total,2) }}
                                    </td>
                                    <td class="px-6 py-4 text-center uppercase text-sm text-gray-500">
                                        {{ !is_null($item->paid_comision_supervisor)?(Libreria::mes(date_format(new DateTime($item->paid_comision_supervisor), 'n')) .' DEL '. date_format(new DateTime($item->paid_comision_supervisor), 'Y')):'-' }}
                                    </td>
                                    <td class="px-6 py-4 text-center uppercase text-sm text-gray-500">
                                        {{ is_null($item->paid_comision_supervisor)?'-':(number_format($item->percentage_comision_supervisor,1,'.','')) }}
                                    </td>
                                    <td class="px-6 py-4 text-center uppercase text-sm text-gray-500">
                                        {{ is_null($item->paid_comision_supervisor)?'-':(number_format($item->total*($item->percentage_comision_supervisor/100)/1.18,2,'.','')) }}
                                    </td>
                                    <td class="px-6 py-4 text-center uppercase text-sm text-gray-500">
                                        @if (!is_null($item->paid_comision_supervisor))
                                        <span
                                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            PAGADO
                                        </span>
                                        @else
                                        <span
                                            class="w-24 text-center pl-3 pr-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                            POR PAGAR
                                        </span>
                                        @endif
                                    </td>
                                    @if (in_array('paid', $arr_actions))
                                        @if (is_null($item->paid_comision_supervisor))
                                            <td class="px-3 py-2 w-4 text-center text-base font-medium">
                                                <a title="Pagar Comision" href="javascript:void(0)" wire:click="$emit('toggle-modal-paid', '{{ $item->id }}')"
                                                class="text-green-600 hover:text-green-800"><i class="fas fa-hand-holding-usd"></i></a>
                                            </td>
                                        @else
                                            <td> - </td>
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
