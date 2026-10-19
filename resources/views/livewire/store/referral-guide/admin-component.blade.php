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
                    <label for="reason_id" class="font-semibold text-gray-600">Motivo: </label>
                    <x-general.select id="reason_id" name="reason_id" wire:model.defer="reason_id" wire:change="$refresh">
                        <option value="">TODOS</option>
                        @foreach ($reasons as $item)
                            <option value="{{ $item->id }}">{{ $item->name }}</option>
                        @endforeach
                    </x-general.select>
                </div>
                <div class="mb-3 space-y-1 w-full text-xs col-span-1 sm:col-span-4 md:col-span-4 lg:col-span-3 xl:col-span-2">
                    <label for="addressee" class="font-semibold text-gray-600">Destinatario: </label>
                    <x-general.input type="text" name="addressee" placeholder="Destinatario" id="addressee"
                        wire:model.defer="addressee" wire:keydown.enter="$refresh"/>
                </div>
                <div class="mb-3 space-y-1 w-full text-xs col-span-1 sm:col-span-4 md:col-span-4 lg:col-span-3 xl:col-span-2">
                    <label for="number" class="font-semibold text-gray-600">Número: </label>
                    <x-general.input type="text" name="number" placeholder="Número" id="number"
                        wire:model.defer="number" wire:keydown.enter="$refresh"/>
                </div>
                <div class="mb-3 space-y-1 w-full text-xs col-span-1 sm:col-span-4 md:col-span-4 lg:col-span-3 xl:col-span-2">
                    <label for="dispatched" class="font-semibold text-gray-600">Entrega: </label>
                    <x-general.select id="dispatched" name="dispatched" wire:model.defer="dispatched" wire:change="$refresh">
                        <option value="">TODOS</option>
                        <option value="Y">COMPLETO</option>
                        <option value="N">PENDIENTE</option>
                        <option value="E">PARCIAL</option>
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

    @if (in_array('delete_referral_guide', $arr_actions))
        @livewire('store.referral-guide.delete-component')
    @endif

    @if (in_array('dispatched_referral_guide', $arr_actions))
        @livewire('store.referral-guide.dispatched-component')
    @endif

    <x-general.card-table>
        @if (in_array('create_referral_guide', $arr_actions))
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
                                    $sunat = DB::connection('pgsql2')->select("select * from solicitud where tipo_documento='G' and correlativo='".substr($item->number,5,8)."' and serie='".substr($item->number,1,3)."' and estado_solicitud!='X'");
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
                                        {{ $item->reason->name }}
                                    </td>
                                    <td class="px-6 py-4 text-center uppercase text-sm text-gray-500">
                                        {{ $item->number }}
                                    </td>
                                    <td class="px-6 py-4 text-center uppercase text-sm text-gray-500">
                                        {{ date_format(new DateTime($item->date_time), 'd/m/Y') }}
                                    </td>
                                    <td class="px-6 py-4 text-center uppercase text-sm text-gray-500">
                                        {{ $item->user->person->getNameComplete() }}
                                    </td>
                                    <td class="px-6 py-4 text-center uppercase text-sm text-gray-500">
                                        {{ $item->client->getNameComplete() }}
                                    </td>
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
                                    @if (in_array('show_referral_guide', $arr_actions))
                                        <td class="px-3 py-2 w-4 text-center text-base font-medium">
                                            <a title="Ver" href="{{ route('show-referral-guide', ['referral_guide' => $item->id]) }}"
                                                class="text-blue-400 hover:text-blue-700"><i class="fas fa-eye"></i></a>
                                        </td>
                                    @endif


                                    {{-- @if (in_array('edit_referral_guide', $arr_actions))
                                        @if ($item->status == "P")
                                        <td class="px-3 py-2 w-4 text-center text-base font-medium">
                                            <a title="Editar" href="{{ route('edit-referral-guide', ['referral_guide' => $item->id]) }}"
                                                class="text-indigo-600 hover:text-indigo-900"><i class="fas fa-pen"></i></a>
                                        </td>
                                        @else
                                        <td class="px-3 py-2 w-4 text-center text-base font-medium">

                                        </td>
                                        @endif
                                    @endif --}}

                                    @if (in_array('dispatched_referral_guide', $arr_actions))
                                        <td class="px-3 py-2 w-4 text-center text-base font-medium">
                                            <a title="Confirmar Salida" href="javascript:void(0)" wire:click="$emit('toggle-modal-dispatched', '{{ $item->id }}')"
                                                class="text-green-600 hover:text-green-800"><i class="fas fa-box-open"></i></a>
                                        </td>
                                    @endif

                                        <td class="px-3 py-2 w-4 text-center text-base font-medium">
                                            <a title="Ver" href="javascript:void(0)" onclick="window.open('/clifacturacion/controlador/contComprobante.php?funcion=generarPDF&idventa={!!$item->id !!}','_blank')"
                                                class="text-red-500 hover:text-red-800"><i class="fas fa-file-pdf"></i></a>
                                        </td>
                                        @if(Auth::user()->user_type_id==1 && $item->status<>"A" && $enviado=="NO")
                                        <td >
                                            <a title="Reenviar Facturador"  href="javascript:void(0)" onclick="declararGuia3('{!! "GuiaRemision" !!}',{!! $item->id !!})"><i class="fas fa-share"></i></a>
                                        </td>
                                        @else
                                        <td> - </td>
                                        @endif                                        

                                    @if (in_array('delete_referral_guide', $arr_actions))
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
@push("js")
<script>
function declararGuia3(tipodocumento,idventa){
    var funcion="enviar"+tipodocumento;
    $.ajax({
        type: "GET",
        url: "/clifacturacion/controlador/contComprobante.php?funcion="+funcion,
        data: "idventa="+idventa,
        success: function(a) {
            console.log(a);
        }
    }); 
}
@if (Session::has('id_guia'))
    declararGuia3('GuiaRemision',{{ Session::get("id_guia") }});
@endif
</script>
@endpush