<div>
    <x-general.card-form>
        <x-slot name="head">
            <div>
                <span class="font-bold ml-1 mr-4 text-base align-middle text-white">
                    B&Uacute;SQUEDA
                </span>
            </div>
            <x-general.button class="bg-green-600 text-white hover:bg-green-700 active:bg-green-800 rounded-md mr-2" wire:click="$refresh">
                <i class="fas fa-search" aria-hidden="true"></i> {{ __(' Buscar') }}
            </x-general.button>
        </x-slot>
        <x-slot name="body">
            <div class="grid grid-cols-1 sm:grid-cols-12 gap-4">
                <div class="mb-3 space-y-1 w-full text-xs col-span-1 sm:col-span-4 md:col-span-4 lg:col-span-3 xl:col-span-2">
                    <label for="checkout_id" class="font-semibold text-gray-600">Cajas: </label>
                    <x-general.select name="checkout_id" id="checkout_id" wire:model.defer="checkout_id" wire:keydown.enter="$refresh" wire:change="$refresh">
                        @foreach ($checkouts as $item)
                        <option value="{{ $item->id }}"> {{ $item->name }} </option>
                        @endforeach
                    </x-general.select>
                </div>
                <div class="mb-3 space-y-1 w-full text-xs col-span-1 sm:col-span-4 md:col-span-4 lg:col-span-3 xl:col-span-2">
                    <label for="checkout_detail_id" class="font-semibold text-gray-600">Fecha: </label>
                    <x-general.select name="checkout_detail_id" id="checkout_detail_id" wire:model.defer="checkout_detail_id" wire:keydown.enter="$refresh" wire:change="$refresh">
                        @foreach ($checkout_details as $item)
                        <option value="{{ $item->id }}"> {{ date("d/m/Y H:i:s",strtotime($item->open_date_time)) }} </option>
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


    <x-general.card-table :justify="'justify-start'">
        <x-slot name="head">
            <a href="#" onclick="window.open('{{ route('report-excel-sale-checkout') }}?checkout_id='+$('#checkout_id').val()+'&checkout_detail_id='+$('#checkout_detail_id').val(),'_blank')" >
                <x-general.button class="bg-green-600 text-white hover:bg-green-700 active:bg-green-800 rounded-md mr-2" >
                    <i class="fas fa-file-excel" aria-hidden="true"></i> {{ __(' EXCEL') }}
                </x-general.button>
            </a>
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
                                    $detail = json_decode($item->detail,true);
                                @endphp
                                <tr>
                                    <td class="px-6 py-4 w-4 text-center text-sm text-gray-500">
                                        {{ $init }}
                                    </td>
                                    <td class="px-6 py-4 text-center uppercase text-sm text-gray-500">
                                        {{ date_format(new DateTime($item->date_time), 'd/m/Y') }}
                                    </td>
                                    <td class="px-6 py-4 text-center uppercase text-sm text-gray-500">
                                        {{ $item->number }}
                                    </td>
                                    <td class="px-6 py-4 text-center uppercase text-sm text-gray-500">
                                        {{ $item->reason->name }}
                                    </td>
                                    <td class="px-6 py-4 text-center uppercase text-sm text-gray-500">
                                        {{ !$item->client_id ? "-" : $item->client_name }}
                                    </td>
                                    <td class="px-6 py-4 text-center uppercase text-sm text-gray-500">
                                        {{ in_array($item->reason->name, ['APERTURA', 'CIERRE']) ? "-" : $item->currency->name }}
                                    </td>
                                    <td class="px-6 py-4 text-center uppercase text-sm font-bold {{ $item->document_type_id == 10 ? "text-green-600" : "text-red-600" }}">
                                        {{ in_array($item->reason->name, ['APERTURA', 'CIERRE']) ? "-" : $item->currency->symbol . " " . number_format($item->cash_amount, 2) . ( $item->currency->base != "Y" ? " - " . "S/" . " " . number_format($item->cash_amount * $item->exchange_rate, 2) : "" ) }}
                                    </td>
                                    <td class="px-6 py-4 text-center uppercase text-sm font-bold {{ $item->document_type_id == 10 ? "text-green-600" : "text-red-600" }}">
                                        {{ in_array($item->reason->name, ['APERTURA', 'CIERRE']) ? "-" : $item->currency->symbol . " " . number_format($item->card_amount, 2) . ( $item->currency->base != "Y" ? " - " . "S/" . " " . number_format($item->card_amount * $item->exchange_rate, 2) : "" ) }}
                                    </td>
                                    <td class="px-6 py-4 text-center uppercase text-sm font-bold {{ $item->document_type_id == 10 ? "text-green-600" : "text-red-600" }}">
                                        {{ in_array($item->reason->name, ['APERTURA', 'CIERRE']) ? "-" : $item->currency->symbol . " " . number_format($item->transfer_amount, 2) . ( $item->currency->base != "Y" ? " - " . "S/" . " " . number_format($item->transfer_amount * $item->exchange_rate, 2) : "" ) }}
                                    </td>
                                    <td class="px-6 py-4 text-center uppercase text-sm text-gray-500">
                                        {{ !isset($detail["tipo_documento"]) ? "-" : $detail["tipo_documento"] }}
                                    </td>
                                    <td class="px-6 py-4 text-center uppercase text-sm text-gray-500">
                                        {{ !isset($detail["numero_documento"]) ? "-" : $detail["numero_documento"] }}
                                    </td>
                                    <td class="px-6 py-4 text-center uppercase text-sm text-gray-500">
                                        {{ !isset($detail["fecha_documento"]) ? "-" : $detail["fecha_documento"] }}
                                    </td>
                                    <td class="px-6 py-4 text-center uppercase text-sm ont-bold text-gray-500">
                                        {{ $item->comment() != "" ? $item->comment() : "-" }}
                                    </td>
                                    @if (in_array('delete_sale_checkout', $arr_actions))
                                        @if ($item->status <> "A" && !in_array($item->reason->name, ['APERTURA', 'CIERRE', 'EGRESO FINAL']))
                                        <td class="px-3 py-2 w-4 text-center text-base font-medium">
                                            <a title="Anular" href="javascript:void(0)" wire:click="$emit('toggle-modal', '{{ $item->id }}')"
                                                class="text-red-600 hover:text-red-800"><i class="fas fa-trash"></i></a>
                                        </td>
                                        @else
                                        <td class="px-3 py-2 w-4 text-center text-base font-medium">
                                            -
                                        </td>
                                        @endif

                                    @endif

                                </tr>
                            @endforeach
                        </x-general.table>
                    </div>

                @endif

                <div class="grid grid-cols-1 md:grid-cols-3 gap-3 mt-5">
                    @foreach ($resume as $item)
                    <div class="min-w-full p-3">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-200">
                                <tr>
                                    <th colspan="2" class="px-6 py-2 text-center text-xs font-bold text-gray-600 uppercase tracking-wider">RESUMEN ({{ $item["currency_name"] }})</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr>
                                    <td class="px-6 py-2 text-left uppercase text-sm text-gray-500 font-bold">Total Apertura</td>
                                    <td class="px-6 py-2 text-right uppercase text-sm text-gray-500 font-bold"> {{ $item["currency_symbol"] . " " . number_format($item["total_apertura"], 2) }} </td>
                                </tr>
                                <tr>
                                    <td class="pr-6 pl-8 py-2 text-left uppercase text-xs text-gray-500">Efectivo </td>
                                    <td class="px-6 py-2 text-right uppercase text-xs text-gray-500"> {{ $item["currency_symbol"] . " " . number_format($item["total_apertura_efectivo"], 2) }} </td>
                                </tr>
                                <tr>
                                    <td class="pr-6 pl-8 py-2 text-left uppercase text-xs text-gray-500">Tarjeta </td>
                                    <td class="px-6 py-2 text-right uppercase text-xs text-gray-500"> {{ $item["currency_symbol"] . " " . number_format($item["total_apertura_tarjeta"], 2) }} </td>
                                </tr>
                                <tr>
                                    <td class="pr-6 pl-8 py-2 text-left uppercase text-xs text-gray-500">Dep&oacute;sito </td>
                                    <td class="px-6 py-2 text-right uppercase text-xs text-gray-500"> {{ $item["currency_symbol"] . " " . number_format($item["total_apertura_deposito"], 2) }} </td>
                                </tr>
                                <tr>
                                    <td class="px-6 py-2 text-left uppercase text-sm text-gray-500 font-bold">Total Ventas</td>
                                    <td class="px-6 py-2 text-right uppercase text-sm text-gray-500 font-bold"> {{ $item["currency_symbol"] . " " . number_format($item["total_ventas"], 2) }} </td>
                                </tr>
                                <tr>
                                    <td class="pr-6 pl-8 py-2 text-left uppercase text-xs text-gray-500">Efectivo </td>
                                    <td class="px-6 py-2 text-right uppercase text-xs text-gray-500"> {{ $item["currency_symbol"] . " " . number_format($item["total_ventas_efectivo"], 2) }} </td>
                                </tr>
                                <tr>
                                    <td class="pr-6 pl-8 py-2 text-left uppercase text-xs text-gray-500">Tarjeta </td>
                                    <td class="px-6 py-2 text-right uppercase text-xs text-gray-500"> {{ $item["currency_symbol"] . " " . number_format($item["total_ventas_tarjeta"], 2) }} </td>
                                </tr>
                                <tr>
                                    <td class="pr-6 pl-8 py-2 text-left uppercase text-xs text-gray-500">Dep&oacute;sito </td>
                                    <td class="px-6 py-2 text-right uppercase text-xs text-gray-500"> {{ $item["currency_symbol"] . " " . number_format($item["total_ventas_deposito"], 2) }} </td>
                                </tr>
                                <tr>
                                    <td class="px-6 py-2 text-left uppercase text-sm text-gray-500 font-bold">Otros Ingresos</td>
                                    <td class="px-6 py-2 text-right uppercase text-sm text-gray-500 font-bold"> {{ $item["currency_symbol"] . " " . number_format($item["total_varios"], 2) }} </td>
                                </tr>
                                <tr>
                                    <td class="pr-6 pl-8 py-2 text-left uppercase text-xs text-gray-500">Efectivo </td>
                                    <td class="px-6 py-2 text-right uppercase text-xs text-gray-500"> {{ $item["currency_symbol"] . " " . number_format($item["total_varios_efectivo"], 2) }} </td>
                                </tr>
                                <tr>
                                    <td class="pr-6 pl-8 py-2 text-left uppercase text-xs text-gray-500">Tarjeta </td>
                                    <td class="px-6 py-2 text-right uppercase text-xs text-gray-500"> {{ $item["currency_symbol"] . " " . number_format($item["total_varios_tarjeta"], 2) }} </td>
                                </tr>
                                <tr>
                                    <td class="pr-6 pl-8 py-2 text-left uppercase text-xs text-gray-500">Dep&oacute;sito </td>
                                    <td class="px-6 py-2 text-right uppercase text-xs text-gray-500"> {{ $item["currency_symbol"] . " " . number_format($item["total_varios_deposito"], 2) }} </td>
                                </tr>
                                <tr>
                                    <td class="px-6 py-2 text-left uppercase text-sm text-gray-500 font-bold">Total Egresos</td>
                                    <td class="px-6 py-2 text-right uppercase text-sm text-gray-500 font-bold"> - {{ $item["currency_symbol"] . " " . number_format($item["total_egresos"], 2) }} </td>
                                </tr>
                                <tr>
                                    <td class="pr-6 pl-8 py-2 text-left uppercase text-xs text-gray-500">Efectivo </td>
                                    <td class="px-6 py-2 text-right uppercase text-xs text-gray-500"> - {{ $item["currency_symbol"] . " " . number_format($item["total_egresos_efectivo"], 2) }} </td>
                                </tr>
                                <tr>
                                    <td class="pr-6 pl-8 py-2 text-left uppercase text-xs text-gray-500">Tarjeta </td>
                                    <td class="px-6 py-2 text-right uppercase text-xs text-gray-500"> - {{ $item["currency_symbol"] . " " . number_format($item["total_egresos_tarjeta"], 2) }} </td>
                                </tr>
                                <tr>
                                    <td class="pr-6 pl-8 py-2 text-left uppercase text-xs text-gray-500">Dep&oacute;sito </td>
                                    <td class="px-6 py-2 text-right uppercase text-xs text-gray-500"> - {{ $item["currency_symbol"] . " " . number_format($item["total_egresos_deposito"], 2) }} </td>
                                </tr>
                                <tr>
                                    <td class="px-6 py-2 text-left uppercase text-sm text-gray-500 font-bold">Saldo Final</td>
                                    <td class="px-6 py-2 text-right uppercase text-sm text-gray-500 font-bold"> {{ $item["currency_symbol"] . " " . number_format($item["total_saldo"], 2) }} </td>
                                </tr>
                                <tr>
                                    <td class="pr-6 pl-8 py-2 text-left uppercase text-xs text-gray-500 font-bold">Efectivo </td>
                                    <td class="px-6 py-2 text-right uppercase text-xs text-gray-500 font-bold"> {{ $item["currency_symbol"] . " " . number_format($item["total_saldo_efectivo"], 2) }} </td>
                                </tr>
                                <tr>
                                    <td class="pr-6 pl-8 py-2 text-left uppercase text-xs text-gray-500 font-bold">Tarjeta </td>
                                    <td class="px-6 py-2 text-right uppercase text-xs text-gray-500 font-bold"> {{ $item["currency_symbol"] . " " . number_format($item["total_saldo_tarjeta"], 2) }} </td>
                                </tr>
                                <tr>
                                    <td class="pr-6 pl-8 py-2 text-left uppercase text-xs text-gray-500 font-bold">Dep&oacute;sito </td>
                                    <td class="px-6 py-2 text-right uppercase text-xs text-gray-500 font-bold"> {{ $item["currency_symbol"] . " " . number_format($item["total_saldo_deposito"], 2) }} </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    @endforeach

                </div>
            </div>
        </x-slot>
    </x-general.card-form>

</div>
