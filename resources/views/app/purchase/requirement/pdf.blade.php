@php
    use App\Librerias\Libreria;
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
            <img src="http://67.205.164.246/public/assets/images/logo.jpeg" width="120">
        @else
            <img src="http://67.205.164.246/public/assets/images/logo.jpeg" width="120">
        @endif
    </div>
    <div style="margin-top: -140px;margin-left: 498px; border-width: 1px;border-radius: 0.375rem;">
        <table style="border-collapse: collapse;width: 196px;border: 1px solid black;border-spacing: 0px;"  border="0">
            <tr>
                <th style="height: 23px;font-size: 15px !important;">REQUERIMIENTO COMPRA</th>
            </tr>
            <tr>
                <th style="height: 23px;font-size: 15px !important;">RUC: {{ $proforma->branch_office->company->ruc }}</th>
            </tr>
            <tr>
                <th style="height: 24px;font-size: 15px !important;">{{ $proforma->number }} </th>
            </tr>
        </table>
    </div>
    <table style="margin-top: 10px;border: 1px solid black;border-spacing: 0px;border-collapse: collapse;" border="1">
        <tr>
            <th style="text-align: left;">FECHA:</th>
            <td>{{ date_format(new DateTime($proforma->date_time), 'd/m/Y') }}</td>
        </tr>
        <tr>
            <th style="text-align: left;">DNI/RUC:</th>
            <td>{{ $proforma->client->getDocumentNumber() }}</td>
        </tr>
        <tr>
            <th style="text-align: left;">PROVEEDOR:</th>
            <td>{{ $proforma->client->getNameComplete() }}</td>
        </tr>
        <tr>
            <th style="text-align: left;">COMENTARIO:</th>
            <td style="text-align: left;">{{ $proforma->comment() }}</td>
        </tr>
        <tr>
            <th colspan="2"></th>
        </tr>
    </table>
    <hr style="margin-top: 10px;">

        <table class="margin-10"  border="1"  style=" width: 100%; border-collapse: collapse; margin-top: 10px;margin-bottom: 10px; padding:5px;border: 1px solid black;" >
            <thead>
                <tr>
                    <th style="width: 300px;font-size: 9px;"> DESCRIPCION </th>
                    <th style="font-size: 9px;"> U.M </th>
                    <th style="font-size: 9px;"> CANT. </th>
                    <!--th style="font-size: 9px;"> PRECIO UNITARIO </th>
                    <th style="width: 80px;font-size: 9px"> PRECIO TOTAL </th-->
                </tr>
            </thead>
            <tbody>
                @foreach ($proforma->details as $key => $item)
                <tr>
                    <!--td align="center"> {{ $key + 1 }} </td-->
                    <td style="font-size: 9px;" align="left"> {{ $item->product->name.' '.$item->product->presentation }}<br/>MARCA: {{$item->product->brand->name }} </td>
                    <td style="font-size: 9px;" align="center"> {{ $item->unit->name }} </td>
                    <td style="font-size: 9px;" align="center"> {{ sprintf('%g', $item->quantity) }} </td>
                    <!--td style="font-size: 9px;" align="right"> {{ number_format($item->purchase_price,2) }} </td>
                    <td style="font-size: 9px;" align="right"> {{ number_format($item->purchase_price * $item->quantity,2) }} </td-->
                </tr>
                @endforeach
                <!--tr>
                    <td align="right" style="font-weight: 700;font-size:11px;" colspan="4">TOTAL</td>
                    <td align="right" colspan="1" style="font-size: 11px;">{{ number_format($proforma->total,2) }}</td>
                </tr-->
            </tbody>
        </table>
    </div>
</body>
</html>
