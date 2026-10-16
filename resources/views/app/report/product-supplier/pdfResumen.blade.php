<?php
use App\Product;
use Illuminate\Support\Facades\DB;
$total=0;
$pendiente=0;
$lista = array();
?>
@if (count($list)>0)
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>REPORTE COMPRAS POR PRODUCTO CONSOLIDADO</title>
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
                <th colspan="5" style="text-align: center"><b>REPORTE COMPRAS POR PRODUCTO CONSOLIDADO DEL {{ date('d/m/Y',strtotime($fechainicio)) }} AL {{ date('d/m/Y',strtotime($fechafin)) }}</b></th>
            </tr>
            <tr>
                <th style="width: 17%;text-align: center"><b>PROVEEDOR</b></th>
                <th style="width: 8%;text-align: center"><b>MONEDA</b></th>
                <th style="width: 8%;text-align: center"><b>CANT.</b></th>
                <th style="width: 30%;text-align: center"><b>PRODUCTO</b></th>
                <th style="width: 10%;text-align: center"><b>TOTAL</b></th>
            </tr>
            </thead>
            <tbody>
                @foreach ($list as $item)
                @php
                $total = $total + $item->quantity*$item->purchase_price;
                if(!isset($lista[$item->client_id.'-'.$item->product_id])){
                    $lista[$item->client_id.'-'.$item->product_id] = array("supplier_name" => $item->supplier_name, "currency" => $item->currency->name, "product" => $item->product_name.' '.$item->presentation.' '.$item->model, "total" => $item->quantity*$item->purchase_price, "cantidad" => $item->quantity);
                }else{
                    $lista[$item->client_id.'-'.$item->product_id]["total"] = $lista[$item->client_id.'-'.$item->product_id]["total"] + $item->quantity*$item->purchase_price;
                    $lista[$item->client_id.'-'.$item->product_id]["cantidad"] = $lista[$item->client_id.'-'.$item->product_id]["cantidad"] + $item->quantity;
                }
                @endphp
                @endforeach
                @foreach($lista as $item)
                <tr>
                    <td>
                        {{ $item["supplier_name"] }}
                    </td>
                    <td>
                        {{ $item["currency"] }}
                    </td>
                    <td style="text-align: center;">
                        {{ number_format( $item["cantidad"],0) }}
                    </td>
                    <td>
                        {{ $item["product"] }}
                    </td>
                    <td style="text-align: center;">
                        {{ number_format( $item["total"],2) }}
                    </td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="4" style="text-align: center;"><b>TOTAL</b></th>
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

