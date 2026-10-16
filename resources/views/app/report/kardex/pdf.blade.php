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
    <title>REPORTE KARDEX VALORIZADO</title>
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
    @foreach ($list as $value)
        <?php 
        if($product_id!=$value->product_id){
            if($product_id!="0"){
                echo "</tbody></table><br />";
            }
            $product_id = $value->product_id;
        ?>
    <table border="1">
        <thead>
        <tr>
            <th colspan="11" style="text-align: center"><b>REPORTE KARDEX VALORIZADO DEL {{ date('d/m/Y',strtotime($fechainicio)) }} AL {{ date('d/m/Y',strtotime($fechafin)) }}</b></th>
        </tr>
        <tr>
            <th style="width: 20px" colspan="11" ><b>PRODUCTO: {{ $value->product_name.' '.$value->presentation.' '.$value->model.' '.$value->brand_name }}</b></th>
        </tr>
        <tr>
            <th style="width: 15px;text-align: center"><b>FECHA</b></th>
            <th style="width: 15px;text-align: center"><b>NUMERO</b></th>
            <th colspan="3" style="width: 36px;text-align: center"><b>ENTRADAS</b></th>
            <th colspan="3" style="width: 36px;text-align: center"><b>SALIDAS</b></th>
            <th colspan="3" style="width: 36px;text-align: center"><b>SALDO</b></th>
        </tr>
        <tr>
            <th><b></b></th>
            <th><b></b></th>
            <th style="width: 12px"><b>CANT.</b></th>
            <th style="width: 12px"><b>PRECIO</b></th>
            <th style="width: 12px"><b>SUBTOTAL</b></th>
            <th style="width: 12px"><b>CANT.</b></th>
            <th style="width: 12px"><b>PRECIO</b></th>
            <th style="width: 12px"><b>SUBTOTAL</b></th>
            <th style="width: 12px"><b>CANT.</b></th>
            <th style="width: 12px"><b>PRECIO</b></th>
            <th style="width: 12px"><b>SUBTOTAL</b></th>
        </tr>
        </thead>
        <tbody>
        @php
            /*$dat = Movimiento::join('detallemovimiento','detallemovimiento.movimiento_id','=','movimiento.id')
                        ->join('tipodocumento','tipodocumento.id','=','movimiento.tipodocumento_id')
                        ->where('detallemovimiento.producto_id','=',$value1->id)
                        ->where('movimiento.fecha','<',$fechaini)
                        ->where('movimiento.sucursal_id','=',$sucursal)
                        ->whereNull('detallemovimiento.deleted_at')
                        ->whereNotIn('movimiento.situacion',['A'])
                        ->whereIn('tipodocumento.stock',['S','R'])
                        ->select(DB::raw('sum(case when tipodocumento.stock=\'S\' then detallemovimiento.cantidad else detallemovimiento.cantidad*(-1) end) as stock'))
                        ->groupBy('detallemovimiento.producto_id')
                        ->first();
            $datNota =  NotaCredito::join('detallenotacredito','detallenotacredito.movimiento_id','=','notacredito.id')
                        ->where('detallenotacredito.producto_id','=',$value1->id)
                        ->where('notacredito.fecha','<',$fechaini)
                        ->where('notacredito.sucursal_id','=',$sucursal)
                        ->whereNull('detallenotacredito.deleted_at')
                        ->whereNotIn('notacredito.situacion',['A'])
                        ->select(DB::raw('sum( detallenotacredito.cantidad ) as stock'))
                        ->groupBy('detallenotacredito.producto_id')
                        ->first();

            if(!is_null($dat)){
                $inicial = $dat->stock;
            }else{
                $inicial = 0;
            }*/
            $inicial = 0; $preciocompra =0 ;
        @endphp
        <tr>
            <td></td>
            <td>SALDO INICIAL</td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td>{{ $inicial }}</td>
            <td>{{ $preciocompra }}</td>
            <td>{{ $inicial*$preciocompra }}</td>
        </tr>
        <?php
        }
        ?>
            <tr>
                <td>{{ date('d/m/Y',strtotime($value->date_time)) }}</td>
                <td>{{ $value->number }}</td>
                @if ($value->type=="I")
                    <td>{{ round($value->quantity,0) }}</td>
                    <td>{{ $value->price }}</td>
                    <td>{{ $value->quantity*$value->price }}</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    @php
                        $div=($value->quantity+$inicial);
                        $preciocompra = round(($value->quantity*$value->price + $inicial*$preciocompra)/($div==0?1:$div),2);
                        $inicial = $inicial + $value->quantity;
                    @endphp
                @else
                    <td></td>
                    <td></td>
                    <td></td>
                    <td>{{ round($value->quantity,0) }}</td>
                    <td>{{ $value->price }}</td>
                    <td>{{ $value->quantity*$value->price }}</td>
                    @php
                        $inicial = $inicial - $value->quantity;
                        if($preciocompra==0){
                            $preciocompra=$value->price;
                        }
                    @endphp
                @endif
                <td>{{ $inicial }}</td>
                <td>{{ $preciocompra }}</td>
                <td>{{ $inicial*$preciocompra }}</td>
            </tr>
        
@endforeach
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

