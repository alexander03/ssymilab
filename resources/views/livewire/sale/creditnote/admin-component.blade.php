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
            </div>
        </x-slot>
    </x-general.card-form>

    @if (in_array('delete_creditnote', $arr_actions))
        @livewire('sale.creditnote.delete-component')
    @endif
    @if (in_array('dispatched_creditnote', $arr_actions))
        @livewire('sale.creditnote.dispatched-component')
    @endif

    <x-general.card-table>
        @if (in_array('create_creditnote', $arr_actions))
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
                                        {{ date_format(new DateTime($item->date_time), 'd/m/Y') }}
                                    </td>
                                    <td class="px-6 py-4 text-center uppercase text-sm text-gray-500">
                                        {{ $item->number_ref }}
                                    </td>
                                    <td class="px-6 py-4 text-center uppercase text-sm text-gray-500">
                                        {{ $item->supplier_document }}
                                    </td>
                                    <td class="px-6 py-4 text-center uppercase text-sm text-gray-500">
                                        {{ $item->supplier_name }}
                                    </td>
                                    <td class="px-6 py-4 text-center uppercase text-sm text-gray-500">
                                        {{ $item->person_name }}
                                    </td>
                                    <td class="px-6 py-4 text-center uppercase text-sm text-gray-500">
                                        {{ $item->currency->name }}
                                    </td>
                                    <td class="px-6 py-4 text-center uppercase text-sm text-gray-500">
                                        {{ number_format( $item->total,2) }}
                                    </td>
                                    <td class="px-6 py-4 text-center uppercase text-sm text-gray-500">
                                        @if ($item->status == "A")
                                        <span
                                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                            ANULADO
                                        </span>
                                        @else
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
                                        @endif
                                    </td>
                                    
                                    @if (in_array('show_creditnote', $arr_actions))
                                        <td class="px-3 py-2 w-4 text-center text-base font-medium">
                                            <a title="Ver" href="{{ route('show-creditnote', ['creditnote' => $item->id]) }}"
                                                class="text-blue-400 hover:text-blue-700"><i class="fas fa-eye"></i></a>
                                        </td>
                                    @endif

                                    @if (in_array('dispatched_creditnote', $arr_actions))
                                        <td class="px-3 py-2 w-4 text-center text-base font-medium">
                                            <a title="Confirmar Ingreso" href="javascript:void(0)" wire:click="$emit('toggle-modal-dispatched', '{{ $item->id }}')"
                                                class="text-green-600 hover:text-green-800"><i class="fas fa-box-open"></i></a>
                                        </td>
                                    @endif

                                    @if (in_array('pdf_creditnote', $arr_actions))
                                        <td class="px-3 py-2 w-4 text-center text-base font-medium">
                                            <a title="Ver" href="javascript:void(0)" onclick="window.open('/clifacturacion/controlador/contComprobante.php?funcion=generarPDF2&idventa={!!$item->id !!}','_blank')"
                                                class="text-red-500 hover:text-red-800"><i class="fas fa-file-pdf"></i></a>
                                        </td>
                                        @if(Auth::user()->user_type_id==1  && $item->status!="A")
                                        <td>
                                            <a title="Reenviar Facturador"  href="javascript:void(0)" onclick="declarar4('NotaCredito',{!! $item->id !!})"><i class="fas fa-share"></i></a>
                                        </td>
                                        @endif
                                    @endif

                                    @if (in_array('delete_creditnote', $arr_actions))
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
function declarar4(tipodocumento,idventa){
    var funcion="enviarNotaCredito";
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
@if (Session::has('id_nota'))
    declarar4('{{ Session::get("tipodocumento") }}',{{ Session::get("id_nota") }});
@endif
</script>
@endpush