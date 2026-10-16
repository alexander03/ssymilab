@php
    use App\Librerias\Libreria;
    use App\Models\ProductMaintenance;
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>SEGUIMIENTO DE EQUIPO</title>
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
    <img src="http://137.184.71.147:81/public/assets/images/unimedilab-logo.png" width="120">
    <div>
        <p class="margin-10" style="font-size: 2rem; line-height: 1rem;font-weight: 700;text-align: center;margin-top: -4rem;">
            SEGUIMIENTO DEL EQUIPO
        </p>
    </div>
    <div>
        <label class="margin-10" style=" line-height: 1rem;font-weight: 700;text-align: left;margin-top: 1.2rem;margin-right: 0px !important;">
            <div style="text-align: right;margin-top: -10px;margin-left: 30px; ">
                <table border="1" style="text-align: center;" align="right">
                    <tr>
                        <th colspan="4">DATOS DEL EQUIPO</th>
                    </tr>
                    <tr>
                        <th>EQUIPO</th>
                        <td colspan='3'>{{ $schedule->product_serie->product->name }}</td>
                    </tr>
                    <tr>
                        <th>MARCA</th>
                        <td>{{ $schedule->product_serie->product->brand->name }}</td>
                        <th>MODELO</th>
                        <td>{{ $schedule->product_serie->product->model }}</td>
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
                    <th> FECHA </th>
                    <th> TIPO MANT. </th>
                    <th> PRODUCTO </th>
                    <th> CANTIDAD </th>
                    <th> SERIE/LOTE </th>
                    <th> FECHA VENC. </th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $cont = 0;
                ?>
                @foreach ($list as $key => $item)
                    @php $cont++; @endphp
                    <tr>
                        <td align="center"> {{ date("d/m/Y",strtotime($item->product_maintenance->done_date)) }} </td>
                        <td align="center"> {{ $item->product_maintenance->type=='P'?'Preventivo':($item->product_maintenance->type=='C'?'Correctivo':($item->product_maintenance->type=='A'?'Asistencia':'Instalacion')) }} </td>
                        <td align="center"> {{ $item->product->name }} </td>
                        <td align="center"> {{ round($item->quantity,0) }} </td>
                        <td align="center"> {{ ($item->product->type=='R')?$item->lot->lot:$item->product_serie->serie}} </td>
                        <td align="center"> {{ ($item->product->type=='R')?$item->lot->due_date:'-'}} </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</body>
</html>
