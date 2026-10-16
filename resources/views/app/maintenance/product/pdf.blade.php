@php
    use App\Librerias\Libreria;
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>LISTADO DE PRODUCTOS</title>
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
            font-size: 10px;
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
    <img src="../public/assets/images/logo.jpeg" width="120">
    <div>
        <p class="margin-10" style="font-size: 1rem; line-height: 1rem;font-weight: 700;text-align: center;margin-top: -2rem;">
            LISTADO DE PRODUCTOS
        </p>
    </div>
    <div>
        <table class="margin-5"  border="1"  style=" width: 100%; border-collapse: collapse; margin-top: 10px;margin-bottom: 10px;" >
            <thead>
                <tr>
                    <th > CODIGO </th>
                    <th > NOMBRE </th>
                    <th > CATEGORIA </th>
                    <th > DISTRIBUIDOR </th>
                    <th > MARCA </th>
                    <th > TIPO </th>
                    <th > ESTADO </th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $cont = 0;
                ?>
                @foreach ($lista as $item)
                    <tr>
                        <td align="center"> {{ $item->code }} </td>
                        <td align="center"> {{ $item->name." ".$item->model }} </td>
                        <td align="center"> {{ ($item->category) ? $item->category->name : "[ELIMINADO]" }} </td>
                        <td align="center"> {{ ($item->supplier) ? $item->supplier->getNameComplete() : "[ELIMINADO]" }} </td>
                        <td align="center"> {{ ($item->brand) ? $item->brand->name : "[ELIMINADO]" }} </td>
                        <td align="right"> {{ ($item->type == "R" ? "REACTIVOS" : ($item->type == "M" ? "MAQUINARIA" : ($item->type == "S" ? "SERVICIO" : "REPUESTOS"))) }} </td>
                        <td align="right"> {{ ($item->status == "Y" ? "HABILITADO" : "DESHABILITADO") }} </td>
                    </tr>
                @endforeach
                
            </tbody>
        </table>
    </div>
</body>
</html>
