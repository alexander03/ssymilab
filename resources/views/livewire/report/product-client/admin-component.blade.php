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
                        <option value="">TODAS</option>
                        @foreach ($branch_offices as $item)
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
                    <label for="client" class="font-semibold text-gray-600">RUC / Cliente: </label>
                    <x-general.input type="text" name="client" placeholder="Cliente" id="client"
                        wire:model.defer="client" wire:keydown.enter="$refresh"/>
                </div>
                <div class="mb-3 space-y-1 w-full text-xs col-span-1 sm:col-span-4 md:col-span-4 lg:col-span-3 xl:col-span-2">
                    <label for="zone_id" class="font-semibold text-gray-600">Zona: </label>
                    <x-general.select id="zone_id" wire:model.defer="zone_id" wire:change="$refresh">
                        <option value="">TODAS</option>
                        @foreach ($zones as $item)
                            <option value="{{ $item->id }}">{{ $item->name }}</option>
                        @endforeach
                        <option value="0">SIN ZONA</option>
                    </x-general.select>
                </div>
                <div class="mb-3 space-y-1 w-full text-xs col-span-1 sm:col-span-4 md:col-span-4 lg:col-span-3 xl:col-span-2">
                    <label for="brand_id" class="font-semibold text-gray-600">Marca: </label>
                    <x-general.select id="brand_id" wire:model.defer="brand_id" wire:change="$refresh">
                        <option value="">TODAS</option>
                        @foreach ($brands as $item)
                            <option value="{{ $item->id }}">{{ $item->name }}</option>
                        @endforeach
                    </x-general.select>
                </div>
                <div class="mb-3 space-y-1 w-full text-xs col-span-1 sm:col-span-4 md:col-span-4 lg:col-span-3 xl:col-span-2">
                    <label for="product" class="font-semibold text-gray-600">Producto: </label>
                    <x-general.input type="text" name="product" placeholder="Nombre o código" id="product"
                        wire:model.defer="product" wire:keydown.enter="$refresh"/>
                </div>
                <div class="mb-3 space-y-1 w-full text-xs col-span-1 sm:col-span-4 md:col-span-4 lg:col-span-3 xl:col-span-2">
                    <label for="rows" class="font-semibold text-gray-600">Clientes por página: </label>
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
            @php
                $export_params = "?branch_office_id='+$('#branch_office_id').val()+'&date_start='+$('#date_start').val()+'&date_end='+$('#date_end').val()+'&client='+encodeURIComponent($('#client').val())+'&zone_id='+$('#zone_id').val()+'&brand_id='+$('#brand_id').val()+'&product='+encodeURIComponent($('#product').val())+'";
            @endphp
            @if (in_array('pdf_report_product_client', $arr_actions))
                <button class="items-center px-3 py-2 border border-transparent rounded-md font-semibold text-xs uppercase tracking-widest focus:outline-none focus:ring disabled:opacity-25 transition bg-red-600 text-white hover:bg-red-700 active:bg-red-800 rounded-md mr-2" onclick="window.open('{{ route('report-pdf-product-client') }}{!! $export_params !!}','_blank');">
                    <i class="fas fa-file-pdf" aria-hidden="true"></i> {{ __(' PDF') }}
                </button>
            @endif
            @if (in_array('excel_report_product_client', $arr_actions))
                <button class="items-center px-3 py-2 border border-transparent rounded-md font-semibold text-xs uppercase tracking-widest focus:outline-none focus:ring disabled:opacity-25 transition bg-green-600 text-white hover:bg-green-700 active:bg-green-800 rounded-md mr-2" onclick="window.open('{{ route('report-excel-product-client') }}{!! $export_params !!}','_blank');">
                    <i class="fas fa-file-excel" aria-hidden="true"></i> {{ __(' EXCEL') }}
                </button>
            @endif
        </x-slot>

        <x-slot name="body">
            <div wire:loading wire:target="$refresh" wire:loading.class="text-gray-400">
                Cargando registros ...
            </div>
            <div wire:loading.remove wire:target="$refresh">
                @if (count($clients) == 0)
                    <span class="text-2xl text-yellow-500 font-bold"> No se encontraron registros</span>
                @else
                    @if ($clients->hasPages())
                        <div class="px-1 pb-3">
                            {{ $clients->onEachSide(0)->links() }}
                        </div>
                    @endif
                    @foreach ($clients as $client_item)
                        @php
                            $client_data = $details[$client_item->client_id] ?? ["products" => [], "brands" => []];
                        @endphp
                        <div class="rounded shadow mb-4 bg-white">
                            <div class="px-4 py-2 bg-blue-900 text-white text-sm font-bold flex justify-between">
                                <span>{{ $client_item->client_name }} ({{ $client_item->client_document }})</span>
                                <span>IMPORTE: S/ {{ number_format($client_item->amount, 2) }}</span>
                            </div>
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-200">
                                    <tr>
                                        <th class="px-3 py-2 text-center text-xs font-bold text-gray-700 uppercase">Código</th>
                                        <th class="px-3 py-2 text-left text-xs font-bold text-gray-700 uppercase">Producto</th>
                                        <th class="px-3 py-2 text-center text-xs font-bold text-gray-700 uppercase">Marca</th>
                                        <th class="px-3 py-2 text-center text-xs font-bold text-gray-700 uppercase">Unidad</th>
                                        <th class="px-3 py-2 text-right text-xs font-bold text-gray-700 uppercase">Cantidad</th>
                                        <th class="px-3 py-2 text-right text-xs font-bold text-gray-700 uppercase">Importe (S/)</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    @foreach ($client_data["products"] as $row)
                                        <tr>
                                            <td class="px-3 py-2 text-center text-xs text-gray-500">{{ $row->product_code }}</td>
                                            <td class="px-3 py-2 text-left text-xs text-gray-500 uppercase">{{ $row->product_name }}</td>
                                            <td class="px-3 py-2 text-center text-xs text-gray-500">{{ $row->brand_name }}</td>
                                            <td class="px-3 py-2 text-center text-xs text-gray-500">{{ $row->unit_name }}</td>
                                            <td class="px-3 py-2 text-right text-xs text-gray-500">{{ number_format($row->quantity, 2) }}</td>
                                            <td class="px-3 py-2 text-right text-xs text-gray-500">{{ number_format($row->amount, 2) }}</td>
                                        </tr>
                                    @endforeach
                                    <tr class="bg-blue-50">
                                        <td colspan="6" class="px-3 py-2 text-left text-xs text-blue-800 font-bold">RESUMEN POR MARCA</td>
                                    </tr>
                                    @foreach ($client_data["brands"] as $brand)
                                        <tr class="bg-gray-50">
                                            <td colspan="2"></td>
                                            <td class="px-3 py-2 text-center text-xs text-gray-700 font-bold">{{ $brand->brand_name }}</td>
                                            <td class="px-3 py-2 text-center text-xs text-gray-700 font-bold">{{ $brand->unit_name }}</td>
                                            <td class="px-3 py-2 text-right text-xs text-gray-700 font-bold">{{ number_format($brand->quantity, 2) }}</td>
                                            <td class="px-3 py-2 text-right text-xs text-gray-700 font-bold">{{ number_format($brand->amount, 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endforeach
                    @if ($clients->hasPages())
                        <div class="px-1 pb-3">
                            {{ $clients->onEachSide(0)->links() }}
                        </div>
                    @endif
                @endif
            </div>
        </x-slot>
    </x-general.card-table>

</div>
