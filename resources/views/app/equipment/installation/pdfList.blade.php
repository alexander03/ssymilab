@php
    use App\Librerias\Libreria;
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>LISTADO DE INSTALACIONES</title>
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
            margin-top: 10px;
            margin-left: 10px;
            padding: 50px;
            background-image: url({{ asset("assets/images/imagen5.png") }});
            background-repeat: no-repeat;
            background-size: 111%;
        }

        td {
            font-size: 9px;
            padding: 4px;
        }

        th {
            font-size: 10px;
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
    <img src="http://137.184.71.147:81/public/assets/images/unimedilab-logo.png" width="120">
    <div>
        <p class="margin-10" style="font-size: 1rem; line-height: 1rem;font-weight: 700;text-align: center;margin-top: -2rem;">
            LISTADO DE INSTALACIONES
        </p>
    </div>
    <div>
        <table class="margin-5"  border="1"  style=" width: 100%; border-collapse: collapse; margin-top: 10px;margin-bottom: 10px;" >
            <thead>
                <tr>
                    <th > FECHA </th>
                    <th > PRODUCTO </th>
                    <th > MODELO </th>
                    <th > MARCA </th>
                    <th > SERIE </th>
                    <th > CLIENTE </th>
                    <th > LUGAR </th>
                    <th > UBICACION </th>
                    <th > ESTADO </th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $cont = 0;
                ?>
                @foreach ($list as $item)
                    @php
                        $product = \App\Models\Product::where('id', $item->product_id)->withTrashed()->first();
                    @endphp
                    <tr>
                        <td class="px-6 py-4 text-center uppercase text-sm text-gray-500">
                            {{ date("d/m/Y",strtotime($item->installed_date)) }}
                        </td>
                        <td class="px-6 py-4 text-center uppercase text-sm text-gray-500">
                            {{ $product->name}}
                        </td>
                        <td class="px-6 py-4 text-center uppercase text-sm text-gray-500">
                            {{$product->model }}
                        </td>
                        <td class="px-6 py-4 text-center uppercase text-sm text-gray-500">
                            {{ ($product->brand) ? $product->brand->name : "[ELIMINADO]" }}
                        </td>
                        <td class="px-6 py-4 text-center uppercase text-sm text-gray-500">
                            {{ $item->product_serie->serie }}
                        </td>
                        <td class="px-6 py-4 text-center uppercase text-sm text-gray-500">
                            {{ $item->customer_name}}
                        </td>
                        <td class="px-6 py-4 text-center uppercase text-sm text-gray-500">
                            {{ $item->product_place}}
                        </td>
                        <td class="px-6 py-4 text-center uppercase text-sm text-gray-500">
                            {{ $item->product_location}}
                        </td>
                        <td class="px-6 py-4 text-center uppercase text-sm text-gray-500">
                            {{ $item->status=='C'?'CESION DE USO':($item->status=='V'?'VENDIDO':'PRESTAMO') }}
                        </td>
                    </tr>
                @endforeach
                
            </tbody>
        </table>
    </div>
</body>
</html>
