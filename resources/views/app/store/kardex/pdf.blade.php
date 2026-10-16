<?php
use App\Models\Kardex;
use App\Models\Product;
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
            <th style="width: 20px" colspan="11" ><b>PRODUCTO: {{ trim($value->product_name.' '.$value->model.' '.$value->presentation.' '.$value->brand_name) }}</b></th>
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
            $inicial = 0; $preciocompra =0 ;
            $user = Auth::user();
            $fechafin = date("Y-m-d",strtotime("-1 day",strtotime($fechainicio)));
            $list = Kardex::search($value->product_id, null, null, $fechafin, $user->branch_office_id)
                ->select('kardexes.*', 'products.name as product_name','documents.date_time','documents.number','people.business_name','people.name','people.surname_father','people.surname_mother','products.presentation','products.model','brands.name as brand_name')
                ;
            $list = $list->orderBy('products.id','asc')->orderBy('documents.date_time','asc')->get();
            foreach($list as $k){
                if ($k->type=="I"){
                    $div=($k->quantity+$inicial);
                    $preciocompra = round(($k->quantity*$k->price + $inicial*$preciocompra)/($div==0?1:$div),2);
                    $inicial = $inicial + $k->quantity;
                }else{
                    $inicial = $inicial - $k->quantity;
                    if($preciocompra==0){
                        $preciocompra=$k->price;
                    }
                }
            }
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
                <td>{{ $value->number.' | '.(is_null($value->document->reason)?'-':$value->document->reason->name) }}</td>
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

