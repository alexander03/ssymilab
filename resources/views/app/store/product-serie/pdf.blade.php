<?php
use App\Product;
use Illuminate\Support\Facades\DB;
$product_id=0;
?>
@if (count($list)>0)
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>REPORTE SERIE PRODUCTO</title>
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
            <th colspan="6" style="text-align: center"><b>REPORTE SERIE  DE PRODUCTOS</b></th>
        </tr>
        <tr>
            <th style="text-align: center"><b>CODIGO</b></th>
            <th style="text-align: center"><b>PRODUCTO</b></th>
            <th style="text-align: center"><b>MARCA</b></th>
            <th style="text-align: center"><b>MODELO</b></th>
            <th style="text-align: center"><b>SERIE</b></th>
            <th style="text-align: center"><b>SITUACION</b></th>
        </tr>
        </thead>
        <tbody>
    @foreach ($list as $value)
        <tr>
            <td>{{ $value->code }}</td>
            <td>{{ $value->product_name }}</td>
            <td>{{ $value->brand_name }}</td>
            <td>{{ $value->model }}</td>
            <td>{{ $value->serie }}</td>
            <td>
                @if ($value->status == "Y")
                <span
                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                    ACTIVO
                </span>
                @elseif ($value->status == "V")
                <span
                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                    VENDIDO
                </span>
                @elseif ($value->status == "S")
                <span
                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-indigo-100 text-indigo-800">
                    FUERA
                </span>
                @elseif ($value->status == "N")
                <span
                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                    NO INGRESADO
                </span>
                @elseif ($value->status == "P")
                <span
                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-purple-100 text-purple-800">
                    SIN GUIA
                </span>
                @elseif ($value->status == "A")
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

