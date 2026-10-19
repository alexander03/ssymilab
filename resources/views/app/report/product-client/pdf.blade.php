<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>REPORTE</title>
    <style>
        *{
            font-family: Verdana, Geneva, Tahoma, sans-serif;
            font-size: 0.85rem;
        }
        body{
            margin: 20px;
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
            border-collapse: collapse;
            border: 1px solid black;
        }
    </style>
</head>
<body>
    <img src="http://137.184.71.147:86/public/assets/images/logo.jpeg" width="120">
    <div>
        <p style=" line-height: 1rem;font-weight: 700;text-align: center;margin-top: -2rem;">
            REPORTE DE PRODUCTOS POR CLIENTE
        </p>
        <p style="text-align: center;">
            {{ $date_start ? date('d/m/Y', strtotime($date_start)) : '-' }} AL {{ $date_end ? date('d/m/Y', strtotime($date_end)) : '-' }}
        </p>
    </div>

    <table border="1" style="width: 100%; border-collapse: collapse; margin-top: 10px; margin-bottom: 10px;">
        <thead>
            <tr>
                <th> CÓDIGO </th>
                <th> PRODUCTO </th>
                <th> MARCA </th>
                <th> UNIDAD </th>
                <th> CANTIDAD </th>
                <th> IMPORTE (S/) </th>
            </tr>
        </thead>
        <tbody>
            @php
                $total_amount = 0;
            @endphp
            @foreach ($clients as $client_item)
                @php
                    $client_data = $details[$client_item->client_id] ?? ["products" => [], "brands" => []];
                    $total_amount += $client_item->amount;
                @endphp
                <tr>
                    <th colspan="6" style="text-align: left; background-color: #e5e7eb;">
                        CLIENTE: {{ $client_item->client_name }} ({{ $client_item->client_document }})
                    </th>
                </tr>
                @foreach ($client_data["products"] as $row)
                    <tr>
                        <td align="center">{{ $row->product_code }}</td>
                        <td>{{ $row->product_name }}</td>
                        <td align="center">{{ $row->brand_name }}</td>
                        <td align="center">{{ $row->unit_name }}</td>
                        <td align="right">{{ number_format($row->quantity, 2, '.', '') }}</td>
                        <td align="right">{{ number_format($row->amount, 2, '.', '') }}</td>
                    </tr>
                @endforeach
                <tr>
                    <td colspan="6" style="font-weight: bold;">RESUMEN POR MARCA</td>
                </tr>
                @foreach ($client_data["brands"] as $brand)
                    <tr>
                        <td colspan="2"></td>
                        <td align="center" style="font-weight: bold;">{{ $brand->brand_name }}</td>
                        <td align="center" style="font-weight: bold;">{{ $brand->unit_name }}</td>
                        <td align="right" style="font-weight: bold;">{{ number_format($brand->quantity, 2, '.', '') }}</td>
                        <td align="right" style="font-weight: bold;">{{ number_format($brand->amount, 2, '.', '') }}</td>
                    </tr>
                @endforeach
                <tr>
                    <th colspan="5" style="text-align: right;">TOTAL {{ $client_item->client_name }}</th>
                    <th style="text-align: right;">{{ number_format($client_item->amount, 2, '.', '') }}</th>
                </tr>
            @endforeach
            <tr>
                <th colspan="5" style="text-align: right;">TOTAL GENERAL</th>
                <th style="text-align: right;">{{ number_format($total_amount, 2, '.', '') }}</th>
            </tr>
        </tbody>
    </table>
</body>
</html>
