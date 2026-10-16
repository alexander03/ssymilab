<x-layouts.master>
    <x-slot name="content">
        <x-general.breadcrumb-header>
            <x-slot name="title_admin"><span class="uppercase">{{ $title_show }}</span></x-slot>
            <x-slot name="links">
                <li class="breadcrumb-item"><a href="javascript: void(0);">{{ $category_menu }}</a></li>
                <li class="breadcrumb-item active">{{ $option_menu }}</li>
            </x-slot>
        </x-general.breadcrumb-header>
        <div class="max-w-8xl mx-auto py-2 sm:px-2 lg:px-4">
            <div class="bg-white rounded-md py-8 px-8 shadow-md">
                <div class="p-2">
                    <div class="grid grid-cols-12 mb-4 gap-2">
                        <div class="col-span-12 order-last md:order-first md:col-span-9 flex items-center uppercase p-3">
                            <div class="w-full text-center">
                                <p class="font-bold text-lg"> {{ $purchase->branch_office->company->business_name }} </p>
                                <p class="font-bold"> {{ $purchase->branch_office->name }} </p>
                                <p> {{ $purchase->branch_office->address . " - " . $purchase->branch_office->department->name. " - " . $purchase->branch_office->province->name. " - " . $purchase->branch_office->district->name }} </p>

                            </div>
                        </div>
                        <div class="col-span-12 md:col-span-3">
                            <div class="border-1 border-gray-500 rounded-md text-center p-3">
                                <p class="text-base"> {{ date_format(new DateTime($purchase->date_time), 'd/m/Y') }} </p>
                                <p class="text-lg font-bold"> {{ $purchase->document_type->name }}</p>
                                {{-- <p class="text-base font-bold">RUC: {{ $purchase->branch_office->company->ruc }}</p> --}}
                                <p class="text-base font-bold"> {{ $purchase->number }} </p>
                            </div>
                        </div>
                    </div>
                    <div class="p-2">
                        <p class="my-1 uppercase"> <span class="text-sm font-bold mr-4">FECHA: </span><span>{{ date_format(new DateTime($purchase->date_time), 'd/m/Y') }}</span>  </p>
                        <p class="my-1 uppercase"> <span class="text-sm font-bold mr-4">DNI/RUC: </span><span>{{ $purchase->client->getDocumentNumber() }}</span>  </p>
                        <p class="my-1 uppercase"> <span class="text-sm font-bold mr-4">PROVEEDOR: </span><span>{{ $purchase->client->getNameComplete() }}</span>  </p>
                        <p class="my-1 uppercase"> <span class="text-sm font-bold mr-4">USUARIO: </span><span>{{ $purchase->user->person->getNameComplete() }}</span>  </p>
                        <p class="my-1 mb-4 uppercase"> <span class="text-sm font-bold mr-4">COMENTARIO: </span><span>{{ $purchase->comment() }}</span>  </p>
                    </div>
                    <div class="my-2 mb-5 rounded shadow overflow-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-200">
                                <tr>
                                    <th colspan="1" class="sticky top-0 py-2 w-6/12 text-center text-xs font-bold bg-blue-900 text-white uppercase tracking-wider">Descripción</th>
                                    <th colspan="1" class="sticky top-0 py-2 w-3/12 text-center text-xs font-bold bg-blue-900 text-white uppercase tracking-wider">Und.</th>
                                    <th colspan="1" class="sticky top-0 py-2 w-1/12 text-center text-xs font-bold bg-blue-900 text-white uppercase tracking-wider">Cant.</th>
                                    <th colspan="1" class="sticky top-0 py-2 w-1/12 text-center text-xs font-bold bg-blue-900 text-white uppercase tracking-wider">P. Unit</th>
                                    <th colspan="1" class="sticky top-0 py-2 w-1/12 text-center text-xs font-bold bg-blue-900 text-white uppercase tracking-wider">Total</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach ($purchase->details as $key => $item)
                                    <tr>
                                        <td class="px-4 py-2 text-left text-sm text-gray-500"> {{ $item->product->name.' '.$item->product->model.' '.$item->product->presentation.' '.$item->product->brand->name }} </td>
                                        <td class="px-4 py-2 text-center text-sm text-gray-500"> {{ $item->unit->name }} </td>
                                        <td class="px-4 py-2 text-center text-sm text-gray-500"> {{ $item->quantity }} </td>
                                        <td class="px-4 py-2 text-right text-sm text-gray-500"> {{ $item->currency->symbol . " " . number_format($item->purchase_price, 2) }} </td>
                                        <td class="px-4 py-2 text-right text-sm text-gray-500"> {{ $item->currency->symbol . " " . number_format($item->quantity * $item->purchase_price, 2) }} </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="4" class="px-4 py-2 text-right text-sm text-gray-500">TOTAL</th>
                                    <th class="px-4 py-2 text-right text-sm text-gray-500">{{ $item->currency->symbol . " " . number_format($purchase->total, 2) }}</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    <div class="grid grid-cols-1 lg:grid-cols-2">
                        <div class="my-2 rounded shadow overflow-auto col-span-1">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-200">
                                    <tr>
                                        <th colspan="1" class="sticky top-0 py-2 w-6/12 text-center text-xs font-bold bg-blue-900 text-white uppercase tracking-wider">DOCUMENTOS ANEXOS</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @if (count($documents) > 0)
                                        @foreach ($documents as $key => $attached)
                                            @if ($key == "requirement")
                                                <tr>
                                                    <td class="px-4 py-2 text-center text-sm text-gray-500"> REQUERIMIENTO: {{ $attached->number }} </td>
                                                </tr>
                                            @else
                                                @foreach ($attached as $entry_guide)
                                                    <tr>
                                                        <td class="px-4 py-2 text-center text-sm text-gray-500"> GUIA DE ENTRADA: {{ $entry_guide->number }} </td>
                                                    </tr>
                                                @endforeach
                                            @endif
                                        @endforeach
                                    @else
                                        <tr>
                                            <td class="px-4 py-2 text-center text-sm text-gray-500">No se encontraron documentos relacionados</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                        @php
                            $medias = $purchase->getMedia('images');
                        @endphp
                        @if (count($medias) > 0)
                        @php
                            $media = $medias[0];
                            //dd($media);
                        @endphp
                        <div class="my-2 rounded shadow overflow-auto col-span-1">
                            <div class="margin-10"  style="text-align: center; margin-top:40px !important ; margin-bottom:50px">
                                <img src="../storage/{{$media->id}}/{{$media->file_name}}" width="350px" style="margin-top:60px" alt="">
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
                <div class="mt-4 flex justify-start">
                    <a href="{{ route('purchase') }}">
                        <x-general.button x-on:click="show=false"  class="bg-gray-200 text-black hover:bg-gray-400 active:bg-gray-500 mx-2">
                            <i class="fas fa-arrow-left mr-2" aria-hidden="true"></i> {{ __(' Atrás') }}
                        </x-general.button>
                    </a>
                </div>
            </div>
        </div>
    </x-slot>
</x-layouts.master>
