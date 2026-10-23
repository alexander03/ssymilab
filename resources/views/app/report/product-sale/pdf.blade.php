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
            REPORTE DE VENTAS POR PRODUCTO
        </p>
        <p style="text-align: center;">
            {{ $date_start ? date('d/m/Y', strtotime($date_start)) : '-' }} AL {{ $date_end ? date('d/m/Y', strtotime($date_end)) : '-' }}
        </p>
    </div>

    @if (count($brands) == 0)
        <p style="text-align: center;">No se encontraron registros</p>
    @else
        <table border="1" style="width: 100%; border-collapse: collapse; margin-top: 10px; margin-bottom: 10px;">
            <thead>
                <tr>
                    <th> CÓDIGO </th>
                    <th> PRODUCTO </th>
                    <th> UNIDAD </th>
                    <th> CANTIDAD </th>
                    <th> IMPORTE (S/) </th>
                </tr>
            </thead>
            <tbody>
                @foreach ($brands as $brand_item)
                    @php
                        $brand_data = $details[$brand_item->brand_id] ?? ["products" => [], "units" => []];
                    @endphp
                    <tr>
                        <th colspan="5" style="text-align: left; background-color: #e5e7eb;">MARCA: {{ $brand_item->brand_name }}</th>
                    </tr>
                    @foreach ($brand_data["products"] as $row)
                        <tr>
                            <td align="center">{{ $row->product_code }}</td>
                            <td>{{ $row->product_name }}</td>
                            <td align="center">{{ $row->unit_name }}</td>
                            <td align="right">{{ number_format($row->quantity, 2, '.', '') }}</td>
                            <td align="right">{{ number_format($row->amount, 2, '.', '') }}</td>
                        </tr>
                    @endforeach
                    @foreach ($brand_data["units"] as $unit)
                        <tr>
                            <td colspan="2" style="text-align: right; font-weight: bold;">TOTAL {{ $brand_item->brand_name }}</td>
                            <td align="center" style="font-weight: bold;">{{ $unit->unit_name }}</td>
                            <td align="right" style="font-weight: bold;">{{ number_format($unit->quantity, 2, '.', '') }}</td>
                            <td align="right" style="font-weight: bold;">{{ number_format($unit->amount, 2, '.', '') }}</td>
                        </tr>
                    @endforeach
                @endforeach
                @php $general_amount = 0; @endphp
                @foreach ($totals as $unit)
                    @php $general_amount += $unit->amount; @endphp
                    <tr>
                        <th colspan="2" style="text-align: right;">TOTAL GENERAL</th>
                        <th>{{ $unit->unit_name }}</th>
                        <th style="text-align: right;">{{ number_format($unit->quantity, 2, '.', '') }}</th>
                        <th style="text-align: right;">{{ number_format($unit->amount, 2, '.', '') }}</th>
                    </tr>
                @endforeach
                <tr>
                    <th colspan="4" style="text-align: right;">IMPORTE TOTAL (S/)</th>
                    <th style="text-align: right;">{{ number_format($general_amount, 2, '.', '') }}</th>
                </tr>
            </tbody>
        </table>
    @endif
</body>
</html>
