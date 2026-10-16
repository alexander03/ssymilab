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
                    <label for="client" class="font-semibold text-gray-600">RUC / Cliente: </label>
                    <x-general.input type="text" name="client" placeholder="Cliente" id="client"
                        wire:model.defer="client" wire:keydown.enter="$refresh"/>
                </div>
                <div class="mb-3 space-y-1 w-full text-xs col-span-1 sm:col-span-4 md:col-span-4 lg:col-span-3 xl:col-span-2">
                    <label for="status_sale" class="font-semibold text-gray-600">Estado: </label>
                    <x-general.select id="status_sale" wire:model.defer="status_sale" wire:change="$refresh">
                        <option value="">TODOS</option>
                        <option value="P">POR PAGAR</option>
                        <option value="C">PAGADO</option>
                        <option value="A">ANULADO</option>
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
            @if (in_array('pdf-vendedor', $arr_actions))
            <a href="#" onclick="window.open('{{ route('pdf-debts-to-cash2') }}?client='+$('#client').val()+'&date_start='+$('#date_start').val()+'&date_end='+$('#date_end').val()+'&status_sale='+$('#status_sale').val()+'&branch_office_id={{$this->branch_office_id}}','_blank')" >
                <x-general.button class="bg-blue-600 text-white hover:bg-blue-700 active:bg-blue-800 rounded-md mr-2" >
                    <i class="fas fa-file-pdf" aria-hidden="true"></i> {{ __(' PDF VENDEDOR') }}
                </x-general.button>
            </a>
            @endif
            @if (in_array('pdf', $arr_actions))
            <a href="#" onclick="window.open('{{ route('pdf-debts-to-cash') }}?client='+$('#client').val()+'&date_start='+$('#date_start').val()+'&date_end='+$('#date_end').val()+'&status_sale='+$('#status_sale').val()+'&branch_office_id={{$this->branch_office_id}}','_blank')" >
                <x-general.button class="bg-red-600 text-white hover:bg-red-700 active:bg-red-800 rounded-md mr-2" >
                    <i class="fas fa-file-pdf" aria-hidden="true"></i> {{ __(' PDF') }}
                </x-general.button>
            </a>
            <a href="#" onclick="window.open('{{ route('excel-debts-to-cash') }}?client='+$('#client').val()+'&date_start='+$('#date_start').val()+'&date_end='+$('#date_end').val()+'&status_sale='+$('#status_sale').val()+'&branch_office_id={{$this->branch_office_id}}','_blank')" >
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
                                        {{ $item->branch_office->name }}
                                    </td>
                                    @if(!is_null($item->document))
                                        <td class="px-6 py-4 text-center uppercase text-sm text-gray-500">
                                            {{ $item->document->number }}
                                        </td>
                                    @else
                                        <td class="px-6 py-4 text-center uppercase text-sm text-gray-500">
                                            -
                                        </td>
                                    @endif
                                    <td class="px-6 py-4 text-center uppercase text-sm text-gray-500">
                                        {{ $item->supplier_document }}
                                    </td>
                                    <td class="px-6 py-4 text-center uppercase text-sm text-gray-500">
                                        {{ $item->supplier_name }}
                                    </td>
                                    <td class="px-6 py-4 text-center uppercase text-sm text-gray-500">
                                        {{ $item->name }}
                                    </td>
                                    <td class="px-6 py-4 text-center uppercase text-sm text-gray-500">
                                        {{ $item->total_amount }}
                                    </td>
                                    <td class="px-6 py-4 text-center uppercase text-sm text-gray-500">
                                        {{ $item->total_amount - $item->cash_amount - $item->card_amount - $item->transfer_amount }}
                                    </td>
                                    <td class="px-6 py-4 text-center uppercase text-sm text-gray-500">
                                        {{ ($item->schedule_date) ? date_format(new DateTime($item->schedule_date), 'd/m/Y') : date_format(new DateTime($item->date_time), 'd/m/Y')}}
                                    </td>
                                    <td class="px-6 py-4 text-center uppercase text-sm text-gray-500">
                                        {{ ($item->status == "P") ? "-" : date_format(new DateTime($item->date_time), 'd/m/Y')}}
                                    </td>
                                    <td class="px-6 py-4 text-center uppercase text-sm text-gray-500">
                                        @if ($item->status == "Y")
                                        <span
                                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            PAGADO
                                        </span>
                                        @elseif ($item->status == "P")
                                        <span
                                            class="w-24 text-center pl-3 pr-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                            POR PAGAR
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
                                </tr>
                                    @if ($item->status == "Y")
                                    <tr>
                                        <td colspan="2" class="px-3 py-2 text-center text-xs text-gray-500 font-bold">
                                            DETALLES DE PAGO
                                        </td>
                                        <td colspan="3" class="px-3 py-2 text-left text-xs text-gray-500 divide-y divide-gray-200">
                                            @if ($item->cash_amount > 0)
                                                <div><span class="font-bold">EFECTIVO:</span> {{ number_format($item->cash_amount, 2) }}</div>
                                            @endif
                                            @if($item->card_amount > 0)
                                                <div><span class="font-bold">TARJETA:</span> {{ number_format($item->card_amount, 2) }}</div>
                                            @endif
                                            @if($item->transfer_amount > 0)
                                                <div><span class="font-bold">DEPOSITO:</span> {{ number_format($item->transfer_amount, 2) }}</div>
                                            @endif
                                        </td>
                                        <td colspan="4" class="px-3 py-2 text-left text-xs text-gray-500 divide-y divide-gray-200">
                                            @php
                                                $payments_per_item = $item->payments;
                                            @endphp
                                            @foreach ($payments_per_item as $payment)
                                                <div><span class="font-bold">{{ $payment->method_name }}:</span> {{ number_format($payment->amount, 2) }} | {{ date('d/m/Y',strtotime($payment->date_time)) }} | {{ $payment->operation_number }}</div>
                                            @endforeach
                                        </td>
                                    </tr>
                                    @endif

                            @endforeach
                        </x-general.table>
                    </div>

                @endif
            </div>
        </x-slot>
    </x-general.card-form>

</div>
