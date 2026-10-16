@php
    use App\Librerias\Libreria;
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>ACTA DE RECEPCION DE PRODUCTO - {{ $entryguide->number }}</title>
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
            font-size: 11px;
            padding: 4px;
        }

        th {
            font-size: 11px;
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
            ACTA DE RECEPCION DE PRODUCTOS
        </p>
    </div>
    <div>
        <label class="margin-10" style=" line-height: 1rem;font-weight: 700;text-align: left;margin-top: 1.2rem;margin-right: 0px !important;">
            Guia/Facturas/Otros:
        </label>
        <label class="margin-10"  style=" line-height: 0.5rem;text-align: left;margin-top: 0rem;">
            {{ $entryguide->number }}
        </label>
        <label class="margin-10" style=" line-height: 1rem;font-weight: 700;text-align: left;margin-top: 1.2rem;margin-right: 0px !important;">
            Fecha:
        </label>
        <label class="margin-10"  style=" line-height: 0.5rem;text-align: left;margin-top: 0rem;">
            {{ date("d/m/Y",strtotime($entryguide->date_time)) }}
        </label>
        
    </div>
    <br />
    <div class="">
        <label class="margin-10"  style=" line-height: 0.5rem;font-weight: 700;text-align: left;margin-top: 1.1rem;margin-right: 0px !important;">
            Fabricante/Proveedor:
        </label>
        <label class="margin-10"  style=" line-height: 0.5rem;text-align: left;margin-top: 0rem;">
            {{ $entryguide->client->getDocumentNumber() }} - {{ $entryguide->client->getNameComplete() }}
        </label>
    </div>
    <br />
    <div>
        <label class="margin-10"  style=" line-height: 0.5rem;font-weight: 700;text-align: left;margin-top: 1.1rem;">
            Procedencia:
        </label>
        <label class="margin-10"  style=" line-height: 0.5rem;text-align: left;margin-top: 0rem;">
            
        </label>
    </div>
    <br />
    <hr>
    <div>
        <table class="margin-5"  border="1"  style=" width: 100%; border-collapse: collapse; margin-top: 10px;margin-bottom: 10px; padding:5px" >
            <thead>
                <tr>
                    <th rowspan="2"> <?php echo utf8_encode("N�");?> </th>
                    <th rowspan="2"> PRODUCTO </th>
                    <th rowspan="2"> PRESENTACION </th>
                    <th rowspan="2"> LOTE </th>
                    <th rowspan="2"> F.V. </th>
                    <th rowspan="2"> COND. ALMACEN </th>
                    <th colspan="2" style="width: 120px;"> CANTIDAD </th>
                    <th rowspan="2"> CERTIFICADO ANALISIS </th>
                </tr>
                <tr>
                    <th> S </th>
                    <th> R </th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $cont = 0;
                ?>
                @foreach ($entryguide->details as $key => $item)
                    @foreach ($item->product_moves as $product_move)
                    @if (!is_null($product_move->product) && $product_move->product->type == "R")
                    @php $cont++; @endphp
                    <tr>
                        <td align="center"> {{ $cont }} </td>
                        <td align="center"> {{ $item->product->name }} </td>
                        <td align="center"> {{ $item->unit->name }} </td>
                        <td align="center"> {{ $product_move->lot->lot }} </td>
                        <td align="center"> {{ date("d/m/Y",strtotime($product_move->lot->due_date)) }} </td>
                        <td align="center"> {{ $item->product->condition }} </td>
                        <td align="right">  </td>
                        <td align="right"> {{ sprintf('%g', $product_move->quantity) }} </td>
                        <td align="right">  </td>
                    </tr>
                    @endif
                    @endforeach
                @endforeach
                
            </tbody>
        </table>

        <div class="margin-10"  style="padding-left:5px; margin-top:10px">
            <p style=" line-height: 0.5rem;font-weight: 700;text-align: left;margin-top:25px; text-decoration: underline;">
                Observaciones:
            </p>
            <p style=" line-height: 0.5rem;font-weight: 700;text-align: left;margin-top:25px;">
                __________________________________________________________________________________________________
            </p>
            <p style=" line-height: 0.5rem;font-weight: 700;text-align: left;margin-top:25px;">
                __________________________________________________________________________________________________
            </p>
            <p style=" line-height: 0.5rem;font-weight: 700;text-align: left;margin-top:25px;">
                __________________________________________________________________________________________________
            </p>
            </div>
        </div>
        <br />
        <br />
        <div>
            <p style=" line-height: 0.5rem;font-weight: 700;text-align: center;margin-top:25px;">
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;_______________________&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;_____________________
            </p>
            <p class="margin-10" style="font-size: 1rem; line-height: 1rem;font-weight: 700;text-align: center;">
                V.B. Director T&eacute;cnico&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Jefe de Almac&eacute;n                                                    
            </p>
        </div>
    </div>
</body>
</html>
