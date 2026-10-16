@php
    use App\Librerias\Libreria;
    use App\Models\Product;
    use App\Models\Lot;
    use App\Models\ProductSerie;
    use Illuminate\Support\Facades\DB;
    use Illuminate\Support\Facades\Auth;
    $brand_id=0;
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>INVENTARIO DE PRODUCTOS</title>
    <style>
        *{
            font-family: Verdana, Geneva, Tahoma, sans-serif;
            font-size: 0.85rem;
            margin:0px;
        }
        .margin-10{
            margin-left:20px;
            margin-right:15px;
            margin-top:20px;
            margin-bottom:15px;
        }
        body{
            margin-top: 10px;
            margin-left: 10px;
            padding: 50px;
            background-image: url({{ asset("assets/images/imagen5.png") }});
            background-repeat: no-repeat;
            background-size: 111%;
        }

        td {
            font-size: 10px;
            padding: 4px;
        }

        th {
            font-size: 10px;
            padding: 4px;
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
    <div>
        <p class="margin-10" style="font-size: 1rem; line-height: 1rem;font-weight: 700;text-align: center;margin-top: -2rem;">
            INVENTARIO DE PRODUCTOS AL {{ date("d/m/Y") }}
        </p>
    </div>
    <div>
        @foreach ($lista as $item)
            <?php 
            if($brand_id!=$item->brand_id){
                if($brand_id!="0"){
                    echo "</tbody></table><br />";
                }
                $brand_id = $item->brand_id;
            ?>
            <table class="margin-5"  border="1"  style=" width: 100%; border-collapse: collapse; margin-top: 10px;margin-bottom: 10px;" >
                <thead>
                    <tr>
                        <th style="text-align: left;" colspan="4">MARCA: {{ ($item->brand) ? $item->brand->name : "[ELIMINADO]" }} </th>
                    </tr>
                    <tr>
                        <th style="width: 80px;" > CODIGO </th>
                        <th style="width: 380px;"> NOMBRE </th>
                        <th > UNIDAD BASE </th>
                        <th > STOCK </th>
                    </tr>
                </thead>
                <tbody>
            <?php 
            }
            ?>
            <tr>
                <td align="center" style="width: 80px;"> {{ $item->code }}</td>
                <td align="left" style="width: 280px;"> {{ $item->name.' '.$item->presentation.' '.$item->model }} 
                    <?php 
                    if(($item->stock+0)>0){
                        echo "<br />";
                        if($item->type=="R"){
                            $lot = Lot::where('product_id','=',$item->id)->where('branch_office_id','=',Auth::user()->branch_office_id)->where('status','like','Y')->where('remain','>',0)->get();
                            foreach ($lot as $value) {
                                echo "F.V.: ".date("d/m/Y",strtotime($value->due_date))."  | Lote: ".$value->lot."  |  Cant: ".round($value->remain,0)."  |  Cod. Barra: ".$value->barcode." <br />";
                            }
                        }else{
                            $serie = ProductSerie::where('product_id','=',$item->id)->where('branch_office_id','=',Auth::user()->branch_office_id)->where('status','like','Y')->get();
                            foreach ($serie as $value) {
                                echo "Serie: ".$value->serie." <br />";
                            }
                        }
                    }
                    ?>
                </td>
                <td align="center"> {{ ($item->base_unit) ? $item->base_unit->name : "[ELIMINADO]" }} </td>
                <td align="right"> {{ number_format($item->stock,0) }} </td>
            </tr>
        @endforeach
            </tbody>
        </table>
    </div>
</body>
</html>
