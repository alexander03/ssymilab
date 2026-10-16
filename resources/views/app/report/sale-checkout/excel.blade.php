<?php 
header("Content-Type: application/vnd.ms-excel");

header("Content-Disposition: attachment; filename=reportecaja.xls");
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
    <img src="../public/assets/images/logo.jpeg" width="120">
    <div>
        <p style=" line-height: 1rem;font-weight: 700;text-align: center;margin-top: -2rem;">
            REPORTE DE CAJA
        </p>
    </div>

    <table border="1"  style=" width: 100%; border-collapse: collapse; margin-top: 10px;margin-bottom: 10px; padding:5px" >
        <thead>
            <tr>
                <th> N� </th>
                <th> Fecha Emision</th>
                <th> Tipo </th>
                <th> Numero </th>
                <th> Concepto</th>
                <th> Persona </th>
                <th> Moneda </th>
                <th> Efectivo </th>
                <th> Tarjeta </th>
                <th> Deposito </th>
                <th> Tipo Doc </th>
                <th> Nro Doc </th>
                <th> Fecha Doc </th>
                <th> Cometario </th>
            </tr>
        </thead>
        <tbody>
        @php
            $init = $lista->firstItem() - 1;
        @endphp
        @foreach ($lista as $item)
            @php
                $init = $init + 1;
                $detail = json_decode($item->detail,true);
            @endphp
            <tr>
                <td class="px-6 py-4 w-4 text-center text-sm text-gray-500">
                    {{ $init }}
                </td>
                <td class="px-6 py-4 text-center uppercase text-sm text-gray-500">
                    {{ date_format(new DateTime($item->date_time), 'd/m/Y') }}
                </td>
                <td>
                    {{ $item->document_type_id == 10 ? "INGRESO" : "EGRESO" }}
                </td>
                <td class="px-6 py-4 text-center uppercase text-sm text-gray-500">
                    {{ $item->number }}
                </td>
                <td class="px-6 py-4 text-center uppercase text-sm text-gray-500">
                    {{ $item->reason->name }}
                </td>
                <td class="px-6 py-4 text-center uppercase text-sm text-gray-500">
                    {{ !$item->client_id ? "-" : $item->client_name }}
                </td>
                <td class="px-6 py-4 text-center uppercase text-sm text-gray-500">
                    {{ in_array($item->reason->name, ['APERTURA', 'CIERRE']) ? "-" : $item->currency->name }}
                </td>
                <td class="px-6 py-4 text-center uppercase text-sm font-bold {{ $item->document_type_id == 10 ? "text-green-600" : "text-red-600" }}">
                    {{ in_array($item->reason->name, ['APERTURA', 'CIERRE']) ? "-" : $item->currency->symbol . " " . number_format($item->cash_amount, 2) . ( $item->currency->base != "Y" ? " - " . "S/ " . number_format($item->cash_amount * $item->exchange_rate, 2) : "" ) }}
                </td>
                <td class="px-6 py-4 text-center uppercase text-sm font-bold {{ $item->document_type_id == 10 ? "text-green-600" : "text-red-600" }}">
                    {{ in_array($item->reason->name, ['APERTURA', 'CIERRE']) ? "-" : $item->currency->symbol . " " . number_format($item->card_amount, 2) . ( $item->currency->base != "Y" ? " - " . "S/" . " " . number_format($item->card_amount * $item->exchange_rate, 2) : "" ) }}
                </td>
                <td class="px-6 py-4 text-center uppercase text-sm font-bold {{ $item->document_type_id == 10 ? "text-green-600" : "text-red-600" }}">
                    {{ in_array($item->reason->name, ['APERTURA', 'CIERRE']) ? "-" : $item->currency->symbol . " " . number_format($item->transfer_amount, 2) . ( $item->currency->base != "Y" ? " - " . "S/" . " " . number_format($item->transfer_amount * $item->exchange_rate, 2) : "" ) }}
                </td>
                <td class="px-6 py-4 text-center uppercase text-sm text-gray-500">
                    {{ !isset($detail["tipo_documento"]) ? "-" : $detail["tipo_documento"] }}
                </td>
                <td class="px-6 py-4 text-center uppercase text-sm text-gray-500">
                    {{ !isset($detail["numero_documento"]) ? "-" : $detail["numero_documento"] }}
                </td>
                <td class="px-6 py-4 text-center uppercase text-sm text-gray-500">
                    {{ !isset($detail["fecha_documento"]) ? "-" : $detail["fecha_documento"] }}
                </td>
                <td class="px-6 py-4 text-center uppercase text-sm ont-bold text-gray-500">
                    {{ $item->comment() != "" ? $item->comment() : "-" }}
                </td>

            </tr>
        @endforeach
        </tbody>
    </table>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-3 mt-5">
        @foreach ($resume as $item)
        <div class="min-w-full p-3">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-200">
                    <tr>
                        <th colspan="2" class="px-6 py-2 text-center text-xs font-bold text-gray-600 uppercase tracking-wider">RESUMEN ({{ $item["currency_name"] }})</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <tr>
                        <td class="px-6 py-2 text-left uppercase text-sm text-gray-500 font-bold">Total Apertura</td>
                        <td class="px-6 py-2 text-right uppercase text-sm text-gray-500 font-bold"> {{ $item["currency_symbol"] . " " . number_format($item["total_apertura"], 2) }} </td>
                    </tr>
                    <tr>
                        <td class="pr-6 pl-8 py-2 text-left uppercase text-xs text-gray-500">Efectivo </td>
                        <td class="px-6 py-2 text-right uppercase text-xs text-gray-500"> {{ $item["currency_symbol"] . " " . number_format($item["total_apertura_efectivo"], 2) }} </td>
                    </tr>
                    <tr>
                        <td class="pr-6 pl-8 py-2 text-left uppercase text-xs text-gray-500">Tarjeta </td>
                        <td class="px-6 py-2 text-right uppercase text-xs text-gray-500"> {{ $item["currency_symbol"] . " " . number_format($item["total_apertura_tarjeta"], 2) }} </td>
                    </tr>
                    <tr>
                        <td class="pr-6 pl-8 py-2 text-left uppercase text-xs text-gray-500">Dep�sito </td>
                        <td class="px-6 py-2 text-right uppercase text-xs text-gray-500"> {{ $item["currency_symbol"] . " " . number_format($item["total_apertura_deposito"], 2) }} </td>
                    </tr>
                    <tr>
                        <td class="px-6 py-2 text-left uppercase text-sm text-gray-500 font-bold">Total Ventas</td>
                        <td class="px-6 py-2 text-right uppercase text-sm text-gray-500 font-bold"> {{ $item["currency_symbol"] . " " . number_format($item["total_ventas"], 2) }} </td>
                    </tr>
                    <tr>
                        <td class="pr-6 pl-8 py-2 text-left uppercase text-xs text-gray-500">Efectivo </td>
                        <td class="px-6 py-2 text-right uppercase text-xs text-gray-500"> {{ $item["currency_symbol"] . " " . number_format($item["total_ventas_efectivo"], 2) }} </td>
                    </tr>
                    <tr>
                        <td class="pr-6 pl-8 py-2 text-left uppercase text-xs text-gray-500">Tarjeta </td>
                        <td class="px-6 py-2 text-right uppercase text-xs text-gray-500"> {{ $item["currency_symbol"] . " " . number_format($item["total_ventas_tarjeta"], 2) }} </td>
                    </tr>
                    <tr>
                        <td class="pr-6 pl-8 py-2 text-left uppercase text-xs text-gray-500">Dep�sito </td>
                        <td class="px-6 py-2 text-right uppercase text-xs text-gray-500"> {{ $item["currency_symbol"] . " " . number_format($item["total_ventas_deposito"], 2) }} </td>
                    </tr>
                    <tr>
                        <td class="px-6 py-2 text-left uppercase text-sm text-gray-500 font-bold">Otros Ingresos</td>
                        <td class="px-6 py-2 text-right uppercase text-sm text-gray-500 font-bold"> {{ $item["currency_symbol"] . " " . number_format($item["total_varios"], 2) }} </td>
                    </tr>
                    <tr>
                        <td class="pr-6 pl-8 py-2 text-left uppercase text-xs text-gray-500">Efectivo </td>
                        <td class="px-6 py-2 text-right uppercase text-xs text-gray-500"> {{ $item["currency_symbol"] . " " . number_format($item["total_varios_efectivo"], 2) }} </td>
                    </tr>
                    <tr>
                        <td class="pr-6 pl-8 py-2 text-left uppercase text-xs text-gray-500">Tarjeta </td>
                        <td class="px-6 py-2 text-right uppercase text-xs text-gray-500"> {{ $item["currency_symbol"] . " " . number_format($item["total_varios_tarjeta"], 2) }} </td>
                    </tr>
                    <tr>
                        <td class="pr-6 pl-8 py-2 text-left uppercase text-xs text-gray-500">Dep�sito </td>
                        <td class="px-6 py-2 text-right uppercase text-xs text-gray-500"> {{ $item["currency_symbol"] . " " . number_format($item["total_varios_deposito"], 2) }} </td>
                    </tr>
                    <tr>
                        <td class="px-6 py-2 text-left uppercase text-sm text-gray-500 font-bold">Total Egresos</td>
                        <td class="px-6 py-2 text-right uppercase text-sm text-gray-500 font-bold"> - {{ $item["currency_symbol"] . " " . number_format($item["total_egresos"], 2) }} </td>
                    </tr>
                    <tr>
                        <td class="pr-6 pl-8 py-2 text-left uppercase text-xs text-gray-500">Efectivo </td>
                        <td class="px-6 py-2 text-right uppercase text-xs text-gray-500"> - {{ $item["currency_symbol"] . " " . number_format($item["total_egresos_efectivo"], 2) }} </td>
                    </tr>
                    <tr>
                        <td class="pr-6 pl-8 py-2 text-left uppercase text-xs text-gray-500">Tarjeta </td>
                        <td class="px-6 py-2 text-right uppercase text-xs text-gray-500"> - {{ $item["currency_symbol"] . " " . number_format($item["total_egresos_tarjeta"], 2) }} </td>
                    </tr>
                    <tr>
                        <td class="pr-6 pl-8 py-2 text-left uppercase text-xs text-gray-500">Dep�sito </td>
                        <td class="px-6 py-2 text-right uppercase text-xs text-gray-500"> - {{ $item["currency_symbol"] . " " . number_format($item["total_egresos_deposito"], 2) }} </td>
                    </tr>
                    <tr>
                        <td class="px-6 py-2 text-left uppercase text-sm text-gray-500 font-bold">Saldo Final</td>
                        <td class="px-6 py-2 text-right uppercase text-sm text-gray-500 font-bold"> {{ $item["currency_symbol"] . " " . number_format($item["total_saldo"], 2) }} </td>
                    </tr>
                    <tr>
                        <td class="pr-6 pl-8 py-2 text-left uppercase text-xs text-gray-500 font-bold">Efectivo </td>
                        <td class="px-6 py-2 text-right uppercase text-xs text-gray-500 font-bold"> {{ $item["currency_symbol"] . " " . number_format($item["total_saldo_efectivo"], 2) }} </td>
                    </tr>
                    <tr>
                        <td class="pr-6 pl-8 py-2 text-left uppercase text-xs text-gray-500 font-bold">Tarjeta </td>
                        <td class="px-6 py-2 text-right uppercase text-xs text-gray-500 font-bold"> {{ $item["currency_symbol"] . " " . number_format($item["total_saldo_tarjeta"], 2) }} </td>
                    </tr>
                    <tr>
                        <td class="pr-6 pl-8 py-2 text-left uppercase text-xs text-gray-500 font-bold">Dep�sito </td>
                        <td class="px-6 py-2 text-right uppercase text-xs text-gray-500 font-bold"> {{ $item["currency_symbol"] . " " . number_format($item["total_saldo_deposito"], 2) }} </td>
                    </tr>
                </tbody>
            </table>
        </div>
        @endforeach

    </div>
</body>