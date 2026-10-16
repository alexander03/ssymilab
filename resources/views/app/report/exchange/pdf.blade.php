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
    <title>REPORTE TIPO DE CAMBIO</title>
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
    <img src="http://67.205.164.246/public/assets/images/logo.jpeg" width="120">
    <table border="1" >
        <thead>
        <tr>
            <th colspan="4" style="text-align: center"><b>REPORTE TIPO CAMBIO DEL {{ date('d/m/Y',strtotime($fechainicio)) }} AL {{ date('d/m/Y',strtotime($fechafin)) }}</b></th>
        </tr>
        <tr>
            <th style="width: 35%;text-align: center"><b>FECHA</b></th>
            <th style="width: 25%;text-align: center"><b>MONEDA BASE</b></th>
            <th style="width: 25%;text-align: center"><b>MONEDA</b></th>
            <th style="width: 25%;text-align: center"><b>VALOR</b></th>
        </tr>
        </thead>
        <tbody>
            @foreach ($list as $item)
            <tr>
                <td>
                    {{ date_format(new DateTime($item->date), 'd/m/Y') }}
                </td>
                <td>
                    {{ $item->base_currency->name }}
                </td>
                <td>
                    {{ $item->currency->name }}
                </td>
                <td>
                    {{ $item->value }}
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

