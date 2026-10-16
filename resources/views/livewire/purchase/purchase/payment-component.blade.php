<div>
    @php
        $modal_width = "2xl";
        $id = "modal-dispatched";
    @endphp
    <x-jet-dialog-modal wire:model="open" :id="$id" :maxWidth="$modal_width">
        @if ($model_id != null)
            <x-slot name="title">
                PAGOS
            </x-slot>
            <x-slot name="content">
                <div class="my-3">
                    {{ $purchase->document_type->name }} : {{ $purchase->number }}
                </div>
                <div class="overflow-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-200">
                            <tr>
                                <th colspan="1" class="sticky top-0 py-1 text-center text-xs font-bold bg-blue-900 text-white uppercase tracking-wider">CUOTA</th>
                                <th colspan="1" class="sticky top-0 py-1 text-center text-xs font-bold bg-blue-900 text-white uppercase tracking-wider">MONTO</th>
                                <th colspan="1" class="sticky top-0 py-1 text-center text-xs font-bold bg-blue-900 text-white uppercase tracking-wider">FECHA</th>
                                <th colspan="1" class="sticky top-0 py-1 text-center text-xs font-bold bg-blue-900 text-white uppercase tracking-wider">FECHA DE PAGO</th>
                                <th colspan="1" class="sticky top-0 py-1 text-center text-xs font-bold bg-blue-900 text-white uppercase tracking-wider">ESTADO</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach ($document_payments as $item)
                            <tr>
                                <td class="px-3 py-2 text-center text-xs text-gray-500">
                                    {{ $item->name }}
                                </td>
                                <td class="px-3 py-2 text-center text-xs text-gray-500">
                                    {{ $item->currency->symbol }} {{ number_format($item->total_amount, 2) }}
                                </td>
                                <td class="px-3 py-2 text-center text-xs text-gray-500">
                                    {{ ($item->status == "P") ? date_format(new DateTime($item->schedule_date), 'd/m/Y') : date_format(new DateTime($item->date_time), 'd/m/Y')}}
                                </td>
                                <td class="px-3 py-2 text-center text-xs text-gray-500">
                                    {{ ($item->status == "P") ? "-" : date_format(new DateTime($item->date_time), 'd/m/Y')}}
                                </td>
                                <td class="px-3 py-2 text-center text-xs text-gray-500">
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
                            @if ($item->status == "Y" || $item->status == "P")
                            <tr>
                                <td colspan="1" class="px-3 py-2 text-center text-xs text-gray-500 font-bold">
                                    DETALLES DE PAGO
                                </td>
                                <td colspan="2" class="px-3 py-2 text-left text-xs text-gray-500 divide-y divide-gray-200">
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
                                <td colspan="2" class="px-3 py-2 text-left text-xs text-gray-500 divide-y divide-gray-200">
                                    @php
                                        $payments_per_item = $item->payments;
                                    @endphp
                                    @foreach ($payments_per_item as $payment)
                                        <div><span class="font-bold">{{ $payment->method_name }}:</span> {{ number_format($payment->amount, 2)." | ".date("d/m/Y",strtotime($payment->date_time))." | ".$payment->operation_number }}</div>
                                    @endforeach
                                </td>
                            </tr>
                            @endif
                            @endforeach
                        </tbody>
                    </table>

                    @if ($current_payment_id && $purchase->status != 'A')
                        <div class="mt-6 border-2 border-gray-200 p-3 rounded-md">
                            <div class="w-full text-left text-base font-bold pl-5 mt-3">
                                PAGAR - {{ $current_payment_name }}
                            </div>
                            <div class="w-full text-left text-base font-bold pl-5 mt-1">
                                TOTAL: {{ $current_payment_symbol }} {{ number_format($current_payment_amount, 2) }}
                            </div>
                            <div class="grid grid-cols-12">
                                <div class="col-span-12 xl:col-span-12 grid grid-cols-1 divide-y divide-gray-100">
                                    <div class=" divide-y divide-gray-100">
                                        @foreach ($payments as $key => $item)
                                        <div class="grid justify-items-end w-full md:pr-5">
                                            <div class="grid sm:flex items-center p-2">
                                                <x-jet-label for="barcode" class="text-xs min-w-max mt-1 mr-3">{{ $item["name"] }}: </x-jet-label>
                                                <div class="flex">
                                                    <x-general.input type="date" wire:model.lazy="payments.{{ $key }}.date" :full="false" :block="false" class="w-52 mt-0 text-right" style="width: 130px;"/>
                                                </div>
                                                <div class="flex">
                                                    <x-general.input type="text" wire:model.lazy="payments.{{ $key }}.operation_number" :full="false" :block="false" class="w-52 mt-0 text-right" placeholder="Nro. OP." style="width: 120px;"/>
                                                </div>
                                                <div class="flex">
                                                    <x-general.input type="number" wire:model.lazy="payments.{{ $key }}.amount" :full="false" :block="false" class="w-52 mt-0 text-right" style="width: 180px;"/>
                                                    <button class="ml-3" wire:click="quit_method_item('{{ $key }}')" wire:loading.attr="disabled" wire:target="quit_method_item('{{ $key }}')"
                                                            wire:key="quit-payment-{{ $key }}"><i class="fas fa-times text-red-600 text-base"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                    <div class="grid justify-items-end w-full pr-6 md:pr-11 p-2">
                                        <div x-data="{ open_method: false }">
                                            <div x-show="open_method" class="col-span-1 pr-2 pt-0 relative w-full">
                                                <ul class="absolute bg-gray-100 shadow-md max-w-full right-2 left-0 z-50 bottom-full">
                                                    @foreach ($payment_methods as $key => $payment_method)
                                                        <li wire:click="select_payment_method('{{ $key }}')"  class="py-2 px-2 hover:bg-blue-100 cursor-pointer text-center">{{ $payment_method["name"]}}</li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                            <div @click.away="open_method = false" class="py-2 sm:mr-2 col-span-1">
                                                <x-general.button class="bg-blue-300 text-white hover:bg-blue-400 active:bg-blue-500 mx-2"
                                                x-on:click="open_method = !open_method;"><i class="fas fa-plus mr-2" aria-hidden="true"></i> Método de pago </x-general.button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    @endif

                </div>
            </x-slot>
        @else
            <x-slot name="title">
                PAGOS
            </x-slot>
            <x-slot name="content">
                NO SE HA SELECCIONADO COMPRA
            </x-slot>
        @endif
        <x-slot name="footer">
            <x-jet-secondary-button x-on:click="show=false" wire:loading.attr="disabled">
                <i class="fas fa-arrow-left mr-2" aria-hidden="true"></i> REGRESAR
            </x-jet-secondary-button>
            @if ($model_id && $purchase->status == "P")
                <x-general.button class="bg-green-500 text-white hover:bg-green-600 active:bg-green-700 mx-2" wire:click="save"
                    wire:loading.attr="disabled" wire:target="save" wire:key="save-payment">
                    <i class="fas fa-hand-holding-usd mr-2" aria-hidden="true"></i> {{ __(' PAGAR') }}
                </x-general.button>
            @endif
        </x-slot>
    </x-jet-dialog-modal>
</div>
