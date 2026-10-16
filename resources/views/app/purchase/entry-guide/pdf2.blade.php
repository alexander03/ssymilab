@php
    use App\Librerias\Libreria;
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>ANALISIS ORGANOLEPTICO - {{ $entryguide->number }}</title>
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
    <img src="../public/assets/images/logo.jpeg" width="120">
    <div>
        <p class="margin-10" style="font-size: 1rem; line-height: 1rem;font-weight: 700;text-align: center;margin-top: -4rem;">
            ANALISIS ORGANOLEPTICO
        </p>
    </div>
    <div>
        <label class="margin-10" style=" line-height: 1rem;font-weight: 700;text-align: left;margin-top: 1.2rem;margin-right: 0px !important;">
            Documento de Ingreso:
        </label>
        <label class="margin-10"  style=" line-height: 0.5rem;text-align: left;margin-top: 0rem;">
            {{ $entryguide->number }}
        </label>
        <label class="margin-10" style=" line-height: 1rem;font-weight: 700;text-align: left;margin-top: 1.2rem;margin-right: 0px !important;">
            <?php //Fecha: ?>
        </label>
        <label class="margin-10"  style=" line-height: 0.5rem;text-align: left;margin-top: 0rem;">
            <?php //{{ date("d/m/Y",strtotime($entryguide->date_time)) }} ?>
            <div style="text-align: right;margin-left: 800px;margin-top: -30px ">
                <table border="1" style="text-align: right;" align="right">
                    <tr>
                        <th colspan="2">LEYENDA</th>
                    </tr>
                    <tr>
                        <th>CONFORME</th>
                        <td><?php echo htmlspecialchars("V");?></td>
                    </tr>
                    <tr>
                        <th>NO CONFORME</th>
                        <td> X </td>
                    </tr>
                    <tr>
                        <th>NO APLICA</th>
                        <td>NA</td>
                    </tr>
                </table>
            </div>
        </label>
    </div>
    <hr>
    <div style="max-width: 200px;">
        <table class="margin-10"  border="1"  style=" width: 100%; border-collapse: collapse; margin-top: 10px;margin-bottom: 10px;margin-left: -30px;" >
            <thead>
                <tr>
                    <th colspan="6"> DATOS DEL PRODUCTO </th>
                    <th colspan="8"> EVALUACION DEL PRODUCTO Y ROTULADO </th>
                    <th colspan="3"> INSPECCION VISUAL </th>
                    <th colspan="4"> REVISION FINAL </th>
                </tr>
                <tr>
                    <th rowspan="2"> N° </th>
                    <th rowspan="2"> CODIGO </th>
                    <th rowspan="2"> DESCRIPCION DEL PRODUCTO </th>
                    <th rowspan="2"> LOTE </th>
                    <th rowspan="2"> CANTIDAD </th>
                    <th rowspan="2"> F.V. </th>
                    <th> REG. SANITARIO</th>
                    <th> NOMBRE DEL PRODUCTO</th>
                    <th> PRESENTACION</th>
                    <th> FABRICANTE</th>
                    <th> PAIS</th>
                    <th> NUMERO LOTE</th>
                    <th> F.V.</th>
                    <th> ENVASE MEDIATO</th>
                    <th> ENVASE INMEDIATO</th>
                    <th> TEMP.</th>
                    <th> ROTULADOS</th>
                    <th> DATOS DEL IMPORTADOR</th>
                    <th> CoA</th>
                    <th colspan="2"> RESULTADO</th>
                </tr>
                <tr>
                    <th> NUMERO </th>
                    <th> CONFORME AL RS </th>
                    <th> PRESENTACION AUTORIZADA </th>
                    <th> SEGUN RS </th>
                    <th> SEGUN RS </th>
                    <th> PRESENTE Y VIABLE </th>
                    <th> PRESENTE Y VIABLE </th>
                    <th> LIMPIO, CERRADO SIN SIGNOS DE DETERIORO </th>
                    <th> LIMPIO, CERRADO SIN SIGNOS DE DETERIORO </th>
                    <th>  2 a 8 C / {{ '<=' }} 30°C </th>
                    <th> LEGIBLE, INDELEBLE Y/O BIEN ADHERIDO  </th>
                    <th> CONFORME </th>
                    <th> RESULTADOS DENTRO DE ESPECIFI
                    CACIONES </th>
                    <th> APROBADO </th>
                    <th> RECHAZADO </th>
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
                        <td align="center"> {{ $item->product->code }} </td>
                        <td align="center"> {{ $item->product->name }} </td>
                        <td align="center"> {{ $product_move->lot->lot }} </td>
                        <td align="center"> {{ sprintf('%g', $product_move->quantity) }} </td>
                        <td align="center"> {{ date("d/m/Y",strtotime($product_move->lot->due_date)) }} </td>
                        <td align="center"> {{ $item->product->rs }} </td>
                        <td align="right">  </td>
                        <td align="center"> {{ $item->product->presentation }} </td>
                        <!--td align="center"> {{ $item->product->supplier->getNameComplete() }} </td-->
                        <td align="center"> {{ $item->product->brand->name }} </td>
                        <td align="right">  </td>
                        <td align="right">  </td>
                        <td align="right">  </td>
                        <td align="right">  </td>
                        <td align="right">  </td>
                        <td align="right">  </td>
                        <td align="right">  </td>
                        <td align="right">  </td>
                        <td align="right">  </td>
                        <td align="right">  </td>
                        <td align="right">  </td>
                    </tr>
                    @endif
                    @endforeach
                @endforeach
                
            </tbody>
        </table>

        <div class="margin-10"  style="padding-left:5px; margin-top:10px">
            <p style=" line-height: 0.5rem;font-weight: 700;text-align: left;margin-top:25px; text-decoration: underline;">
                Observaciones Reportadas:
            </p>
            <p style=" line-height: 0.5rem;font-weight: 700;text-align: left;margin-top:25px;">
                ____________________________________________________________________________________________________________________________________________________________________________________________
            </p>
            <p style=" line-height: 0.5rem;font-weight: 700;text-align: left;margin-top:25px;">
                ____________________________________________________________________________________________________________________________________________________________________________________________
            </p>
            <p style=" line-height: 0.5rem;font-weight: 700;text-align: left;margin-top:25px;">
                ____________________________________________________________________________________________________________________________________________________________________________________________
            </p>
            </div>
        </div>
        <br />
        <br />
        <div>
            <p style=" line-height: 0.5rem;font-weight: 700;text-align: center;margin-top:25px;">
                ______________________________&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;_________________________
            </p>
            <p class="margin-10" style="font-size: 1rem; line-height: 1rem;font-weight: 700;text-align: center;">
                Liberado por&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Fecha
            </p>
        </div>
    </div>
</body>
</html>
