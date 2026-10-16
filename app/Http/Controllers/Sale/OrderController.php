<?php

namespace App\Http\Controllers\Sale;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Barryvdh\DomPDF\Facade as PDF;
use Elibyy\TCPDF\Facades\TCPDF;
use App\Librerias\Libreria;

class OrderController extends Controller
{
    protected $directory = 'app.sale.order';
    protected $title_admin = 'Pedidos';
    protected $title_show = 'Ver pedido';
    protected $title_create = 'Crear pedido';
    protected $title_edit = 'Editar pedido';
    protected $category_menu = 'Ventas';
    protected $option_menu = 'Pedido';
    protected $option_create = 'Crear pedido';
    protected $option_show = 'Ver pedido';
    protected $option_edit = 'Editar pedido';

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $title_admin = $this->title_admin;
        $category_menu = $this->category_menu;
        $option_menu = $this->option_menu;

        return view("{$this->directory}.admin")->with(compact('title_admin', 'category_menu', 'option_menu'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $title_create = $this->title_create;
        $category_menu = $this->category_menu;
        $option_menu = $this->option_menu;
        $option_create = $this->option_create;

        $order = new Order();

        return view("{$this->directory}.create")->with(compact('order', 'title_create', 'category_menu', 'option_menu', 'option_create'));
    }

    /**
     * Show a resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show($order_id)
    {
        $title_show = $this->title_show;
        $category_menu = $this->category_menu;
        $option_menu = $this->option_menu;
        $option_show = $this->option_show;

        try {
            $order = Order::findOrFail($order_id);
        } catch (\Exception $e) {
            return redirect()->route('order');
        }

        return view("{$this->directory}.show")->with(compact('order', 'title_show', 'category_menu', 'option_menu', 'option_show'));
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $order
     * @return \Illuminate\Http\Response
     */
    public function edit($order_id)
    {
        $title_edit = $this->title_edit;
        $category_menu = $this->category_menu;
        $option_menu = $this->option_menu;
        $option_edit = $this->option_edit;

        try {
            $order = Order::findOrFail($order_id);
            if($order->status == "Y" || $order->status == "A"){
                Session::flash('warning', 'El pedido se encuentra con factura o anulado!');
                return redirect()->route('order');
            }
        } catch (\Exception $e) {
            return redirect()->route('order');
        }

        return view("{$this->directory}.edit")->with(compact('order', 'title_edit', 'category_menu', 'option_menu', 'option_edit'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $proforma
     * @return \Illuminate\Http\Response
     */
    public function pdf($order_id)
    {
        try {
            $proforma = Order::findOrFail($order_id);
        } catch (\Exception $e) {
            return redirect()->route('proforma');
        }
        $pdf = new TCPDF();
        $pdf::AddPage('P');
        $borde = 0;
        $pdf::SetXY(120, 15);
        $pdf::SetFont('helvetica','B',10);
        $alto = 8;
        $pdf::Cell(70, $alto, utf8_decode("RUC: ".$proforma->branch_office->company->ruc), $borde, 0, "C");
        $pdf::SetXY(120, $pdf::GetY() + 8);
        $pdf::SetFont('helvetica','B',10);
        //$pdf::SetTextColor(75,119,227);
        $pdf::SetFillColor(0,111,173);
        $pdf::SetTextColor(255,255,255);
        $alto = 12;
        $pdf::MultiCell(70,$alto,utf8_decode("\nPEDIDO"),$borde,'C',true);
        $pdf::SetTextColor(0);
        if(substr($proforma->number,1,3)=="001"){
            $pdf::Image(__DIR__."/../../../../clifacturacion/controlador/logo.jpeg",15,1,104,40);
        }else{
            $pdf::Image(__DIR__."/../../../../clifacturacion/controlador/logo.jpeg",15,1,104,40);
        }
        
        $pdf::SetFont('helvetica','',10);
        $pdf::SetXY(120, $pdf::GetY());
        $pdf::Cell(70, $alto, utf8_decode("Nro. ".$proforma->number), $borde, 0, "C");
        //$pdf::Rect(140, 12, 58, 25,'F');
        $pdf::RoundedRect(120, 12, 70, 35,2,'1234');
        
        $pdf::SetXY(15, 28);
        $pdf::SetFont('helvetica','B',11);
        $alto = 4;
        $pdf::Cell(100, $alto, utf8_decode($proforma->branch_office->company->business_name),$borde, 0, "C");
        $pdf::SetTextColor(0,0,0);
        $pdf::SetXY(25, $pdf::GetY()+6);
        $pdf::SetFont('helvetica','',8);
        $alto = 4;
        $pdf::MultiCell(90, $alto, utf8_decode($proforma->branch_office->address), $borde, "C");
        
        /*$pdf::SetXY(15, $pdf::GetY());
        $pdf::SetFont('helvetica','',8);
        $pdf::Cell(100, $alto, "Telf: 074 225124", $borde, 0, "C");
        $pdf::SetXY(25, $pdf::GetY()+$alto);
        $pdf::SetFont('helvetica','',8);
        $pdf::Multicell(80, $alto, "Email: ventas@unimedilab.com.pe - ventasunimedilab@hotmail.com", $borde, "C");
        $pdf::SetXY(15, $pdf::GetY()+$alto+1);*/
        $pdf::SetXY(15, 54);

        $details = ($proforma->detail && trim($proforma->detail) != "") ? json_decode($proforma->detail, true) : [];
        $alto = 5;
        $pdf::SetFont('helvetica','B',8);
        $pdf::Cell(32, $alto, utf8_decode("Fecha Emision:"), 'R', 0, "C");
        $pdf::Cell(30, $alto, utf8_decode("Cod Usuario:"), 'R', 0, "C");
        $pdf::Cell(87, $alto, utf8_decode("Comentario:"), 'R', 0, "C");
        $pdf::Cell(35, $alto, utf8_decode("Moneda"), 0, 0, "C");
        $pdf::SetXY(15, $pdf::GetY()+$alto);

        $pdf::SetFont('helvetica','',8);
        $pdf::Cell(32, $alto, date("d/m/Y",strtotime($proforma->date_time)),'R', 0, "C");
        $pdf::Cell(30, $alto, $proforma->user->person->code, 'R', 0, "C");
        $pdf::Cell(87, $alto, (array_key_exists('comment', $details)) ? $details["comment"] : "", 'R', 0, "C");
        $pdf::Cell(35, $alto, $proforma->currency->name, 0, 0, "C");

        $pdf::RoundedRect(15, 54, 185, 10,2,'1234');

        $pdf::SetXY(15, $pdf::GetY()+$alto*2);
        
        $pdf::SetFont('helvetica','B',9);
        $pdf::Cell(27, $alto, utf8_decode("RUC"), 'B', 0, "L");
        $pdf::Cell(3, $alto, utf8_decode(":"), 'B', 0, "L");
        $pdf::SetFont('helvetica','',9);
        $pdf::Cell(155, $alto, utf8_decode($proforma->client->getDocumentNumber()), 'B', 0, "L");

        $pdf::SetXY(15, $pdf::GetY()+$alto);
        $pdf::SetFont('helvetica','B',9);
        $pdf::Cell(27, $alto, ("Señor(es)"), 'B', 0, "L");
        $pdf::Cell(3, $alto, (":"), 'B', 0, "L");
        $pdf::SetFont('helvetica','',9);
        $alto1 = $pdf::getNumLines($proforma->client->getNameComplete(), 155);
        $pdf::MultiCell(155, $alto, ($proforma->client->getNameComplete()), 'B', "L");
        
        $pdf::SetXY(15, $pdf::GetY());

        $pdf::SetFont('helvetica','B',9);
        $pdf::Cell(27, $alto, ("Dirección"), $borde, 0, "L");
        $pdf::Cell(3, $alto, utf8_decode(":"), $borde, 0, "L");
        $pdf::SetFont('helvetica','',8);
        $alto2 = $pdf::getNumLines(($proforma->client->address),140);
        $pdf::MultiCell(140, $alto, ($proforma->client->address), $borde,"L");

        $pdf::SetXY(15, $pdf::GetY());
        $pdf::RoundedRect(15, 69, 185, 15,2,'1234');

        $pdf::SetXY(15, $pdf::GetY()+$alto);

        /*$pdf::SetFont('helvetica','B',9);
        $pdf::Cell(40, $alto, ("Estimados Señores:"), $borde, 0, "L");
        $pdf::SetXY(15, $pdf::GetY()+$alto);        

        $pdf::SetFont('helvetica','',9);
        $pdf::Cell(40, $alto, ("Por intermedio de la presente le hacemos llegar nuestra cotización, en referencia a su solicitud."), $borde, 0, "L");
        $pdf::SetXY(15, $pdf::GetY()+$alto);*/

        $productosRectY = $pdf::GetY();
        $pdf::SetDrawColor(255, 255, 255);
        $pdf::SetFillColor(0,111,173);
        $pdf::SetTextColor(255,255,255);
        $pdf::SetFont('helvetica','B',8);
        $alto=8;
        $agregar = 15;
        if($proforma->details[0]->product->type=='R'){
            $pdf::Cell(15, $alto, utf8_decode("CODIGO"), 1, 0, "C", true);
            $pdf::Cell(86, $alto, utf8_decode("DESCRIPCION"), 1, 0, "C", true);
            $pdf::Cell(15, $alto, utf8_decode("F.V."), 1, 0, "C", true);
        }else{
            $pdf::Cell(20, $alto, utf8_decode("CODIGO"), 1, 0, "C", true);
            $pdf::Cell(96, $alto, utf8_decode("DESCRIPCION"), 1, 0, "C", true);
        }
        $pdf::Cell(15, $alto, utf8_decode("CANT."), 1, 0, "C", true);
        $pdf::Cell(17, $alto, utf8_decode("U.M."), 1, 0, "C", true);
        $y = $pdf::GetY();
        $pdf::Multicell(17, $alto, utf8_decode("PRECIO UNITARIO"), 1, "C", true);
        $pdf::SetXY(165+$agregar,$y);
        $y = $pdf::GetY();
        $pdf::Multicell(20, $alto, utf8_decode("PRECIO TOTAL"), 1, "C", true);
        $pdf::SetXY(182+$agregar,$y);
        $pdf::SetFont('helvetica','',8);
        $pdf::Ln();
        $pdf::SetDrawColor(0, 0, 0);
        $total = 0;
        $alto = 5;
        $pdf::SetTextColor(0,0,0);
        $pdf::SetFillColor(255, 255, 255);
        $borde2 = 0;
        $productosRectHeight = 0;
        foreach ($proforma->details as $key => $item){
            $details = ($item->detail && trim($item->detail) != "") ? json_decode($item->detail, true) : [];
            $fv = (array_key_exists('due_date', $details)) ? $details["due_date"] : "";
            $plazo = (array_key_exists('delivery_time', $details)) ? $details["delivery_time"] : "";

            $alto2 = $pdf::getNumLines(($item->product->name.' '.$item->product->presentation."\n MARCA: ".$item->product->brand->name) , 66);
            $pdf::SetXY(15, $pdf::GetY());
            if($proforma->details[0]->product->type=='R'){
                $pdf::Cell(15, $alto2, $item->product->code, $borde2, 0, "C");
                $y=$pdf::GetY();
                $pdf::MultiCell(86, $alto, $item->product->name.' '.$item->product->presentation."\n MARCA: ".$item->product->brand->name, $borde2, "L");
                $pdf::SetXY(116,$y);
                $pdf::Cell(15, $alto2, $fv, $borde2, 0, "C");
            }else{
                $pdf::Cell(20, $alto2, $item->product->code, $borde2, 0, "C");
                $y=$pdf::GetY();
                $pdf::MultiCell(96, $alto, $item->product->name.' '.$item->product->presentation."\n MARCA: ".$item->product->brand->name, $borde2, "L");
                $pdf::SetXY(131,$y);
            }
            $pdf::Cell(15, $alto2, sprintf('%g', $item->quantity), $borde2, 0, "C");
            $pdf::Cell(17, $alto2, $item->unit->name, $borde2, 0, "C");
            $pdf::Cell(17, $alto2, number_format($item->sell_price,2), $borde2, 0, "C");
            $pdf::Cell(20, $alto2, $proforma->currency->symbol .' '. number_format($item->sell_price * $item->quantity,2), $borde2, 0, "C");
            $pdf::SetXY(15, $pdf::GetY()+($alto2-0.4)*5);
            $productosRectHeight += ($alto2-0.4)*5;
        }
        $pdf::SetXY(15, $pdf::GetY()+$alto-2);
        $pdf::SetFont('helvetica','B',9);
        $pdf::Cell(165, $alto, "TOTAL", 'TR', 0, "R");
        $pdf::Cell(20, $alto, $proforma->currency->symbol . ' ' . number_format($proforma->total,2), 'T', 0, "R");

        $pdf::RoundedRect(15, $productosRectY, 185, $productosRectHeight + $alto*4-4,2,'0110');

        $pdf::SetXY(15, $pdf::GetY()+$alto*2);

        $pdf::SetFont('helvetica','B',9);
        $y=$pdf::GetY();
        $pdf::Multicell(145, $alto, "Son: ".Libreria::convertir($proforma->total), 0, "L");
        $pdf::SetXY(160,$y);
        $pdf::Cell(40, $alto, "(PRECIOS INCLUYEN IGV)", 0, 0, "C");

        if($proforma->comment()!=""){
            $pdf::SetXY(15, $pdf::GetY()+$alto*2);
            $pdf::Cell(42, $alto, "CONDICIONES DE VENTA:", 0, 0, "R");
            $y=$pdf::GetY();
            $pdf::SetFont('helvetica','',9);
            $altura = $pdf::getNumLines(str_replace(array("<p>","&nbsp;","</p>"),array("","\n",""),$proforma->comment()),145);
            $pdf::Multicell(145, $alto, str_replace(array("<p>","&nbsp;","</p>"),array("","\n",""),$proforma->comment()), 0, "L");
            $pdf::Ln($altura/3);
        }

        $pdf::SetXY(15, $pdf::GetY()+$alto*2);

        $borde=1;
        $pdf::SetFont('helvetica','',7);
        $pdf::Cell(185,$alto,"USTED PUEDE HACER PAGOS DIRECTAMENTE EN NUESTRAS CUENTAS EN LOS SIGUIENTES BANCOS",1,0,'L');
        $pdf::Ln();
        $pdf::SetFont('helvetica','',7);
        $pdf::Cell(5, $alto, "", 0, 0, "L");
        $pdf::Cell(45, $alto, "BANCO: BBVA", "LT", 0, "L");
        $pdf::Cell(45, $alto, "BANCO: BBVA", "LT", 0, "L");
        $pdf::Cell(45, $alto, "BANCO: BCP", "LT", 0, "L");
        $pdf::Cell(50, $alto, "BANCO: YAPE", "RLT", 0, "L");
        $pdf::Ln();
        $pdf::SetFont('helvetica','',7);
        $pdf::Cell(5, $alto, "", 0, 0, "L");
        $pdf::Cell(45, $alto, "TITULAR: SSYMILAB E.I.R.L.", "L", 0, "L");
        $pdf::Cell(45, $alto, "TITULAR: SSYMILAB E.I.R.L.", "L", 0, "L");
        $pdf::Cell(45, $alto, "TITULAR: SSYMILAB E.I.R.L.", "L", 0, "L");
        $pdf::Cell(50, $alto, "TITULAR: SSYMILAB E.I.R.L.", "RL", 0, "L");
        $pdf::Ln();
        $pdf::SetFont('helvetica','',7);
        $pdf::Cell(5, $alto, "", 0, 0, "L");
        $y = $pdf::GetY();
        $x = $pdf::GetX();
        $pdf::MultiCell(45, $alto, "NRO CUENTA (SOLES): 0011-0200-0100154444", "L",  "L");
        $pdf::SetXY($x+45,$y);
        $y = $pdf::GetY();
        $x = $pdf::GetX();
        $pdf::MultiCell(45, $alto, "NRO CUENTA (DOLARES): 0011-0200-0100157559", "L", "L");
        $pdf::SetXY($x+45,$y);
        $y = $pdf::GetY();
        $x = $pdf::GetX();
        $pdf::MultiCell(45, $alto, "NRO CUENTA (SOLES): 285-9877952-0-77", "L", "L");
        $pdf::SetXY($x+45,$y);
        $y = $pdf::GetY();
        $x = $pdf::GetX();
        $pdf::MultiCell(50, $alto, "972558640", "RL", "L");
        $pdf::SetXY($x+45,$y);

        $pdf::Ln($alto+1);
        $pdf::SetFont('helvetica','',7);
        $pdf::Cell(5, $alto, "", 0, 0, "L");
        $y = $pdf::GetY();
        $x = $pdf::GetX();
        $pdf::MultiCell(45, $alto, "CCI: 011-200-000100154444-32", "LB", "L");
        $pdf::SetXY($x+45,$y);
        $y = $pdf::GetY();
        $x = $pdf::GetX();
        $pdf::MultiCell(45, $alto, "CCI: 011-200-000100157559-34", "LB", "L");
        $pdf::SetXY($x+45,$y);
        $y = $pdf::GetY();
        $x = $pdf::GetX();
        $pdf::MultiCell(45, $alto, "CCI: 002-285-009877952077-58", "LB", "L");
        $pdf::SetXY($x+45,$y);
        $y = $pdf::GetY();
        $x = $pdf::GetX();
        $pdf::MultiCell(50, $alto, "                               ", "LRB", "L");
        $pdf::Ln();

        $altura = $pdf::GetY();
        $pdf::SetFont('helvetica','B',9);
        $pdf::Cell(5, $alto, "", 0, 0, "L");
        $pdf::Cell(70, $alto, "SSYMILAB E.I.R.L.", 0, 0, "L");
        $pdf::SetXY(15, $pdf::GetY()+$alto);
        $pdf::Cell(70, $alto, "20607558168 ", 0, 0, "L");
        $pdf::SetXY(15, $pdf::GetY()+$alto);

        $pdf::Cell(70, $alto, "Departamento de Ventas y Licitaciones ", 0, 0, "L");
        $pdf::SetXY(15, $pdf::GetY()+$alto);

        if(substr($proforma->number,1,3)=="001"){
            $pdf::Cell(70, $alto, "E-mail: ventas@ssymilabperu.com", 0, 0, "L");
            $pdf::SetXY(15, $pdf::GetY()+$alto);

            $pdf::Cell(70, $alto, "Celular: 972558640 ", 0, 0, "L");
        }else{
            $pdf::Cell(70, $alto, "E-mail: ventas@ssymilabperu.com ", 0, 0, "L");
            $pdf::SetXY(15, $pdf::GetY()+$alto);

            $pdf::Cell(70, $alto, "Celular: 972558640 ", 0, 0, "L");
        }

        $pdf::Image(__DIR__."/../../../../clifacturacion/controlador/firma.jpeg",85,$altura,50,30);
        $pdf::Output('Pedido.pdf');
        //$pdf = PDF::loadView("{$this->directory}.pdf", compact('proforma'))->setPaper('A4', 'Portrait');
        //return $pdf::stream("proforma-{$proforma->number}.pdf");
    }
}
