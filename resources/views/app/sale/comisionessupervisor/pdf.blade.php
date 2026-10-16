<?php
use App\Sale;
use Illuminate\Support\Facades\DB;
use App\Librerias\Libreria;
$vendedor_id=0;$total = 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>REPORTE COMISIONES SUPERVISOR</title>
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
    <img src="http://137.184.71.147:81/public/assets/images/unimedilab-logo.png" width="120">
    <div>
        <p style=" line-height: 1rem;font-weight: 700;text-align: center;margin-top: -2rem;">
            REPORTE COMISIONES POR SUPERVISOR - {{ Libreria::mes(date_format(new DateTime($fecha), 'n')) }} DEL {{ date_format(new DateTime($fecha), 'Y') }}
        </p>
    </div>
    @if(count($sale)>0)
        @foreach ($sale as $item)
            <?php 
            if($vendedor_id!=$item->person_id){
                if($vendedor_id!="0"){
                    echo "</tbody><tfoot><tr><th colspan='5'>TOTAL</th><th style='text-align:right'>".number_format($total,2,'.','')."</th></tr></tfoot></table><br />";
                }
                $vendedor_id = $item->person_id;
            ?>
            <table border="1"  style=" width: 100%; border-collapse: collapse; margin-top: 10px;margin-bottom: 10px; padding:5px" >
                <thead>
                    <tr>
                        <th style="text-align: left;" colspan="6">{{ $item->vendedor->getNameComplete() }}</th>
                    </tr>
                    <tr>
                        <th> Nº </th>
                        <th> Venta </th>
                        <th> Fecha Doc. </th>
                        <th> Cliente </th>
                        <th> Total </th>
                        <th> Comision </th>
                    </tr>
                </thead>
                <tbody>
            @php
                $init = 0;
                $total = 0;
                }
                $init = $init + 1;
                $total = $total + number_format($item->total*($item->percentage_comision_supervisor/100)/1.18,2,'.','');
            @endphp
                <tr>
                    <td>
                        {{ $init }}
                    </td>
                    <td>
                        {{ $item->number }}
                    </td>
                    <td>
                        {{ date("d/m/Y",strtotime($item->date_time)) }}
                    </td>
                    <td style="width: 250px">
                        {{ $item->client->getNameComplete() }}
                    </td>
                    <td style="text-align: right;">
                        {{ $item->total }}
                    </td>
                    <td  style="text-align: right;">
                        {{ number_format($item->total*($item->percentage_comision_supervisor/100)/1.18,2,'.','') }}
                    </td>
                </tr>
        @endforeach
                </tr>
            </tbody>
            <tfoot><tr><th colspan='5'>TOTAL</th><th style="text-align: right;">{{ number_format($total,2,'.','')}}</th></tr></tfoot>
        </table>
    @else
        <p style=" line-height: 1rem;font-weight: 700;text-align: center;margin-top: -2rem;">SIN INFORMACION</p>
    @endif
</body>
</html>
