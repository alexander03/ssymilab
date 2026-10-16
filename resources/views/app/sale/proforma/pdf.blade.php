@php
    use App\Librerias\Libreria;
    $total=0;
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>PROFORMA - {{ $proforma->number }}</title>
    <style>
        *{
            font-family: Verdana, Geneva, Tahoma, sans-serif;
            font-size: 0.85rem;
            margin:0px;
        }
        .margin-10{
            margin-left:0px;
            margin-right:15px;
            margin-top:20px;
            margin-bottom:15px;
        }
        body{
            margin-top: 0px;
            margin-left: 0px;
            padding: 50px;
            background-image: url({{ asset("assets/images/fondo-membretado.png") }});
            background-repeat: no-repeat;
            background-size: 100%;
        }

        td {
            font-size: 11px;
            padding: 5px;
        }

        th {
            font-size: 11px;
            padding: 5px;
        }
    </style>
</head>
<body>
    <div>
        @if($proforma->branch_office_id==1)
            <img src="{{ asset("assets/images/logo-chiclayo.jpg") }}" width="480px;" height="100px;" />
        @else
            <img src="{{ asset("assets/images/logo-lima.jpg") }}" width="480px;" height="100px;" />
        @endif
    </div>
    <div style="margin-top: -140px;margin-left: 498px; border-width: 1px;border-radius: 0.375rem;">
        <table style="border-collapse: collapse;width: 196px;border: 1px solid black;border-spacing: 0px;"  border="0">
            <tr>
                <th style="height: 23px;font-size: 15px !important;">PROFORMA</th>
            </tr>
            <tr>
                <th style="height: 23px;font-size: 15px !important;">RUC: {{ $proforma->branch_office->company->ruc }}</th>
            </tr>
            <tr>
                <th style="height: 24px;font-size: 15px !important;">{{ $proforma->number }} </th>
            </tr>
        </table>
    </div>
    <table style="margin-top: 8px;border: 1px solid black;border-spacing: 0px;border-collapse: collapse;" border="1">
        <tr>
            <td>
                <table border="0">
                    <tr>
                        <th colspan="2" style="text-decoration: underline;text-align: left;">DATOS DEL CLIENTE</th>
                    </tr>
                    <tr>
                        <th colspan="2"></th>
                    </tr>
                    <tr>
                        <th style="text-align: left;">RUC:</th>
                        <td>{{ $proforma->client->getDocumentNumber() }}</td>
                    </tr>
                    <tr>
                        <th colspan="2"></th>
                    </tr>
                    <tr>
                        <th style="text-align: left;">SEÑOR(ES):</th>
                        <td>{{ $proforma->client->getNameComplete() }}</td>
                    </tr>
                    <tr>
                        <th colspan="2"></th>
                    </tr>
                    <tr>
                        <th style="text-align: left;">DIRECCION:</th>
                        <td style="text-align: left;width: 396px;">{{ $proforma->client->address }}</td>
                    </tr>
                    <tr>
                        <th colspan="2"></th>
                    </tr>
                </table>
            </td>
    <?php
    $details = ($proforma->detail && trim($proforma->detail) != "") ? json_decode($proforma->detail, true) : [];
    ?>
            <td>
                <table border="0">
                    <tr>
                        <th colspan="2" style="text-decoration: underline;text-align: left;">DATOS DEL DOCUMENTO</th>
                    </tr>
                    <tr>
                        <th style="text-align: left;">FECHA:</th>
                        <td>{{ date("d/m/Y",strtotime($proforma->date_time)) }}</td>
                    </tr>
                    <tr>
                        <th style="text-align: left;">COD. USUARIO:</th>
                        <td>{{ $proforma->user->person->code }}</td>
                    </tr>
                    <tr>
                        <th style="text-align: left;">VALIDEZ OFERTA:</th>
                        <td>{{ (array_key_exists('validez', $details)) ? $details["validez"] : ""; }}</td>
                    </tr>
                    @if(((array_key_exists('condicion', $details)) ? $details["condicion"] : "")!="")
                        <tr>
                            <th style="text-align: left;">COND. PAGO:</th>
                            <td>{{ (array_key_exists('condicion', $details)) ? $details["condicion"] : ""; }}</td>
                        </tr>
                    @else
                        <tr>
                            <th colspan="2"></th>
                        </tr>
                    @endif

                    <tr>
                        <th style="text-align: left;">MONEDA:</th>
                        <td>{{ $proforma->currency->name }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
    <hr style="margin-top: 8px;">
    <div>
        <p class="margin-10"  style=" line-height: 0.5rem;font-weight: 700;text-align: left;margin-top: 1.1rem;">
            Estimados Señores:
        </p>
        <p class="margin-10"  style=" line-height: 0.5rem;text-align: left;margin-top: 0rem;">
            Por intermedio de la presente le hacemos llegar nuestra cotización, en referencia a su solicitud.
        </p>
    </div>
    <div>

        @foreach ($proforma->details as $key => $item)
            @if ($item->product->type == "M")
            <p class="margin-10"  style="font-size: 16px; line-height: 1.5rem;text-align: center;margin-top: 0rem;font-weight: 700; margin-top:20px">
                {{$item->product->name.' '.$item->product->model}}
            </p>
            <div class="margin-10"  id="specifications" style="padding-left:5px; margin-top:15px">
                {!! str_replace("<table>", "<table border='1'>" , $item->product->specifications) !!}
            </div>

                @php
                    $medias = $item->product->getMedia('images');
                @endphp
                @if (count($medias) > 0)
                    @php
                        $media = $medias[0];
                    @endphp
                    <div class="margin-10"  style="text-align: center; margin-top:40px !important ; margin-bottom:50px">
                        <img src="{{$media->getPath()}}" width="350px" style="margin-top:60px" alt="">
                    </div>
                @endif
            @endif
        @endforeach

        <table class="margin-10"  border="1"  style=" width: 100%; border-collapse: collapse; margin-top: 10px;margin-bottom: 10px; padding:5px;border: 1px solid black;" >
            <thead>
                <tr>
                    <th style="font-size: 9px;"> CODIGO </th>
                    <th style="width: 300px;font-size: 9px;"> DESCRIPCION </th>
                    @if($proforma->details[0]->product->type=='R')
                        <th style="font-size: 9px;width: 50px"> F.V. </th>
                    @endif
                    <th style="font-size: 9px;" > PLAZO DE ENTREGA </th>
                    <th style="font-size: 9px;"> CANT. </th>
                    <th style="font-size: 9px;"> U.M </th>
                    <th style="font-size: 9px;"> PRECIO UNITARIO </th>
                    <th style="width: 80px;font-size: 9px"> PRECIO TOTAL </th>
                </tr>
            </thead>
            <tbody>
                @foreach ($proforma->details as $key => $item)
                    <?php
                    $details = ($item->detail && trim($item->detail) != "") ? json_decode($item->detail, true) : [];
                    $fv = (array_key_exists('due_date', $details)) ? $details["due_date"] : "";
                    $plazo = (array_key_exists('delivery_time', $details)) ? $details["delivery_time"] : "";
                    $total = $total + $item->sell_price * $item->quantity;
                    ?>
                <tr>
                    <!--td align="center"> {{ $key + 1 }} </td-->
                    <td style="font-size: 9px;" align="center"> {{ $item->product->code }} </td>
                    <td style="font-size: 9px;" align="left"> {{ $item->product->name.' '.$item->product->presentation }}<br/>MARCA: {{$item->product->brand->name }} </td>
                    @if($proforma->details[0]->product->type=='R')
                        <td style="font-size: 9px;" align="center"> {{ $fv }} </td>
                    @endif
                    <td style="font-size: 9px;" align="center"> {{ $plazo }} </td>
                    <td style="font-size: 9px;" align="center"> {{ sprintf('%g', $item->quantity) }} </td>
                    <td style="font-size: 9px;" align="center"> {{ $item->unit->name }} </td>
                    <td style="font-size: 9px;" align="right"> {{ number_format($item->sell_price,2) }} </td>
                    <td style="font-size: 9px;" align="right"> {{ $proforma->currency->symbol }} {{ number_format($item->sell_price * $item->quantity,2) }} </td>
                </tr>
                @endforeach
                <tr>
                    @if($proforma->details[0]->product->type=='R')
                        <td align="right" style="font-weight: 700;font-size:11px;" colspan="7">TOTAL</td>
                    @else
                        <td align="right" style="font-weight: 700;font-size:11px;" colspan="6">TOTAL</td>
                    @endif
                    <td align="right" colspan="1" style="font-size: 11px;">{{ $proforma->currency->symbol }}  {{ number_format($total,2) }}</td>
                </tr>
            </tbody>
        </table>
        <div class="margin-10" style="padding-left:5px;">
            <p style="font-weight: 700;">Son:  {{ Libreria::convertir($total) }}</p>
            <p style="text-align: right;font-weight: 700;margin-top: -10px;">(PRECIOS INCLUYEN IGV)</p>
        </div>
        @if($proforma->comment()!="")
        <div class="margin-10"  style="padding-left:5px; margin-top:10px">
            <p style=" line-height: 0.5rem;font-weight: 700;text-align: left;margin-top:25px; text-decoration: underline;">
                CONDICIONES DE VENTA:
            </p>
            <div style="line-height: 1.5rem;text-align: left;margin-top: 0rem;">
                {!! str_replace("<table>", "<table border='1'>" , $proforma->comment()) !!}
            </div>
        </div>
        @endif
        <div class="margin-10"  style="padding-left:5px; margin-top:10px">
            <p style=" line-height: 0.5rem;font-weight: 700;text-align: left;margin-top:25px; text-decoration: underline;">
                Paguese a la orden de UNIMEDILAB SAC
            </p>
            <table border="1"  style=" width: 65%; border-collapse: collapse; margin-top: 10px;margin-bottom: 10px;border: 1px solid black;" >
                <tr>
                    <td style="font-weight: 700;width: 55%;">CTA BCP SOLES: 305-2227461-0-13 </td><td style="font-weight: 700;">CCI: 002-305-00222746101312 </td>
                </tr>
                <tr>
                    <td style="font-weight: 700;width: 55%;">CTA BBVA SOLES: 0011-0285-0100111684 </td><td style="font-weight: 700;">CCI: 0011-285-000100011168445 </td>
                </tr>
            </table>
        </div>
        <div class="margin-10"  style="padding-left:5px; margin-top:15px">
            <p style="line-height: 0.5rem;text-align: left;margin-top: 0rem;font-weight: 700; margin-top:10px">
                UNIMEDILAB SAC.
            </p>
            <p style="line-height: 0.5rem;text-align: left;margin-top: 0rem;font-weight: 700; margin-top:10px">
                20538910717
            </p>
            <p style="line-height: 0.5rem;text-align: left;margin-top: 0rem;font-weight: 700; margin-top:10px">
                Departamento de Ventas y Licitaciones
            </p>
            <p style="line-height: 0.5rem;text-align: left;margin-top: 0rem;font-weight: 700; margin-top:10px">
                E-mail: ventas@unimedilab.com.pe
            </p>
            <p style="line-height: 0.5rem;text-align: left;margin-top: 0rem;font-weight: 700; margin-top:10px">
                Celular: 938230483
            </p>
        </div>
    </div>
</body>
</html>
