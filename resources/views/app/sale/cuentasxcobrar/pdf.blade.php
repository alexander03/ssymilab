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
            REPORTE CUENTAS POR COBRAR
        </p>
    </div>

    @php
        //CABECERA DE LA TABLA: SE REPITE AL INICIO DE CADA ZONA
        $table_open = '<table border="1" style=" width: 100%; border-collapse: collapse; margin-top: 5px;margin-bottom: 15px; padding:5px"><thead><tr><th> N&ordm; </th><th> Fecha Emision</th><th> Venta </th><th> Cliente </th><th> Cuota</th><th> Monto </th><th> Fecha de Venci </th><th> Fecha de pago </th><th> Pendiente </th><th> Estado </th></tr></thead><tbody>';
        $table_close = '</tbody></table>';
    @endphp
            @php
                $init = 0;
                $zone_group = false;
                $zone_name = "";
                $zone_amount = 0;
                $zone_pending = 0;
                $client_group = false;
                $client_name = "";
                $client_amount = 0;
                $client_pending = 0;
                $total_amount = 0;
                $total_pending = 0;
            @endphp
            @foreach ($lista as $key => $item)
            @php
                $init = $init + 1;
                if(strtotime($item->schedule_date)<=strtotime('now') && $item->status=='P'){
                    $color="color:red";
                }else{
                    $color="";
                }
                if(!is_null($item->document)){
                    $item_pending = ($item->status == "P") ? ($item->total_amount - $item->cash_amount - $item->card_amount - $item->transfer_amount) : 0;
                    $new_zone = ($zone_group === false || $zone_group !== $item->client_zone_id);
                    $new_client = ($new_zone || $client_group !== $item->client_group_id);
            @endphp
                {{-- CIERRE DEL CLIENTE ANTERIOR (Y DE LA ZONA SI CAMBIA) --}}
                @if ($new_client && $client_group !== false)
                    <tr>
                        <td colspan="5" style="text-align: right; font-weight: bold;">SUBTOTAL {{ $client_name }}</td>
                        <td style="font-weight: bold;">S/ {{ number_format($client_amount, 2, '.', '') }}</td>
                        <td colspan="2"></td>
                        <td style="font-weight: bold;">S/ {{ number_format($client_pending, 2, '.', '') }}</td>
                        <td></td>
                    </tr>
                @endif
                @if ($new_zone && $zone_group !== false)
                    <tr>
                        <th colspan="5" style="text-align: right;">SUBTOTAL ZONA {{ $zone_name }}</th>
                        <th>S/ {{ number_format($zone_amount, 2, '.', '') }}</th>
                        <th colspan="2"></th>
                        <th>S/ {{ number_format($zone_pending, 2, '.', '') }}</th>
                        <th></th>
                    </tr>
                    {!! $table_close !!}
                @endif
                @if ($new_zone)
                    @php
                        $zone_group = $item->client_zone_id;
                        $zone_name = $item->zone_name;
                        $zone_amount = 0;
                        $zone_pending = 0;
                    @endphp
                    {{-- TITULO DE LA ZONA FUERA DE LA TABLA Y CABECERA REPETIDA --}}
                    <p style="font-weight: 700; margin-top: 15px; margin-bottom: 0px;">ZONA: {{ $zone_name }}</p>
                    {!! $table_open !!}
                @endif
                @if ($new_client)
                    @php
                        $client_group = $item->client_group_id;
                        $client_name = $item->supplier_name;
                        $client_amount = 0;
                        $client_pending = 0;
                    @endphp
                    <tr>
                        <td colspan="10" style="text-align: left; font-weight: bold; background-color: #f3f4f6;">CLIENTE: {{ $item->supplier_name }} ({{ $item->supplier_document }}) | VENDEDOR: {{ $item->seller_name ? $item->seller_name : 'SIN VENDEDOR' }}</td>
                    </tr>
                @endif
                @php
                    $client_amount += $item->total_amount;
                    $client_pending += $item_pending;
                    $zone_amount += $item->total_amount;
                    $zone_pending += $item_pending;
                    $total_amount += $item->total_amount;
                    $total_pending += $item_pending;
                @endphp
                <tr style="{{$color}}">
                    <td class="px-6 py-4 w-4 text-center text-sm text-gray-500">
                        {{ $init }}
                    </td>
                    <td class="px-6 py-4 text-center uppercase text-sm text-gray-500">
                        {{ date("d/m/Y",strtotime($item->document->date_time)) }}
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
                        S/ {{ $item->total_amount }}
                    </td>
                    <td class="px-6 py-4 text-center uppercase text-sm text-gray-500">
                        {{ ($item->schedule_date) ? date_format(new DateTime($item->schedule_date), 'd/m/Y') : date_format(new DateTime($item->date_time), 'd/m/Y')}}
                    </td>
                    <td class="px-6 py-4 text-center uppercase text-sm text-gray-500">
                        {{ ($item->status == "P") ? "-" : date_format(new DateTime($item->date_time), 'd/m/Y')}}
                    </td>
                    <td class="px-6 py-4 text-center uppercase text-sm text-gray-500">
                        {{ ($item->status == "P") ? "S/ ".($item->total_amount - $item->cash_amount - $item->card_amount - $item->transfer_amount):'-' }}
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
                    <td colspan="3" class="px-3 py-2 text-left text-xs text-gray-500 divide-y divide-gray-200">
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
                            <div>{{ date('d/m/Y',strtotime($payment->date_time)) }} | <span class="font-bold">{{ $payment->method_name }}</span>  | N° OP. {{ $payment->operation_number }} | {{ number_format($payment->amount, 2) }}</div>
                        @endforeach
                    </td>
                </tr>
                @endif
                <?php
                }
                ?>
                @endforeach
            @if ($zone_group !== false)
            <tr>
                <td colspan="5" style="text-align: right; font-weight: bold;">SUBTOTAL {{ $client_name }}</td>
                <td style="font-weight: bold;">S/ {{ number_format($client_amount, 2, '.', '') }}</td>
                <td colspan="2"></td>
                <td style="font-weight: bold;">S/ {{ number_format($client_pending, 2, '.', '') }}</td>
                <td></td>
            </tr>
            <tr>
                <th colspan="5" style="text-align: right;">SUBTOTAL ZONA {{ $zone_name }}</th>
                <th>S/ {{ number_format($zone_amount, 2, '.', '') }}</th>
                <th colspan="2"></th>
                <th>S/ {{ number_format($zone_pending, 2, '.', '') }}</th>
                <th></th>
            </tr>
            {!! $table_close !!}
            <table border="1" style=" width: 100%; border-collapse: collapse; margin-top: 5px; padding:5px">
                <tr>
                    <th style="text-align: right;">TOTAL GENERAL</th>
                    <th>MONTO: S/ {{ number_format($total_amount, 2, '.', '') }}</th>
                    <th>PENDIENTE: S/ {{ number_format($total_pending, 2, '.', '') }}</th>
                </tr>
            </table>
            @else
            <p style="text-align: center;">No se encontraron registros</p>
            @endif
</body>
</html>
