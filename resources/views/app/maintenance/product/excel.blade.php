<?php
header("Content-Type: application/vnd.ms-excel");

header("Content-Disposition: attachment; filename=reporteproductos.xls");
?>
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
    <p style="font-weight: 700; text-align: center;">
        REPORTE DE PRODUCTOS
    </p>

    <table border="1" style="width: 100%; border-collapse: collapse;">
        <thead>
            <tr>
                <th> CODIGO </th>
                <th> NOMBRE </th>
                <th> CATEGORIA </th>
                <th> DISTRIBUIDOR </th>
                <th> MARCA </th>
                <th> TIPO </th>
                <th> ESTADO </th>
                <th> UNIDAD </th>
                <th> MONEDA </th>
                <th> P. COMPRA </th>
                <th> P. VENTA </th>
                <th> % GANANCIA </th>
                <th> P. VENTA MIN. </th>
                <th> % GANANCIA MIN. </th>
            </tr>
        </thead>
        <tbody>
            @foreach ($lista as $item)
                @php
                    $units = $item->all_product_units;
                    //PRODUCTO SIN UNIDADES: UNA FILA SIN PRECIOS
                    if(count($units) == 0){
                        $units = [null];
                    }
                @endphp
                @foreach ($units as $unit)
                    @php
                        //% CALCULADO DESDE LOS PRECIOS: (VENTA - COMPRA) / COMPRA * 100
                        $purchase_price = $unit ? (float) $unit->purchase_price : 0;
                        $sell_price = $unit ? (float) $unit->sell_price : 0;
                        $sell_price_min = $unit ? (float) $unit->sell_price_min : 0;
                        $win = ($purchase_price > 0) ? ($sell_price - $purchase_price) / $purchase_price * 100 : null;
                        $win_min = ($purchase_price > 0 && $sell_price_min > 0) ? ($sell_price_min - $purchase_price) / $purchase_price * 100 : null;
                    @endphp
                    <tr>
                        <td align="center"> {{ $item->code }} </td>
                        <td> {{ $item->name." ".$item->model }} </td>
                        <td align="center"> {{ ($item->category) ? $item->category->name : "[ELIMINADO]" }} </td>
                        <td align="center"> {{ ($item->supplier) ? $item->supplier->getNameComplete() : "[ELIMINADO]" }} </td>
                        <td align="center"> {{ ($item->brand) ? $item->brand->name : "[ELIMINADO]" }} </td>
                        <td align="center"> {{ ($item->type == "R" ? "REACTIVOS" : ($item->type == "M" ? "MAQUINARIA" : ($item->type == "S" ? "SERVICIO" : "REPUESTOS"))) }} </td>
                        <td align="center"> {{ ($item->status == "Y" ? "HABILITADO" : "DESHABILITADO") }} </td>
                        <td align="center"> {{ $unit ? $unit->name : "-" }} </td>
                        <td align="center"> {{ ($unit && $unit->currency) ? $unit->currency->code : "-" }} </td>
                        <td align="right"> {{ $unit ? number_format($purchase_price, 2, '.', '') : "" }} </td>
                        <td align="right"> {{ $unit ? number_format($sell_price, 2, '.', '') : "" }} </td>
                        <td align="right"> {{ is_null($win) ? "" : number_format($win, 2, '.', '') }} </td>
                        <td align="right"> {{ ($unit && $sell_price_min > 0) ? number_format($sell_price_min, 2, '.', '') : "" }} </td>
                        <td align="right"> {{ is_null($win_min) ? "" : number_format($win_min, 2, '.', '') }} </td>
                    </tr>
                @endforeach
            @endforeach
        </tbody>
    </table>
</body>
</html>
