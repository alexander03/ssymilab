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
        .page-break {
          page-break-after: always;
        }
        @page { sheet-size: A4-L; }
    </style>
</head>
<body>
            @php
                $init = 0;
                $total = 0;
                $pagado = 0;
                $vendedor_id = 0;
            @endphp
            @if(count($lista)>0)
            @foreach ($lista as $key => $item)
                @php
                    
                    if($vendedor_id!=$item->person_id){
                        if($vendedor_id!="0"){
                            echo "<tr><th colspan='4'>TOTAL</th><th>".number_format($total + $pagado,2,'.','')."</th>
                                    <th>".number_format($pagado,2,'.','')."</th>
                                    <th>".number_format($total,2,'.','')."</th>
                                </tr></tbody></table><br /><br /><br /><div class='page-break'></div>";
                            $total = 0;
                        }
                @endphp
                <pagebreak></pagebreak>
                <img src="http://137.184.71.147:86/public/assets/images/logo.jpeg" width="120">
                    <div>
                        <p style=" line-height: 1rem;font-weight: 700;text-align: center;margin-top: -2rem;">
                            REPORTE CUENTAS POR COBRAR VENDEDOR
                        </p>
                        <h2 style="text-align: center;">
                            {{$item->person_name}}
                        </h2>
                    </div>

                    <table border="1"  style=" width: 100%; border-collapse: collapse; margin-top: 10px;margin-bottom: 10px; padding:5px" >
                        <thead>
                            <tr>
                                <th> Nº </th>
                                <th> Fecha</th>
                                <th> Nro. Doc. </th>
                                <th> Cliente </th>
                                <th> Total S/</th>
                                <th width="50px;"> A Cta. S/</th>
                                <th width="50px;"> Saldo S/</th>
                            </tr>
                        </thead>
                        <tbody>
                @php
                        $vendedor_id = $item->person_id;
                        $init = 0;
                    }
                    $init = $init + 1;
                    $total = $total + $item->pending_amount;
                    $pagado = $pagado + $item->total - $item->pending_amount;
                    if(date("Y-m",strtotime($item->date_time))!=date("Y-m") && $item->total>700){
                        $color="yellow";
                    }elseif($item->total < 700){
                        $color = "orange";
                    }else{
                        $color = "";
                    }
                @endphp
                <tr style="background: {{ $color }}">
                    <td class="px-6 py-4 text-center uppercase text-sm text-gray-500">
                        {{ $init }}
                    </td>
                    <td class="px-6 py-4 text-center uppercase text-sm text-gray-500">
                        {{ date('d/m/Y',strtotime($item->date_time)) }}
                    </td>
                    <td class="px-6 py-4 text-center uppercase text-sm text-gray-500">
                        {{ $item->number }}
                    </td>
                    <td class="px-6 py-4 text-center uppercase text-sm text-gray-500">
                        {{ $item->supplier_name }}
                    </td>
                    <td style="text-align: center;">
                        {{ number_format($item->total,2,'.','') }}
                    </td>
                    <td style="text-align: center;">
                        {{ number_format($item->total - $item->pending_amount,2,'.','') }}
                    </td>
                    <td style="text-align: center;">
                        {{ number_format($item->pending_amount,2,'.','') }}
                    </td>
                </tr>
                                   
                @endforeach
            @endif
                <tr>
                    <th colspan="4">TOTAL</th>
                    <th>{{ number_format($total+$pagado,2,'.','') }}</th>
                    <th>{{ number_format($pagado,2,'.','') }}</th>
                    <th>{{ number_format($total,2,'.','') }}</th>
                </tr>
            </tr>
        </tbody>
    </table>

</body>
</html>
