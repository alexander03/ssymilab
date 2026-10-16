<?php
use App\Product;
use Illuminate\Support\Facades\DB;
$inicial = 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>REPORTE KARDEX LOTE</title>
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
    <table border="1">
        <thead>
            <tr>
                <th colspan="7" style="text-align: center"><b>REPORTE KARDEX LOTE {{ $lot->lot }} - {{ date("d/m/Y",strtotime($lot->due_date)) }}</b></th>
            </tr>
            <tr>
                <th style="width: 20px" colspan="7" ><b>PRODUCTO: {{ $lot->product->name.' '.$lot->product->presentation.' '.$lot->product->model.' '.$lot->product->brand->name }}</b></th>
            </tr>
            <tr>
                <th style="width: 15px;text-align: center"><b>SUCURSAL</b></th>
                <th style="width: 15px;text-align: center"><b>FECHA</b></th>
                <th style="width: 15px;text-align: center"><b>NUMERO</b></th>
                <th style="width: 15px;text-align: center"><b>PROVEEDOR/CLIENTE</b></th>
                <th style="width: 36px;text-align: center"><b>ENTRADAS</b></th>
                <th style="width: 36px;text-align: center"><b>SALIDAS</b></th>
                <th style="width: 36px;text-align: center"><b>SALDO</b></th>
            </tr>
        </thead>
        <tbody>
        @foreach($data as $value)
            <tr>
                <td>{{ $value->branch_office->name }}</td>
                <td>{{ date('d/m/Y',strtotime($value->created_at)) }}</td>
                <td>{{ $value->document->number.' | '.(is_null($value->document->reason)?'-':$value->document->reason->name) }}</td>
                <td>{{ $value->document->client->getNameComplete() }}</td>
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
                        $inicial = $inicial - $value->quantity;
                    @endphp
                @endif
                <td style="text-align: center;">{{ $inicial }}</td>
            </tr>
        @endforeach
</body>
</html>

