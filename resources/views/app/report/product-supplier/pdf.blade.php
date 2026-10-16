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
    <title>REPORTE COMPRAS POR PRODUCTO</title>
    <style>
        *{
            font-family: Verdana, Geneva, Tahoma, sans-serif;
            font-size: 0.85rem;
        }
        body{
            margin: 20px;
        }
        td {
            font-size: 10px;
            padding: 3px;
        }

        th {
            font-size: 11px;
            padding: 3px;
        }
        table{
            border-spacing: 0px;
            border-collapse: collapse;  /* <--- add this so all the internal <td>s share adjacent borders  */
            border: 1px solid black;
            width: 100%;
        }
    </style>
</head>
<body>
    <img src="../public/assets/images/logo.jpeg" width="120">
        <table border="1" style="table-layout:fixed;">
            <thead>
            <tr>
                <th colspan="11" style="text-align: center"><b>REPORTE COMPRAS POR PRODUCTO DEL {{ date('d/m/Y',strtotime($fechainicio)) }} AL {{ date('d/m/Y',strtotime($fechafin)) }}</b></th>
            </tr>
            <tr>
                <th style="width: 10%;text-align: center"><b>SUCURSAL</b></th>
                <th style="width: 10%;text-align: center"><b>TIPO DOC</b></th>
                <th style="width: 9%;text-align: center"><b>NUMERO</b></th>
                <th style="width: 9%;text-align: center"><b>FECHA</b></th>
                <th style="width: 10%;text-align: center"><b>DOCUMENTO</b></th>
                <th style="width: 17%;text-align: center"><b>PROVEEDOR</b></th>
                <th style="width: 10%;text-align: center"><b>MONEDA</b></th>
                <th style="width: 8%;text-align: center"><b>CANT.</b></th>
                <th style="width: 30%;text-align: center"><b>PRODUCTO</b></th>
                <th style="width: 10%;text-align: center"><b>P. COMPRA</b></th>
                <th style="width: 10%;text-align: center"><b>TOTAL</b></th>
            </tr>
            </thead>
            <tbody>
                @foreach ($list as $item)
                @php
                $total = $total + $item->quantity*$item->purchase_price;
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
                    <td style="text-align: center;">
                        {{ number_format( $item->quantity,0) }}
                    </td>
                    <td>
                        {{ $item->product_name.' '.$item->presentation.' '.$item->model }}
                    </td>
                    <td style="text-align: center;">
                        {{ number_format( $item->purchase_price,2) }}
                    </td>
                    <td style="text-align: center;">
                        {{ number_format( $item->quantity*$item->purchase_price,2) }}
                    </td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="10" style="text-align: center;"><b>TOTAL</b></th>
                    <th style="text-align: center;">{{ number_format($total,2) }}</th>
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

