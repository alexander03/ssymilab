<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>REPORTE DOCUMENTO DE ALMACEN</title>
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
    <div>
        <p style=" line-height: 1rem;font-weight: 700;text-align: center;margin-top: -2rem;font-size: 20px;">
            {{ $store_document->document_type->name }} {{ $store_document->number }}
        </p>
        <br />
    <div class="p-2">
        <p class="my-1 uppercase"> <span style="font-weight: 700;">FECHA: </span><span>{{ date_format(new DateTime($store_document->date_time), 'd/m/Y') }}</span>  </p>
        <p class="my-1 uppercase"> <span style="font-weight: 700;">USUARIO: </span><span>{{ $store_document->user->person->getNameComplete() }}</span>  </p>
        <p class="my-1 uppercase"> <span style="font-weight: 700;">COMENTARIO: </span><span>{{ $store_document->comment() }}</span>  </p>
    </div>
    <div class="my-2 rounded shadow overflow-auto">
        <table border="1"  style=" width: 100%; border-collapse: collapse; margin-top: 10px;margin-bottom: 10px; padding:5px">
            <thead class="bg-gray-200">
                <tr>
                    <th colspan="1" class="sticky top-0 py-2 w-7/12 text-center text-xs font-bold bg-blue-900 text-white uppercase tracking-wider">Código</th>
                    <th colspan="1" class="sticky top-0 py-2 w-7/12 text-center text-xs font-bold bg-blue-900 text-white uppercase tracking-wider">Descripción</th>
                    <th colspan="1" class="sticky top-0 py-2 w-2/12 text-center text-xs font-bold bg-blue-900 text-white uppercase tracking-wider">Und.</th>
                    <th colspan="1" class="sticky top-0 py-2 w-2/12 text-center text-xs font-bold bg-blue-900 text-white uppercase tracking-wider">Cant.</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach ($store_document->details as $key => $item)
                    <tr>
                        <td class="px-4 py-2 text-center text-sm text-gray-500"> {{ $item->product->code }} </td>
                        <td class="px-4 py-2 text-left text-sm text-gray-500"> {{ $item->product->name.' '.$item->product->presentation.' '.$item->product->model.' '.$item->product->brand->name }} 
                            @if(count($item->product_moves)>0)
                            <br />
                            @endif
                            @foreach ($item->product_moves as $product_move)
                                @if ($product_move->product->type == "M" || $product_move->product->type == "E" || $product_move->product->type == "C")
                                    Serie: {{ $product_move->product_serie->serie }} <br />
                                @endif
                                @if ($product_move->product->type == "R")
                                    Lote: {{ $product_move->lot->lot }}  |  F. V.: {{ date("d/m/Y",strtotime($product_move->lot->due_date)) }}  |  Cantidad: {{ sprintf('%g', $product_move->quantity) }} <br />
                                @endif
                            @endforeach
                        </td>
                        <td class="px-4 py-2 text-center text-sm text-gray-500"> {{ $item->unit->name }} </td>
                        <td class="px-4 py-2 text-center text-sm text-gray-500"> {{ round($item->quantity,0) }} </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</body>
</html>
