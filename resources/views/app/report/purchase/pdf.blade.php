<?php
use App\Product;
use Illuminate\Support\Facades\DB;
$total=0;
$pendiente=0;
?>
@if (count($list)>0)
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>REPORTE COMPRAS</title>
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
    <table border="1">
        <thead>
        <tr>
            <th colspan="9" style="text-align: center"><b>REPORTE COMPRAS DEL {{ date('d/m/Y',strtotime($fechainicio)) }} AL {{ date('d/m/Y',strtotime($fechafin)) }}</b></th>
        </tr>
        <tr>
            <th style="width: 15px;text-align: center"><b>SUCURSAL</b></th>
            <th style="width: 15px;text-align: center"><b>TIPO DOC</b></th>
            <th style="width: 15px;text-align: center"><b>NUMERO</b></th>
            <th style="width: 15px;text-align: center"><b>FECHA</b></th>
            <th style="width: 15px;text-align: center"><b>DOCUMENTO</b></th>
            <th style="width: 15px;text-align: center"><b>PROVEEDOR</b></th>
            <th style="width: 15px;text-align: center"><b>MONEDA</b></th>
            <th style="width: 15px;text-align: center"><b>TOTAL</b></th>
            <th style="width: 15px;text-align: center"><b>PENDIENTE</b></th>
        </tr>
        </thead>
        <tbody>
            @foreach ($list as $item)
            @php
            $total = $total + $item->total;
            $pendiente = $pendiente + $item->pending_amount;
            @endphp
            <tr>
                <td>
                    {{ $item->branch_office->name }}
                </td>
                <td>
                    {{ $item->document_type->name }}
                </td>
                <td>
                    {{ $item->number }}
                </td>
                <td>
                    {{ date_format(new DateTime($item->date_time), 'd/m/Y') }}
                </td>
                <td>
                    {{ $item->supplier_document }}
                </td>
                <td>
                    {{ $item->supplier_name }}
                </td>
                <td>
                    {{ $item->currency->name }}
                </td>
                <td>
                    {{ number_format( $item->total,2) }}
                </td>
                <td>
                    {{ number_format( $item->pending_amount,2) }}
                </td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th colspan="7" style="text-align: center;"><b>TOTAL</b></th>
                <th style="text-align: center;">{{ number_format($total,2) }}</th>
                <th style="text-align: center;">{{ number_format($pendiente,2) }}</th>
            </tr>
        </tfoot>
    </table>
@else
    <table>
        <tr>
            <td>
                SIN RESULTADOS
            </td>
        </tr>
    </table>
@endif
</body>
</html>

