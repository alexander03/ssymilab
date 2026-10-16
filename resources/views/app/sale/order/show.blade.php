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
                                <p class="font-bold text-lg"> {{ $order->branch_office->company->business_name }} </p>
                                <p class="font-bold"> {{ $order->branch_office->name }} </p>
                                <p> {{ $order->branch_office->address . " - " . $order->branch_office->department->name. " - " . $order->branch_office->province->name. " - " . $order->branch_office->district->name }} </p>

                            </div>
                        </div>
                        <div class="col-span-12 md:col-span-3">
                            <div class="border-1 border-gray-500 rounded-md text-center p-3">
                                <p class="text-base"> {{ date_format(new DateTime($order->date_time), 'd/m/Y') }} </p>
                                <p class="text-lg font-bold"> PEDIDO</p>
                                <p class="text-base font-bold">RUC: {{ $order->branch_office->company->ruc }}</p>
                                <p class="text-base font-bold"> {{ $order->number }} </p>
                            </div>
                        </div>
                    </div>
                    <div class="p-2">
                        <p class="my-1 uppercase"> <span class="text-sm font-bold mr-4">FECHA: </span><span>{{ date_format(new DateTime($order->date_time), 'd/m/Y') }}</span>  </p>
                        <p class="my-1 uppercase"> <span class="text-sm font-bold mr-4">DNI/RUC: </span><span>{{ $order->client->getDocumentNumber() }}</span>  </p>
                        <p class="my-1 uppercase"> <span class="text-sm font-bold mr-4">PROVEEDOR: </span><span>{{ $order->client->getNameComplete() }}</span>  </p>
                        <p class="my-1 uppercase"> <span class="text-sm font-bold mr-4">USUARIO: </span><span>{{ $order->user->person->getNameComplete() }}</span>  </p>
                        <p class="my-1 uppercase"> <span class="text-sm font-bold mr-4">COMENTARIO: </span><span>{{ $order->comment() }}</span>  </p>
                    </div>
                    <div class="my-2 rounded shadow overflow-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-200">
                                <tr>
                                    <th colspan="1" class="sticky top-0 py-1 w-1/12 text-center text-xs font-bold bg-blue-900 text-white uppercase tracking-wider">Cóigo</th>
                                    <th colspan="1" class="sticky top-0 py-1 w-6/12 text-center text-xs font-bold bg-blue-900 text-white uppercase tracking-wider">Descripción</th>
                                    <th colspan="1" class="sticky top-0 py-1 w-2/12 text-center text-xs font-bold bg-blue-900 text-white uppercase tracking-wider">Und.</th>
                                    <th colspan="1" class="sticky top-0 py-1 w-2/12 text-center text-xs font-bold bg-blue-900 text-white uppercase tracking-wider">Cant.</th>
                                    <th colspan="1" class="sticky top-0 py-1 w-3/12 text-center text-xs font-bold bg-blue-900 text-white uppercase tracking-wider">Precio</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach ($order->details as $key => $item)
                                    <tr>
                                        <td class="px-4 py-2 text-left text-sm text-gray-500"> {{ $item->product->code }} </td>
                                        <td class="px-4 py-2 text-left text-sm text-gray-500"> {{ $item->product->name.' '.$item->product->model }} </td>
                                        <td class="px-4 py-2 text-center text-sm text-gray-500"> {{ $item->unit->name }} </td>
                                        <td class="px-4 py-2 text-center text-sm text-gray-500"> {{ sprintf('%g', $item->quantity) }} </td>
                                        <td class="px-4 py-2 text-center text-sm text-gray-500"> {{ $order->currency->symbol . " " .  number_format($item->sell_price,2) }} </td>
                                    </tr>
                                @endforeach
                                <tr>
                                    <td colspan="4" class="px-4 py-2 font-bold text-right text-sm text-gray-500"> TOTAL </td>
                                    <td class="px-4 py-2 text-center font-bold text-sm text-gray-500"> {{ $order->currency->symbol . " " .  number_format($order->total,2) }} </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="mt-4 flex justify-start">
                    <a href="{{ route('order') }}">
                        <x-general.button x-on:click="show=false"  class="bg-gray-200 text-black hover:bg-gray-400 active:bg-gray-500 mx-2">
                            <i class="fas fa-arrow-left mr-2" aria-hidden="true"></i> {{ __(' Atrás') }}
                        </x-general.button>
                    </a>
                </div>
            </div>
        </div>
    </x-slot>
</x-layouts.master>
