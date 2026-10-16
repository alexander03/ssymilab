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
            font-size: 12px;
            padding: 4px;
        }

        th {
            font-size: 12px;
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
        <table class="margin-5"   style=" border-collapse: collapse; margin-top: 10px;margin-bottom: 10px;border: 2px solid black" >
            <thead>
                <tr>
                    <td style="text-align: left;"> REMITE: </td>
                </tr>
                    <th style="text-align: left;font-size: 20px">SSYMILAB E.I.R.L.</th>
                </tr>
                <tr>
                    <td style="text-align: left;padding-left: 10px;"> - RUC: 20607558168</td>
                </tr>
                <tr>
                    <td style="text-align: left;padding-left: 10px;"> - AV. INFANCIA NRO. 538 CUSCO CUSCO WANCHAQ</td>
                </tr>
                <tr>
                    <td style="text-align: left;padding-left: 10px;"> - 972558640</td>
                </tr>
                <tr>
                    <td></td>
                </tr>
                <tr>
                    <td style="text-align: left;padding-left: 20px;"><label style="font-weight: 700;">CLIENTE:</label> {{ $data->getNameComplete() }}</td>
                </tr>
                <tr>
                    <td style="text-align: left;padding-left: 20px;"><label style="font-weight: 700;">ATENCION:</label> {{ $data->shipping_person }}</td>
                </tr>
                <tr>
                    <td style="text-align: left;padding-left: 20px;"><label style="font-weight: 700;">DNI: </label> {{ $data->shipping_person_dni }}</td>
                </tr>
                <tr>
                    <td style="text-align: left;padding-left: 20px;">- DESTINO: {{ $data->address }}</td>
                </tr>
                <tr>
                    <td>
                        <table style="padding-left: 400px;">
                            <tr>
                                <td style="margin-bottom:10px">TEMPERATURA REFRIGERADA</td>
                                <td style="border: 1px solid black;width: 20px;margin-bottom: 10px;"></td>
                            </tr>
                            <tr>
                                <td colspan="2"></td>
                            </tr>
                            <tr >
                                <td style="margin-bottom: 10px;">TEMPERATURA AMBIENTE</td>
                                <td style="border: 1px solid black;width: 20px;margin-bottom: 10px;"></td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </thead>
        </table>
        <img src="http://64.23.174.175/public/assets/images/logo.jpeg" width="180" style="padding-left: 440px;margin-top: -290px;">
        <table class="margin-10"  style="border: 2px solid black;padding-left:100px " >
            <tr>
                <th style="font-size: 100px;color:red;padding: 50px">FRAGIL</th>
            </tr>
        </table>
    </div>
</body>
</html>
