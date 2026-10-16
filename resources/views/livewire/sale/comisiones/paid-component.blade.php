<div>
    @php
        $modal_width = "2xl";
        $id = "modal-paid";
    @endphp
    <x-jet-dialog-modal wire:model="open" :id="$id" :maxWidth="$modal_width">
        @if ($model_id != null)
            <x-slot name="title">
                PAGAR COMISION
            </x-slot>
            <x-slot name="content">
                <div class="my-3">
                    {{ $sale->document_type->name }} : {{ $sale->number }}
                </div>
                <div class="overflow-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-200">
                            <tr>
                            	<th colspan="1" class="sticky top-0 py-1 text-center text-xs font-bold bg-blue-900 text-white uppercase tracking-wider">TOTAL</th>
                            	<th colspan="1" class="sticky top-0 py-1 text-center text-xs font-bold bg-blue-900 text-white uppercase tracking-wider">%</th>
                                <th colspan="1" class="sticky top-0 py-1 text-center text-xs font-bold bg-blue-900 text-white uppercase tracking-wider">COMISION</th>
                                <th colspan="1" class="sticky top-0 py-1 text-center text-xs font-bold bg-blue-900 text-white uppercase tracking-wider">MES</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                        	<td class="px-3 py-2 text-center text-xs text-gray-500">
                        		{{ number_format($sale->total,2,'.','') }}
                        	</td>
                        	<td class="px-3 py-2 text-center text-xs text-gray-500">
                                <x-general.input type="text" :full="false" :block="false" class="w-52 mt-0 text-right" placeholder="%" style="width: 80px;" id="porcentaje" wire:model.lazy="porcentaje" name="porcentaje"/>
                                <x-jet-input-error for="porcentaje" />
                            </td>
                            <td class="px-3 py-2 text-center text-xs text-gray-500">
                                <x-general.input type="text" :full="false" :block="false" class="w-52 mt-0 text-right" placeholder="Comision" style="width: 120px;" id="comision" wire:model.lazy="comision" name="comision" readonly=""/>
                            </td>
                            <td class="px-3 py-2 text-center text-xs text-gray-500">
                                <x-general.input type="month" wire:model.defer="month" :full="false" :block="false" class="w-52 mt-0 text-right" style="width: 150px;"/>
                            </td>

                        </tbody>
                    </table>
                </div>
            </x-slot>
        @else
            <x-slot name="title">
                PAGOS
            </x-slot>
            <x-slot name="content">
                NO SE HA SELECCIONADO VENTA
            </x-slot>
        @endif
            <x-slot name="footer">
                <x-jet-secondary-button x-on:click="show=false" wire:loading.attr="disabled">
                    <i class="fas fa-arrow-left mr-2" aria-hidden="true"></i> REGRESAR
                </x-jet-secondary-button>
                @if ($model_id != null && is_null($sale->paid_comision))
                <x-general.button class="bg-green-500 text-white hover:bg-green-600 active:bg-green-700 mx-2" wire:click="save"
                    wire:loading.attr="disabled" wire:target="save" wire:key="save-paid">
                    <i class="fas fa-hand-holding-usd mr-2" aria-hidden="true"></i> {{ __(' PAGAR') }}
                </x-general.button>
                @endif
            </x-slot>
    </x-jet-dialog-modal>
</div>
