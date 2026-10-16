<?php 
header("Content-Type: application/vnd.ms-excel");

header("Content-Disposition: attachment; filename=reporteventas.xls");
?>
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
            REPORTE DE VENTAS
        </p>
    </div>

    <table border="1"  style=" width: 100%; border-collapse: collapse; margin-top: 10px;margin-bottom: 10px; padding:5px" >
        <thead>
            <tr>
                <th> Nº </th>
                <th> Fecha Emision</th>
                <th> Tipo Doc </th>
                <th> Numero </th>
                <th> Documento</th>
                <th> Cliente </th>
                <th> Total </th>
                <th> Moneda </th>
                <th> Vendedor </th>
                <th> Pendiente </th>
                <th> Entrega </th>
                <th> Estado </th>
            </tr>
        </thead>
        <tbody>
        @php
            $init = $lista->firstItem() - 1;
        @endphp
        @foreach ($lista as $item)
            @php
                $init = $init + 1;
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

            </tr>
        @endforeach
        </tbody>
    </table>
</body>