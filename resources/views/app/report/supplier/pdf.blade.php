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
    <title>REPORTE PROVEEDOR</title>
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
                <th colspan="4" style="text-align: center"><b>REPORTE PROVEEDOR </b></th>
            </tr>
            <tr>
                <th style="width: 14%;text-align: center"><b>NRO. DOC.</b></th>
                <th style="width: 40%;text-align: center"><b>NOMBRES / RAZON SOCIAL</b></th>
                <th style="width: 30%;text-align: center"><b>EMAIL</b></th>
                <th style="width: 15%;text-align: center"><b>TELEFONO</b></th>
            </tr>
            </thead>
            <tbody>
                @foreach ($list as $item)
                <tr>
                    <td>
                        {{ ($item->type == '1') ? (($item->dni && $item->dni != "") ? $item->dni : "-") : $item->ruc }}
                    </td>
                    <td>
                        {{ $item->getNameComplete() }}
                    </td>
                    <td>
                        {{ ($item->email && $item->email != "") ? $item->email : "-" }}
                    </td>
                    <td>
                        {{ ($item->phone && $item->phone != "") ? $item->phone : "-" }}
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

