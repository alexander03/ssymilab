@php
    use App\Librerias\Libreria;
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>FORMATO-ENVIO</title>
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
            margin-top: 11px;
            margin-left: 11px;
            padding: 50px;
            background-image: url({{ asset("assets/images/imagen5.png") }});
            background-repeat: no-repeat;
            background-size: 111%;
        }

        td {
            font-size: 11px;
            padding: 4px;
        }

        th {
            font-size: 11px;
            padding: 4px;
        }
        table{
            border-spacing: 0px;
            border-collapse: collapse;  /* <--- add this so all the internal <td>s share adjacent borders  */
            border: 0px solid black;
        }
    </style>
</head>
<body>
    <div>
        <table class="margin-5"   style=" border-collapse: collapse; margin-top: 10px;margin-bottom: 10px;" >
            <thead>
                <tr>
                    <th style="text-align: left;"> REMITE: </th>
                    <td style="text-align: left;"> UNIMEDILAB S.A.C.</td>
                </tr>
                <tr>
                    <th style="text-align: left;"> RUC: </th>
                    <td style="text-align: left;"> 20538910717</td>
                </tr>
                <tr>
                    <th style="text-align: left;"> TELEFONO: </th>
                    <td style="text-align: left;"> 074-225124 / 967305273</td>
                </tr>
                <tr>
                    <th style="text-align: left;"> DIRECCION: </th>
                    <td style="text-align: left;"> Calle Silva Salgado N° 145 Urb. Arturo Cabrejos falla - Chiclayo</td>
                </tr>
            </thead>
        </table>
        <br />
        <!--img src="http://137.184.71.147:81/public/assets/images/logo-pequeno.png" width="120" style="padding-left: 650px;"-->
        <table class="margin-5"   style="border-collapse: collapse; margin-top: -120px;margin-bottom: 10px;width: 90%"  >
            <thead>
                <tr>
                    <th style="text-align: right;"> <label style="font-weight: 700;">ATENCIÓN:</label> </th>
                </tr>
                <tr>
                    <td style="text-align: right;"><label style="font-weight: 700;">CLIENTE:</label> {{ $data->shipping_person }}</td>
                </tr>
                <tr>
                    <td style="text-align: right;"><label style="font-weight: 700;">DNI: </label> {{ $data->shipping_person_dni }}</td>
                </tr>
                <tr>
                    <td style="text-align: right;"><label style="font-weight: 700;">TELEFONO: </label> {{ $data->phone }}</td>
                </tr>
                <tr>
                    <td style="text-align: right;"><label style="font-weight: 700;">DIRECCION: </label> {{ $data->address }}</td>
                </tr>
            </thead>
        </table>
    </div>
</body>
</html>
