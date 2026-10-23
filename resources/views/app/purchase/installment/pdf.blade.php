<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>REPORTE</title>
    <style>
        *{
            font-family: Verdana, Geneva, Tahoma, sans-serif;
            font-size: 0.85rem;
        }
        body{
            margin: 20px;
        }
        td {
            font-size: 11px;
            padding: 5px;
        }

        th {
            font-size: 11px;
            padding: 5px;
        }
        table{
            border-spacing: 0px;
            border-collapse: collapse;  /* <--- add this so all the internal <td>s share adjacent borders  */
            border: 1px solid black;
        }
    </style>
</head>
<body>
    <img src="http://137.184.71.147:86/public/assets/images/logo.jpeg" width="120">
    <div>
        <p style=" line-height: 1rem;font-weight: 700;text-align: center;margin-top: -2rem;">
            REPORTE CUENTAS POR PAGAR
        </p>
    </div>

    <table border="1"  style=" width: 100%; border-collapse: collapse; margin-top: 10px;margin-bottom: 10px; padding:5px" >
        <thead>
            <tr>
                <th> Nº </th>
                <th> Compra </th>
                <th> Proveedor </th>
                <th> Cuota</th>
                <th> Monto. </th>
                <th> Monto Pendiente </th>
                <th> Fecha </th>
                <th> Fecha de pago </th>
                <th> Estado </th>
            </tr>
        </thead>
        <tbody>
            @php
                                    $init = 0;
                                @endphp
            @foreach ($lista as $key => $item)
            @php
                                    $init = $init + 1;
                                @endphp
                                <tr>
                                    <td class="px-6 py-4 w-4 text-center text-sm text-gray-500">
                                        {{ $init }}
                                    </td>
                                    <td class="px-6 py-4 text-center uppercase text-sm text-gray-500">
                                        {{ $item->document->number }}
                                    </td>
                                    <td class="px-6 py-4 text-center uppercase text-sm text-gray-500">
                                        {{ $item->supplier_name }}
                                    </td>
                                    <td class="px-6 py-4 text-center uppercase text-sm text-gray-500">
                                        {{ $item->name }}
                                    </td>
                                    <td class="px-6 py-4 text-center uppercase text-sm text-gray-500">
                                        {{ $item->total_amount }}
                                    </td>
                                    <td class="px-6 py-4 text-center uppercase text-sm text-gray-500">
                                        {{ number_format($item->total_amount - $item->cash_amount - $item->card_amount - $item->transfer_amount, 2, '.', '') }}
                                    </td>
                                    <td class="px-6 py-4 text-center uppercase text-sm text-gray-500">
                                        {{ ($item->schedule_date) ? date_format(new DateTime($item->schedule_date), 'd/m/Y') : date_format(new DateTime($item->date_time), 'd/m/Y')}}
                                    </td>
                                    <td class="px-6 py-4 text-center uppercase text-sm text-gray-500">
                                        {{ ($item->status == "P") ? "-" : date_format(new DateTime($item->date_time), 'd/m/Y')}}
                                    </td>
                                    <td class="px-6 py-4 text-center uppercase text-sm text-gray-500">
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
                                    @if ($item->status == "Y")
                                    <tr>
                                        <td colspan="2" class="px-3 py-2 text-center text-xs text-gray-500 font-bold">
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
                                        <td colspan="5" class="px-3 py-2 text-left text-xs text-gray-500 divide-y divide-gray-200">
                                            @php
                                                $payments_per_item = $item->payments;
                                            @endphp
                                            @foreach ($payments_per_item as $payment)
                                                <div><span class="font-bold">{{ $payment->method_name }}:</span> {{ number_format($payment->amount, 2) }}@if (trim($payment->operation_number) != "") | <span class="font-bold">VOUCHER:</span> {{ $payment->operation_number }}@endif</div>
                                            @endforeach
                                        </td>
                                    </tr>
                                    @endif
                @endforeach
            </tr>
        </tbody>
    </table>

</body>
</html>
