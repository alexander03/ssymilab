<?php
use App\Product;
use Illuminate\Support\Facades\DB;
$product_id=0;
?>
@if (count($lista)>0)
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>REPORTE STOCK PRODUCTO</title>
    <style>
        *{
            font-family: Verdana, Geneva, Tahoma, sans-serif;
            font-size: 0.85rem;
        }
        body{
            margin: 10px;
        }
        td {
            font-size: 11px;
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
        }
    </style>
</head>
<body>
    <img src="../public/assets/images/logo.jpeg" width="120">
    <table border="1">
        <thead>
        <tr>
            <th colspan="7" style="text-align: center"><b>REPORTE STOCK  DE PRODUCTOS</b></th>
        </tr>
        <tr>
            <th style="text-align: center"><b>CODIGO</b></th>
            <th style="text-align: center"><b>PRODUCTO</b></th>
            <th style="text-align: center"><b>MODELO</b></th>
            <th style="text-align: center"><b>MARCA</b></th>
            <th style="text-align: center"><b>CANTIDAD</b></th>
            <th style="text-align: center"><b>SEPARADO</b></th>
            <th style="text-align: center"><b>DISPONIBLE</b></th>
        </tr>
        </thead>
        <tbody>
    @foreach ($lista as $value)
        <tr>
            <td>{{ $value->code }}</td>
            <td>{{ $value->product_name.' '.$value->presentation }}</td>
            <td>{{ $value->model }}</td>
            <td>{{ ($value->product->brand) ? $value->product->brand->name : "[ELIMINADO]" }}</td>
            <td style="text-align: center">{{ sprintf('%g', $value->real) }}</td>
            <td style="text-align: center">{{ sprintf('%g', $value->separated) }}</td>
            <td style="text-align: center">{{ sprintf('%g', $value->available) }}</td>
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

