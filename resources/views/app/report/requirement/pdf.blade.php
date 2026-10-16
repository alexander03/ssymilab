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
    <title>REPORTE REQUERIMIENTOS</title>
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
            <th colspan="7" style="text-align: center"><b>REPORTE REQUERIMIENTOS DEL {{ date('d/m/Y',strtotime($fechainicio)) }} AL {{ date('d/m/Y',strtotime($fechafin)) }}</b></th>
        </tr>
        <tr>
            <th style="width: 15px;text-align: center"><b>SUCURSAL</b></th>
            <th style="width: 15px;text-align: center"><b>NUMERO</b></th>
            <th style="width: 15px;text-align: center"><b>FECHA</b></th>
            <th style="width: 15px;text-align: center"><b>DOCUMENTO</b></th>
            <th style="width: 15px;text-align: center"><b>PROVEEDOR</b></th>
            <th style="width: 15px;text-align: center"><b>COMENTARIO</b></th>
            <th style="width: 15px;text-align: center"><b>ESTADO</b></th>
        </tr>
        </thead>
        <tbody>
            @foreach ($list as $item)
            
            <tr>
                <td>
                    {{ $item->branch_office->name }}
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
                @php
                    $comment = json_decode($item->detail)->comment;
                @endphp
                <td>
                    {{ $comment }}
                </td>
                <td>
                    @if ($item->status == "Y")
                    <span
                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                        CON FACTURA
                    </span>
                    @elseif ($item->status == "P")
                    <span
                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                        SIN FACTURA
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

