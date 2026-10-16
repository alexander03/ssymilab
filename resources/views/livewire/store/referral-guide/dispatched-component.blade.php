<div>
    @php
        $modal_width = "2xl";
        $id = "modal-dispatched";
    @endphp
    <x-jet-dialog-modal wire:model="open" :id="$id" :maxWidth="$modal_width">
        @if ($model_id != null)
            <x-slot name="title">
                CONFIRMAR SALIDA
            </x-slot>
            <x-slot name="content">
                @if ($model_id && $referral_guide->dispatched == "N" && $referral_guide->status <> "A")
                <div class="m-2 p-1 col-span-1 sm:col-span-2 grid grid-cols-1 sm:grid-cols-12">
                    <div class="p-2 col-span-1 sm:col-span-12">
                        <x-jet-label for="search_barcode">Codigo de barras</x-jet-label>
                        <x-general.input type="text" id="search_barcode" wire:model.defer="search_barcode" wire:keydown.enter="check_barcode"/>
                    </div>
                </div>
                @endif
                <div class="overflow-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-200">
                            <tr>
                                <th colspan="1" class="sticky top-0 py-1 w-10/12 text-center text-xs font-bold bg-blue-900 text-white uppercase tracking-wider">Producto</th>
                                <th colspan="1" class="sticky top-0 py-1 w-2/12 text-center text-xs font-bold bg-blue-900 text-white uppercase tracking-wider">Cantidad</th>
                                <th colspan="1" class="sticky top-0 py-1 w-2/12 text-center text-xs font-bold bg-blue-900 text-white uppercase tracking-wider"></th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @if (count($details) == 0)
                            <tr>
                                <td colspan="6" class="px-3 py-2 text-center text-xs text-gray-500"> No tiene detalles. </td>
                            </tr>
                            @else
                                @foreach ($details as $key => $item)
                                    <tr>
                                        @php
                                            $product = \App\Models\Product::where('id', $item["product_id"])->withTrashed()->first();
                                        @endphp
                                        <td colspan="1" class="px-3 py-2 text-center text-sm text-gray-500"> {{ $product->name.' '.$product->model.' '.$product->presentation.' '.$product->brand->name }} </td>
                                        <td colspan="1" class="px-3 py-2 text-center text-sm text-gray-500">
                                            @if ($item["dispatched_quantity"] ==  $item["quantity"])
                                                <i class="fas fa-check text-green-500"></i>
                                            @else
                                                <i class="fas fa-exclamation-triangle text-yellow-500"></i>
                                            @endif
                                             {{ sprintf('%g', $item["dispatched_quantity"]) . "/" . sprintf('%g', $item["quantity"]) }}
                                        </td>
                                        <td></td>
                                    </tr>
                                    @foreach ($item["product_moves"] as $key2 => $product_move)
                                        @if ($product_move["product"]["type"] !="R" && $product_move["product"]["type"] !="O")
                                        <tr>
                                            <td colspan="1" class="px-3 py-2 text-left text-sm text-gray-500">
                                                <div class="flex align-middle">
                                                    <div class="mr-3 w-5 flex items-center">
                                                        <i class="fas fa-angle-double-right"></i>
                                                    </div>
                                                    <div>
                                                        <p>Serie: {{ $product_move["product_serie"]["serie"] }}</p>
                                                    </div>

                                                </div>
                                            </td>
                                            <td colspan="1" class="px-3 py-2 text-center text-sm text-gray-500">
                                                @if ($product_move["dispatched_quantity"] ==  $product_move["quantity"])
                                                    <i class="fas fa-check text-green-500"></i>
                                                @else
                                                    <i class="fas fa-exclamation-triangle text-yellow-500"></i>
                                                @endif
                                                {{ sprintf('%g', $product_move["dispatched_quantity"]) . "/" . sprintf('%g', $product_move["quantity"]) }}
                                            </td>
                                            <td  colspan="1" class="px-3 py-2 text-left text-sm text-gray-500">
                                                @if ($model_id && $referral_guide->dispatched == "N" && $referral_guide->status <> "A")
                                                <i class="fas fa-plus-circle text-green-500 text-base cursor-pointer" wire:key="add-product-move-{{$key}}-{{$key2}}" wire:click="add_product_move('{{$key}}', '{{$key2}}')"></i>
                                                <i class="fas fa-plus-circle text-blue-500 text-base cursor-pointer" wire:key="add-product-move-{{$key}}-{{$key2}}" wire:click="add_product_move_all('{{$key}}', '{{$key2}}')"></i>
                                                <i class="fas fa-minus-circle text-red-500 text-base cursor-pointer" wire:key="quit-product-move-{{$key}}-{{$key2}}" wire:click="quit_product_move('{{$key}}', '{{$key2}}')"></i>
                                                @endif
                                            </td>
                                        </tr>
                                        @else
                                        @if ($key == $detail_key && $key2 == $lote_key)
                                            <tr class="bg-blue-100">
                                                <td colspan="1" class="px-3 py-2 text-left text-sm text-gray-500" wire:key="unselect_lot-{{$key}}-{{$key2}}" wire:click="unselect_lot">
                                        @else
                                            <tr>
                                                <td colspan="1" class="px-3 py-2 text-left text-sm text-gray-500" wire:key="select_lot-{{$key}}-{{$key2}}" wire:click="select_lot('{{$key}}', '{{$key2}}')">
                                        @endif
                                                <div class="flex align-middle">
                                                    <div class="mr-3 w-5 flex items-center">
                                                        <i class="fas fa-angle-double-right"></i>
                                                    </div>
                                                    <div>
                                                        <p>Lote: {{ $product_move["lot"]["lot"] }}</p>
                                                        <p>Cod. Barra: {{ $product_move["lot"]["barcode"] }}</p>
                                                        <p>F. V.: {{ $product_move["lot"]["due_date"] }}</p>
                                                        <p>Cantidad: {{ sprintf('%g', $product_move["quantity"]) }}</p>
                                                    </div>

                                                </div>
                                            </td>
                                            <td colspan="1" class="px-3 py-2 text-center text-sm text-gray-500">
                                                @if ($product_move["dispatched_quantity"] ==  $product_move["quantity"])
                                                    <i class="fas fa-check text-green-500"></i>
                                                @else
                                                    <i class="fas fa-exclamation-triangle text-yellow-500"></i>
                                                @endif
                                                {{ sprintf('%g', $product_move["dispatched_quantity"]) . "/" . sprintf('%g', $product_move["quantity"]) }}
                                            </td>
                                            <td  colspan="1" class="px-3 py-2 text-left text-sm text-gray-500">
                                                @if ($model_id && $referral_guide->dispatched == "N" && $referral_guide->status <> "A")
                                                    <i class="fas fa-plus-circle text-green-500 text-base cursor-pointer" wire:key="add-product-move-{{$key}}-{{$key2}}" wire:click="add_product_move('{{$key}}', '{{$key2}}')"></i>
                                                    <i class="fas fa-plus-circle text-blue-500 text-base cursor-pointer" wire:key="add-product-move-{{$key}}-{{$key2}}" wire:click="add_product_move_all('{{$key}}', '{{$key2}}')"></i>
                                                    <i class="fas fa-minus-circle text-red-500 text-base cursor-pointer" wire:key="quit-product-move-{{$key}}-{{$key2}}" wire:click="quit_product_move('{{$key}}', '{{$key2}}')"></i>
                                                @endif
                                            </td>
                                        </tr>
                                        @endif
                                    @endforeach
                                @endforeach
                                @endif
                        </tbody>
                    </table>
                </div>
            </x-slot>
        @else
            <x-slot name="title">
                CONFIRMAR INGRESO
            </x-slot>
            <x-slot name="content">
                NO SE HA SELECCIONADO GUIA
            </x-slot>
        @endif
        <x-slot name="footer">
            <x-jet-secondary-button x-on:click="show=false" wire:loading.attr="disabled">
                <i class="fas fa-arrow-left mr-2" aria-hidden="true"></i> REGRESAR
            </x-jet-secondary-button>
            @if ($model_id && $referral_guide->dispatched == "N" && $referral_guide->status <> "A")
                <x-general.button class="bg-blue-500 text-white hover:bg-blue-600 active:bg-blue-700 mx-2" wire:click="save"
                    wire:loading.attr="disabled" wire:target="save" wire:key="save-dispatched">
                    <i class="fas fa-save mr-2" aria-hidden="true"></i> {{ __(' FINALIZAR') }}
                </x-general.button>
            @endif
        </x-slot>
    </x-jet-dialog-modal>
</div>
