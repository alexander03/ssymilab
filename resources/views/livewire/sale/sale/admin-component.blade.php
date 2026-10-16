@php
use Illuminate\Support\Facades\DB;
@endphp
<div>
    <x-general.card-form>
        <x-slot name="head">
            <div>
                <span class="font-bold ml-1 mr-4 text-base align-middle text-white">
                    BÚSQUEDA
                </span>
            </div>
            <div class="flex">
                <span class="font-bold ml-1 mr-4 text-base align-middle text-white">{{ $search }}</span>
                <div class="space-y-1 text-xs col-span-1 sm:col-span-4 md:col-span-4 lg:col-span-3 xl:col-span-1 mr-4 h-9 mt-0">
                    <x-general.select id="rows" name="rows" class="mt-0" wire:model.defer="rows" wire:change="$refresh">
                        <option value="10">10</option>
                        <option value="20">20</option>
                        <option value="30">30</option>
                    </x-general.select>
                </div>
                <x-general.button class="bg-green-600 text-white hover:bg-green-700 active:bg-green-800 rounded-md mr-2" wire:click="$refresh">
                    <i class="fas fa-search" aria-hidden="true"></i> {{ __(' Buscar') }}
                </x-general.button>
            </div>
        </x-slot>
        <x-slot name="body">
            <div class="grid grid-cols-1 sm:grid-cols-12 gap-4">
                <div class="mb-3 space-y-1 w-full text-xs col-span-1 sm:col-span-4 md:col-span-4 lg:col-span-3 xl:col-span-2">
                    <label for="date_start" class="font-semibold text-gray-600">Desde: </label>
                    <x-general.input type="date" name="date_start" id="date_start"
                        wire:model.defer="date_start" wire:keydown.enter="$refresh" wire:keyup="$refresh"/>
                </div>
                <div class="mb-3 space-y-1 w-full text-xs col-span-1 sm:col-span-4 md:col-span-4 lg:col-span-3 xl:col-span-2">
                    <label for="date_end" class="font-semibold text-gray-600">Hasta: </label>
                    <x-general.input type="date" name="date_end" id="date_end"
                        wire:model.defer="date_end" wire:keydown.enter="$refresh" wire:keyup="$refresh"/>
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
                    <label for="supplier" class="font-semibold text-gray-600">Cliente: </label>
                    <x-general.input type="text" name="supplier" placeholder="Cliente" id="supplier"
                        wire:model.defer="supplier" wire:keydown.enter="$refresh"/>
                </div>
                <div class="mb-3 space-y-1 w-full text-xs col-span-1 sm:col-span-4 md:col-span-4 lg:col-span-3 xl:col-span-2">
                    <label for="vendedor" class="font-semibold text-gray-600">Vendedor: </label>
                    <x-general.input type="text" name="vendedor" placeholder="Vendedor" id="vendedor"
                        wire:model.defer="vendedor" wire:keydown.enter="$refresh"/>
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
            </div>
        </x-slot>
    </x-general.card-form>

    @if (in_array('delete_sale', $arr_actions))
        @livewire('sale.sale.delete-component')
    @endif

    @if (in_array('payment_sale', $arr_actions))
        @livewire('sale.sale.payment-component')
    @endif

    <x-general.card-table>
        @if (in_array('create_sale', $arr_actions))
            <x-slot name="head">
                <x-general.button class="bg-blue-600 text-white hover:bg-blue-700 active:bg-blue-800 rounded-md mr-2" wire:click="create_route">
                    <i class="fas fa-plus" aria-hidden="true"></i> {{ __(' Nuevo') }}
                </x-general.button>
                <a href="#" onclick="window.open('{{ route('excel-sale') }}?supplier='+$('#supplier').val()+'&date_start='+$('#date_start').val()+'&date_end='+$('#date_end').val()+'&status_sale='+$('#status_sale').val()+'&number='+$('#number').val()+'&document_type_id='+$('#document_type_id').val()+'&vendedor='+$('#vendedor').val(),'_blank')" >
                    <x-general.button class="bg-green-600 text-white hover:bg-green-700 active:bg-green-800 rounded-md mr-2" >
                        <i class="fas fa-file-excel" aria-hidden="true"></i> {{ __(' EXCEL') }}
                    </x-general.button>
                </a>
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
                                    $sunat = DB::connection('pgsql2')->select("select * from solicitud where tipo_documento='".substr($item->number,0,1)."' and correlativo='".substr($item->number,5,8)."' and serie='".substr($item->number,1,3)."' and estado_solicitud!='X'");
                                    if(count($sunat)>0){
                                        $enviado = "SI";
                                    }else{
                                        $enviado = "NO";
                                    }
                                @endphp
                                <tr>
                                    <td class="px-6 py-4 w-4 text-center text-sm text-gray-500">
                                        {{ $init }}
                                    </td>
                                    <td class="px-6 py-4 text-center uppercase text-sm text-gray-500">
                                        {{ date_format(new DateTime($item->date_time), 'd/m/Y') }}
                                    </td>
                                    <td class="px-6 py-4 text-center uppercase text-sm text-gray-500">
                                        {{ $item->document_type->name }}
                                    </td>
                                    <td class="px-6 py-4 text-center uppercase text-sm text-gray-500">
                                        {{ $item->number }}
                                    </td>
                                    <td class="px-6 py-4 text-center uppercase text-sm text-gray-500">
                                        {{ $item->supplier_document }}
                                    </td>
                                    <td class="px-6 py-4 text-center uppercase text-sm text-gray-500">
                                        {{ $item->supplier_name }}
                                    </td>
                                    <td class="px-6 py-4 text-center uppercase text-sm text-gray-500">
                                        {{ number_format( $item->total,2) }}
                                    </td>
                                    <td class="px-6 py-4 text-center uppercase text-sm text-gray-500">
                                        {{ $item->currency->name }}
                                    </td>
                                    <td class="px-6 py-4 text-center uppercase text-sm text-gray-500">
                                        {{ $item->person_name }}
                                    </td>
                                    <td class="px-6 py-4 text-center uppercase text-sm text-gray-500">
                                        {{ number_format( $item->pending_amount,2) }}
                                    </td>
                                    <!--td class="px-6 py-4 text-center uppercase text-sm text-gray-500">
                                        {{ ($item->schedule_time) ? date_format(new DateTime($item->schedule_time), 'd/m/Y')  : "-" }}
                                    </td-->
                                    <td class="px-6 py-4 text-center uppercase text-sm text-gray-500">
                                        @if ($item->dispatched == "Y")
                                        <span
                                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            COMPLETO
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
                                        @if ($item->status == "C")
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
                                    <td class="px-6 py-4 text-center uppercase text-sm text-gray-500">
                                        @if ($enviado == "SI")
                                        <span
                                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            ENVIADO
                                        </span>
                                        @elseif ($enviado == "NO")
                                        <span
                                            class="w-24 text-center pl-3 pr-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                            NO ENVIADO
                                        </span>
                                        @else
                                            -
                                        @endif
                                    </td>
                                    @if (in_array('show_sale', $arr_actions))
                                        <td class="px-3 py-2 w-4 text-center text-base font-medium">
                                            <a title="Ver" href="{{ route('show-sale', ['sale' => $item->id]) }}"
                                                class="text-blue-400 hover:text-blue-700"><i class="fas fa-eye"></i></a>
                                        </td>
                                    @endif

                                    {{-- @if (in_array('edit_sale', $arr_actions))
                                        @if ($item->status == "P")
                                        <td class="px-3 py-2 w-4 text-center text-base font-medium">
                                            <a title="Editar" href="{{ route('edit-sale', ['sale' => $item->id]) }}"
                                                class="text-indigo-600 hover:text-indigo-900"><i class="fas fa-pen"></i></a>
                                        </td>
                                        @else
                                        <td class="px-3 py-2 w-4 text-center text-base font-medium">

                                        </td>
                                        @endif
                                    @endif --}}

                                    @if (in_array('pdf_venta', $arr_actions))
                                        <td class="px-3 py-2 w-4 text-center text-base font-medium">
                                            <a title="Ver" href="javascript:void(0)" onclick="window.open('/clifacturacion/controlador/contComprobante.php?funcion=generarPDF&idventa={!!$item->id !!}','_blank')"
                                                class="text-red-500 hover:text-red-800"><i class="fas fa-file-pdf"></i></a>
                                        </td>
                                        @if(Auth::user()->user_type_id==1 && $item->status!="A" && $enviado=="NO"))
                                        <td>
                                            <a title="Reenviar Facturador"  href="javascript:void(0)" onclick="declarar2('{!! $item->document_type_id==12?"Boleta":"Factura" !!}',{!! $item->id !!})"><i class="fas fa-share"></i></a>
                                        </td>
                                        @else
                                        <td class="px-3 py-2 w-4 text-center text-base font-medium">
                                            -
                                            <a style="display:none" title="Reenviar Facturador"  href="javascript:void(0)" onclick="declarar2('{!! $item->document_type_id==12?"Boleta":"Factura" !!}',{!! $item->id !!})"><i class="fas fa-share"></i></a>
                                        </td>
                                        @endif
                                    @endif

                                    @if (in_array('guide_sale', $arr_actions) && $item->status!="A")
                                        <td class="px-3 py-2 w-4 text-center text-base font-medium">
                                            <a title="Guia" href="{{ route('create-sale-guide', ['sale_id' => $item->id]) }}"
                                                class="text-indigo-600 hover:text-indigo-900"><i class="fas fa-file"></i></a>
                                        </td>
                                    @else
                                    <td class="px-3 py-2 w-4 text-center text-base font-medium">

                                    </td>
                                    @endif

                                    @if (in_array('payment_sale', $arr_actions))
                                        <td class="px-3 py-2 w-4 text-center text-base font-medium">
                                            <a title="Pagos" href="javascript:void(0)" wire:click="$emit('toggle-modal-payment', '{{ $item->id }}')"
                                                class="text-green-600 hover:text-green-800"><i class="fas fa-hand-holding-usd"></i></a>
                                        </td>

                                    @endif

                                    @if (in_array('delete_sale', $arr_actions))
                                        @if ($item->status <> 'A')
                                            <td class="px-3 py-2 w-4 text-center text-base font-medium">
                                                <a title="Anular" href="javascript:void(0)" wire:click="$emit('toggle-modal', '{{ $item->id }}')"
                                                    class="text-red-600 hover:text-red-800"><i class="fas fa-trash"></i></a>
                                            </td>
                                        @else
                                            <td></td>
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
@push("js")
<script>
function declarar2(tipodocumento,idventa){
    if(confirm("Desea reenviar al facturador el comprobante?")){
        var funcion="enviar"+tipodocumento;
        $.ajax({
            type: "GET",
            url: "/clifacturacion/controlador/contComprobante.php?funcion="+funcion,
            data: "idventa="+idventa,
            success: function(a) {
                console.log(a);
                alert("Enviado correctamente");
            }
        }); 
    }
}
function anularPago(sale_id,documentpayment_id,payment_id,metodo){
    if(confirm("Desea anular el pago " + metodo + " ?")){
        $.ajax({
            type: "POST",
            url: "sale/anular-pago",
            data: "sale_id="+sale_id+"&documentpayment_id="+documentpayment_id+"&payment_id="+payment_id+"&_token={{ csrf_token() }}",
            success: function(a) {
                console.log(a);
                alert("Pago anulado correctamente");
                window.livewire.emit('refresh-sale');
            }
        }); 
    }
}
@if (Session::has('id_venta'))
    declarar2('{{ Session::get("tipodocumento") }}',{{ Session::get("id_venta") }});
@endif
</script>
@endpush