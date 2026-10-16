<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>REPORTE PROXIMOS MANTENIMIENTOS</title>
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
    <img src="http://137.184.71.147:81/public/assets/images/unimedilab-logo.png" width="120">
    <div>
        <p style=" line-height: 1rem;font-weight: 700;text-align: center;margin-top: -4rem;">
            PROXIMOS MANTENIMIENTOS
        </p>
    </div>

    <table border="1"  style=" width: 100%; border-collapse: collapse; margin-top: 10px;margin-bottom: 10px; padding:5px" >
        <thead>
            <tr>
                <th> PRODUCTO </th>
                <th> SERIE </th>
                <th> CLIENTE </th>
                <th> LUGAR</th>
                <th> UBICACION </th>
                <th> ESTADO </th>
                <th> ULTIMO MANT. </th>
                <th> PROX. MANT. </th>
            </tr>
        </thead>
        <tbody>
            @php
                                    $init = 0;
                    @endphp
            @foreach ($lista as $key => $item)
            @php
                                    $init = $init + 1;
                                @endphp
                                <tr>
                                    <td class="px-6 py-4 text-center uppercase text-sm text-gray-500">
                                        {{ $item->product_serie->product->name.' '.$item->product_serie->product->model }}
                                    </td>
                                    <td class="px-6 py-4 text-center uppercase text-sm text-gray-500">
                                        {{ $item->product_serie->serie }}
                                    </td>
                                    <td class="px-6 py-4 text-center uppercase text-sm text-gray-500">
                                        {{ $item->customer_name }}
                                    </td>
                                    <td class="px-6 py-4 text-center uppercase text-sm text-gray-500">
                                        {{ $item->product_place }}
                                    </td>
                                    <td class="px-6 py-4 text-center uppercase text-sm text-gray-500">
                                        {{ $item->product_location }}
                                    </td>
                                    <td class="px-6 py-4 text-center uppercase text-sm text-gray-500">
                                        {{ $item->status=='C'?'CESION DE USO':($item->status=='V'?'VENDIDO':'PRESTAMO') }}
                                    </td>
                                    <td class="px-6 py-4 text-center uppercase text-sm text-gray-500">
                                        <?php
                                        if($item->ultimomantenimiento!=""){
                                            echo date("d/m/Y",strtotime($item->ultimomantenimiento));
                                        }
                                        ?>
                                    </td>

                                    <td class="px-6 py-4 text-center uppercase text-sm text-gray-500">
                                        <?php
                                        if($item->ultimomantenimiento!=""){
                                            $fechainicial = strtotime("+".$item->period." month",strtotime($item->installed_date));
                                            while(strtotime($item->ultimomantenimiento)>$fechainicial){
                                                $fechainicial = strtotime("+".$item->period." month",$fechainicial);
                                            }
                                            if(date("Y-m",strtotime($item->ultimomantenimiento))==date("Y-m",$fechainicial)){
                                                $fechainicial = strtotime("+".$item->period." month",$fechainicial);
                                            }
                                            echo date("d/m/Y",$fechainicial);
                                        }else{
                                            $fechainicial = strtotime("+".$item->period." month",strtotime($item->installed_date));
                                            echo date("d/m/Y",$fechainicial);
                                        }
                                        ?>
                                    </td>
                                </tr>
                           
                @endforeach
            </tr>
        </tbody>
    </table>

</body>
</html>
