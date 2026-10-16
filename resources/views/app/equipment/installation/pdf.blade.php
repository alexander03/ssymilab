@php
    use App\Librerias\Libreria;
    use App\Models\ProductMaintenance;
    $product = \App\Models\Product::where('id', $schedule->product_serie->product_id)->withTrashed()->first();
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>CRONOGRAMA DE MANTENIMIENTO - {{ $schedule->id }} - {{ $anio }}</title>
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
            margin-top: 15px;
            margin-left: 15px;
            padding: 50px;
            background-image: url({{ asset("assets/images/imagen5.png") }});
            background-repeat: no-repeat;
            background-size: 111%;
        }

        td {
            font-size: 9px;
            padding: 3px;
        }

        th {
            font-size: 9px;
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
    <img src="http://137.184.71.147:81/public/assets/images/unimedilab-logo.png" width="120" style="margin-top: -30px;">
    <div>
        <p class="margin-10" style="font-size: 2rem; line-height: 1rem;font-weight: 700;text-align: center;margin-top: -3rem;">
            CRONOGRAMA DE MANTENIMIENTO PREVENTIVO {{ $anio }}
        </p>
    </div>
    <br />
    <div>
        <label class="margin-10" style=" line-height: 1rem;font-weight: 700;text-align: left;margin-top: 1.2rem;margin-right: 0px !important;">
            <div style="text-align: right;margin-top: -10px;margin-left: 30px; ">
                <table border="1" style="text-align: center;" align="right">
                    <tr>
                        <th colspan="4">DATOS DEL EQUIPO</th>
                    </tr>
                    <tr>
                        <th>EQUIPO</th>
                        <td colspan='3'>{{ $product->name }}</td>
                    </tr>
                    <tr>
                        <th>MARCA</th>
                        <td>{{ $product->brand->name }}</td>
                        <th>MODELO</th>
                        <td>{{ $product->model }}</td>
                    </tr>
                    <tr>
                        <th>N° SERIE</th>
                        <td colspan="3">{{ $schedule->product_serie->serie }}</td>
                    </tr>
                    <tr>
                        <th>ESTADO</th>
                        <td colspan="3">{{ $schedule->status=='C'?'CESION DE USO':($schedule->status=='V'?'VENDIDO':'PRESTAMO') }}</td>
                    </tr>
                    <tr>
                        <th>FECHA INST.</th>
                        <td colspan="3">{{ date("d/m/Y",strtotime($schedule->installed_date)) }}</td>
                    </tr>
                </table>
            </div>
            <div style="text-align: right;margin-left: 700px;margin-top: -250px ">
                <table border="1" style="text-align: right;" align="right">
                    <tr>
                        <th colspan="2">DATOS DEL CLIENTE</th>
                    </tr>
                    <tr>
                        <th>CLIENTE</th>
                        <td>{{ $schedule->customer_name }}</td>
                    </tr>
                    <tr>
                        <th>LUGAR</th>
                        <td>{{ $schedule->product_place }}</td>
                    </tr>
                    <tr>
                        <th>UBICACION</th>
                        <td>{{ $schedule->product_location }}</td>
                    </tr>
                </table>
            </div>
        </label>
    </div>
    <br /><br /><br />
    <div style="max-width: 200px;">
        <table class="margin-10"  border="1"  style=" width: 800px; border-collapse: collapse; margin-top: 10px;margin-bottom: 10px;margin-left: 30px;" >
            <thead>
                <tr>
                    <th rowspan="2"> ITEM </th>
                    <th rowspan="2"> LISTADO DE TRABAJOS A EJECUTAR </th>
                    <th rowspan="2"> MES </th>
                    <th> 1 </th>
                    <th> 2 </th>
                    <th> 3 </th>
                    <th> 4 </th>
                    <th> 5 </th>
                    <th> 6 </th>
                    <th> 7 </th>
                    <th> 8 </th>
                    <th> 9 </th>
                    <th> 10 </th>
                    <th> 11 </th>
                    <th> 12 </th>
                </tr>
                <tr>
                    <th> ENE </th>
                    <th> FEB </th>
                    <th> MAR </th>
                    <th> ABR </th>
                    <th> MAY </th>    
                    <th> JUN </th>
                    <th> JUL </th>
                    <th> AGO </th>
                    <th> SEP </th>
                    <th> OCT </th>
                    <th> NOV </th>
                    <th> DIC </th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $cont = 0;
                if(!is_null($maintenance)){
                ?>
                @foreach ($maintenance->jobs as $key => $item)
                    @php $cont++; @endphp
                    <tr>
                        <td align="center"> {{ $cont }} </td>
                        <td align="center" colspan="2"> {{ $item->name }} </td>
                        <?php
                        $inicio = date("m",strtotime($schedule->installed_date));
                        if(date("Y",strtotime($schedule->installed_date))==$anio){

                        }else{
                            for($dd=$inicio;$dd<=12;$dd = $dd + $maintenance->period){

                            }
                            $inicio = $dd % 12;
                        }
                        for($x=1;$x<=12;$x++){
                            if($x==($inicio+0)){
                                echo "<td align='center'>".$item->type."</td>";
                                $inicio = $inicio + $maintenance->period;
                            }else{
                                echo "<td></td>";
                            }
                        }
                        ?>
                    </tr>
                @endforeach
                <?php
                }
                ?>
            </tbody>
        </table>
        <div class="margin-10"  style="padding-left:5px; margin-top:10px;margin-left: 300px;">
            <table border="0" style="width: 400px">
                <tr>
                    <th>R = REVISAR ESTADO</th>
                    <th>E = EJECUTAR INSTRUCCION</th>
                    <th>C = CAMBIAR</th>
                </tr>
            </table>
        </div>
        <br />
        <div class="margin-10"  style="padding-left:5px; margin-top:10px">
            <table border="1" style="text-align: right;width: 800px;" align="right">
                <tr>
                    <th colspan="4">DETALLES DEL MANTENIMIENTO</th>
                </tr>
                <tr>
                    <th>FECHA</th>
                    <th>RESPONSABLE DE INGENIERIA</th>
                    <th>PERSONAL USUARIO</th>
                    <th>OBSERVACIONES</th>
                </tr>
                <?php
                $detalle = ProductMaintenance::where('schedule_id','=',$schedule->id)->whereIn('type',['C','P'])->orderBy('done_date','asc')->get();
                foreach ($detalle as $key => $value) {
                    echo "<tr>";
                    echo "<td align='center'>".date("d/m/Y",strtotime($value->done_date))."</td>";
                    echo "<td align='center'>".$value->head_name."</td>";
                    echo "<td align='center'>".$value->staff_name."</td>";
                    echo "<td align='center'>".$value->observations."</td>";
                    echo "</tr>";
                }
                ?>
            </table>
        </div>
        <br />
        <div class="margin-10"  style="padding-left:5px; margin-top:10px;">
            <table border="0" style="width: 400px">
                <tr>
                    <th style="text-align: left;">Nota:</th>
                </tr>
                <tr>
                    <th style="text-align: left;">El cronograma de mantenimiento se realizará según fechas previamente programadas.</th>
                </tr>
                <tr>
                    <th style="text-align: left;">El Dpto. De Ingeniería brindara servicio de atención dentro de las 24 horas del día</th>
                </tr>
            </table>
        </div>
    </div>
</body>
</html>
