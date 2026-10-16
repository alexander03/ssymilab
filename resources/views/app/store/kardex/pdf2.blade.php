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
    <title>REPORTE KARDEX</title>
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
    <table border="1" width="100%">
        <thead>
        <tr>
            <th colspan="6" style="text-align: center"><b>REPORTE KARDEX DEL {{ date('d/m/Y',strtotime($fechainicio)) }} AL {{ date('d/m/Y',strtotime($fechafin)) }}</b></th>
        </tr>
        <tr>
            <th style="width: 20px" colspan="6" ><b>PRODUCTO: {{ trim($value->product_name.' '.$value->model.' '.$value->presentation.' '.$value->brand_name) }}</b></th>
        </tr>
        <tr>
            <th style="width: 15px;text-align: center"><b>FECHA</b></th>
            <th style="width: 15px;text-align: center"><b>NUMERO</b></th>
            <th style="width: 15px;text-align: center"><b>PROVEEDOR/CLIENTE</b></th>
            <th style="width: 36px;text-align: center"><b>ENTRADAS</b></th>
            <th style="width: 36px;text-align: center"><b>SALIDAS</b></th>
            <th style="width: 36px;text-align: center"><b>SALDO</b></th>
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
                    $inicial = $inicial + $k->quantity;
                }else{
                    $inicial = $inicial - $k->quantity;
                }
            }
        @endphp
        <tr>
            <td></td>
            <td>SALDO INICIAL</td>
            <td></td>
            <td></td>
            <td></td>
            <td style="text-align: center;">{{ $inicial }}</td>
        </tr>
        <?php
        }
        ?>
            <tr>
                <td>{{ date('d/m/Y',strtotime($value->date_time)) }}</td>
                <td>{{ $value->number.' | '.(is_null($value->document->reason)?'-':$value->document->reason->name) }}</td>
                <td>{{ trim($value->business_name.' '.$value->name.' '.$value->surname_father.' '.$value->surname_mother) }} </td>
                @if ($value->type=="I")
                    <td style="text-align: center;">{{ round($value->quantity,0) }}</td>
                    <td></td>
                    @php
                        $inicial = $inicial + $value->quantity;
                    @endphp
                @else
                    
                    <td></td>
                    <td style="text-align: center;">{{ round($value->quantity,0) }}</td>
                    @php
                        $inicial = $inicial- $value->quantity;
                    @endphp
                @endif
                <td style="text-align: center;">{{ $inicial }}</td>
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

