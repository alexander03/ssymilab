<?php

namespace App\Http\Controllers\Sale;

use App\Http\Controllers\Controller;
use App\Models\Proforma;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Barryvdh\DomPDF\Facade as PDF;
use Elibyy\TCPDF\Facades\TCPDF;
use App\Librerias\Libreria;

class ProformaController extends Controller
{
    protected $directory = 'app.sale.proforma';
    protected $title_admin = 'Proforma';
    protected $title_show = 'Ver proforma';
    protected $title_create = 'Crear proforma';
    protected $title_edit = 'Editar proforma';
    protected $category_menu = 'Ventas';
    protected $option_menu = 'Proforma';
    protected $option_create = 'Crear proforma';
    protected $option_show = 'Ver proforma';
    protected $option_edit = 'Editar proforma';

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

        $proforma = new Proforma();

        return view("{$this->directory}.create")->with(compact('proforma', 'title_create', 'category_menu', 'option_menu', 'option_create'));
    }

    /**
     * Show a resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show($proforma_id)
    {
        $title_show = $this->title_show;
        $category_menu = $this->category_menu;
        $option_menu = $this->option_menu;
        $option_show = $this->option_show;

        try {
            $proforma = Proforma::findOrFail($proforma_id);
        } catch (\Exception $e) {
            return redirect()->route('proforma');
        }

        return view("{$this->directory}.show")->with(compact('proforma', 'title_show', 'category_menu', 'option_menu', 'option_show'));
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $proforma
     * @return \Illuminate\Http\Response
     */
    public function edit($proforma_id)
    {
        $title_edit = $this->title_edit;
        $category_menu = $this->category_menu;
        $option_menu = $this->option_menu;
        $option_edit = $this->option_edit;

        try {
            $proforma = Proforma::findOrFail($proforma_id);
            if($proforma->status == "Y" || $proforma->status == "A"){
                Session::flash('warning', 'El requerimiento se encuentra con factura o anulado!');
                return redirect()->route('proforma');
            }
        } catch (\Exception $e) {
            return redirect()->route('proforma');
        }

        return view("{$this->directory}.edit")->with(compact('proforma', 'title_edit', 'category_menu', 'option_menu', 'option_edit'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $proforma
     * @return \Illuminate\Http\Response
     */
    public function pdf($proforma_id)
    {
        try {
            $proforma = Proforma::findOrFail($proforma_id);
        } catch (\Exception $e) {
            return redirect()->route('proforma');
        }
        $pdf = new TCPDF();
        $pdf::AddPage('P');
        $borde = 0;
        $pdf::SetXY(120, 10);
        $pdf::SetFont('helvetica','B',10);
        $alto = 8;
        $pdf::Cell(70, $alto, utf8_decode("RUC: ".$proforma->branch_office->company->ruc), $borde, 0, "C");
        $pdf::SetXY(120, $pdf::GetY() + 8);
        $pdf::SetFont('helvetica','B',10);
        $pdf::SetFillColor(41,50,131);
        $pdf::SetTextColor(255,255,255);
        $alto = 12;
        $pdf::MultiCell(70,$alto,utf8_decode("\nPROFORMA"),$borde,'C',true);
        $pdf::SetTextColor(0);
        if(substr($proforma->number,1,3)=="001"){
            $pdf::Image(__DIR__."/../../../../clifacturacion/controlador/logo.jpeg",15,1,104,40);
        }else{
            $pdf::Image(__DIR__."/../../../../clifacturacion/controlador/logo.jpeg",15,1,104,40);
        }
        //$pdf::Image(__DIR__."/../../../../public/assets/images/logo_unimedilab.jpg",45,8,40,20);
        $pdf::SetFont('helvetica','',10);
        $pdf::SetXY(120, $pdf::GetY());
        $pdf::Cell(70, $alto, utf8_decode("Nro. ".$proforma->number), $borde, 0, "C");
        //$pdf::Rect(140, 12, 58, 25,'F');
        $pdf::RoundedRect(120, 7, 70, 35,2,'1234');
        
        $pdf::SetXY(15, 47);
        /*$pdf::SetFont('helvetica','B',11);
        $alto = 4;
        $pdf::Cell(100, $alto, utf8_decode($proforma->branch_office->company->business_name),$borde, 0, "C");
        $pdf::SetTextColor(0,0,0);
        $pdf::SetXY(25, $pdf::GetY()+6);
        $pdf::SetFont('helvetica','',8);
        $alto = 4;
        $pdf::MultiCell(90, $alto, utf8_decode($proforma->branch_office->address), $borde, "C");
        
        $pdf::SetXY(15, $pdf::GetY());
        $pdf::SetFont('helvetica','',8);
        $pdf::Cell(100, $alto, "Telf: 074 225124", $borde, 0, "C");
        $pdf::SetXY(25, $pdf::GetY()+$alto);
        $pdf::SetFont('helvetica','',8);
        $pdf::Multicell(80, $alto, "Email: ventas@unimedilab.com.pe - ventasunimedilab@hotmail.com", $borde, "C");
        $pdf::SetXY(15, $pdf::GetY()+$alto+1);*/

        $details = ($proforma->detail && trim($proforma->detail) != "") ? json_decode($proforma->detail, true) : [];
        $alto = 5;
        $pdf::SetFont('helvetica','B',8);
        $pdf::Cell(32, $alto, utf8_decode("Fecha Emision:"), 'R', 0, "C");
        $pdf::Cell(30, $alto, utf8_decode("Cod Usuario:"), 'R', 0, "C");
        $pdf::Cell(32, $alto, utf8_decode("Validez Oferta:"), 'R', 0, "C");
        $pdf::Cell(55, $alto, utf8_decode("Cond. Pago"), 'R', 0, "C");
        $pdf::Cell(35, $alto, utf8_decode("Moneda"), 0, 0, "C");
        $pdf::SetXY(15, $pdf::GetY()+$alto);

        $pdf::SetFont('helvetica','',8);
        $pdf::Cell(32, $alto, date("d/m/Y",strtotime($proforma->date_time)),'R', 0, "C");
        $pdf::Cell(30, $alto, $proforma->user->person->code, 'R', 0, "C");
        $pdf::Cell(32, $alto, (array_key_exists('validez', $details)) ? $details["validez"] : "", 'R', 0, "C");
        $pdf::Cell(55, $alto, (array_key_exists('condicion', $details)) ? $details["condicion"] : "", 'R', 0, "C");
        $pdf::Cell(35, $alto, $proforma->currency->name, 0, 0, "C");

        $pdf::RoundedRect(15, 47, 185, 10,2,'1234');

        $pdf::SetXY(15, $pdf::GetY()+$alto*2);
        $yinicial=$pdf::GetY();
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
        $alto2 = $pdf::getNumLines(($proforma->client->address),155);
        $pdf::MultiCell(155, $alto, ($proforma->client->address), $borde,"L");

        $pdf::SetXY(15, $pdf::GetY());
        $yfinal=$pdf::GetY();

        $pdf::RoundedRect(15, 62, 185, $yfinal-$yinicial,2,'1234');

        $pdf::SetXY(15, $pdf::GetY()+$alto);

        $pdf::SetFont('helvetica','B',9);
        $pdf::Cell(40, $alto, ("Estimados Señores:"), $borde, 0, "L");
        $pdf::SetXY(15, $pdf::GetY()+$alto);        

        $pdf::SetFont('helvetica','',9);
        $pdf::Cell(40, $alto, ("Por intermedio de la presente le hacemos llegar nuestra cotización, en referencia a su solicitud."), $borde, 0, "L");
        $pdf::SetXY(15, $pdf::GetY()+$alto);

        $productosRectY = $pdf::GetY();
        $pdf::SetDrawColor(255, 255, 255);
        $pdf::SetFillColor(41,50,131);
        $pdf::SetTextColor(255,255,255);
        $pdf::SetFont('helvetica','B',8);
        $alto=8;
        $agregar = 15;
        //DESCUENTO POR ITEM: COLUMNA "DSCTO %" DE 12 MM QUE SE QUITA A LA DESCRIPCION
        $proforma_detail = ($proforma->detail && trim($proforma->detail) != "") ? json_decode($proforma->detail, true) : [];
        $item_discount = (array_key_exists('discount_type', $proforma_detail) && $proforma_detail["discount_type"] == "I");
        $dw = $item_discount ? 12 : 0;
        if(!is_null($proforma->details[0]->product) && $proforma->details[0]->product->type=='R'){
            $pdf::Cell(15, $alto, utf8_decode("CODIGO"), 1, 0, "C", true);
            $pdf::Cell(66-$dw, $alto, utf8_decode("DESCRIPCION"), 1, 0, "C", true);
            $pdf::Cell(15, $alto, utf8_decode("F.V."), 1, 0, "C", true);
        }else{
            $pdf::Cell(20, $alto, utf8_decode("CODIGO"), 1, 0, "C", true);
            $pdf::Cell(76-$dw, $alto, utf8_decode("DESCRIPCION"), 1, 0, "C", true);
        }
        $y = $pdf::GetY();
        $pdf::Multicell(20, $alto, utf8_decode("PLAZO DE ENTREGA"), 1, "C", true);
        $pdf::SetXY(116+$agregar-$dw,$y);
        $pdf::Cell(15, $alto, utf8_decode("CANT."), 1, 0, "C", true);
        $pdf::Cell(17, $alto, utf8_decode("U.M."), 1, 0, "C", true);
        $y = $pdf::GetY();
        $pdf::Multicell(17, $alto, utf8_decode("PRECIO UNITARIO"), 1, "C", true);
        $pdf::SetXY(165+$agregar-$dw,$y);
        if($item_discount){
            $pdf::Cell(12, $alto, utf8_decode("DSCTO %"), 1, 0, "C", true);
        }
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
        $productosRectHeight = 0;$total=0;
        foreach ($proforma->details as $key => $item){
            $details = ($item->detail && trim($item->detail) != "") ? json_decode($item->detail, true) : [];
            $fv = (array_key_exists('due_date', $details)) ? $details["due_date"] : "";
            $plazo = (array_key_exists('delivery_time', $details)) ? $details["delivery_time"] : "";
            if($pdf::GetY()>265){
                $pdf::RoundedRect(15, $productosRectY, 185, $productosRectHeight + $alto*4-4,2,'0110');
                $pdf::AddPage('P');
                $productosRectY = $pdf::GetY();
                $productosRectHeight = -7;
            }
            $percent = ($item_discount && array_key_exists('descuento', $details)) ? (float) $details["descuento"] : 0;
            $alto2 = $pdf::getNumLines(($item->product->name.' '.$item->product->presentation."\n MARCA: ".$item->product->brand->name) , 66-$dw);
            $pdf::SetXY(15, $pdf::GetY());
            if($proforma->details[0]->product->type=='R'){
                $pdf::Cell(15, $alto2, $item->product->code, $borde2, 0, "C");
                $y=$pdf::GetY();
                $pdf::MultiCell(66-$dw, $alto, $item->product->name.' '.$item->product->presentation."\n MARCA: ".$item->product->brand->name, $borde2, "L");
                $pdf::SetXY(96-$dw,$y);
                $pdf::Cell(15, $alto2, $fv, $borde2, 0, "C");
            }else{
                $pdf::Cell(20, $alto2, $item->product->code, $borde2, 0, "C");
                $y=$pdf::GetY();
                $pdf::MultiCell(76-$dw, $alto, $item->product->name.' '.$item->product->presentation."\n MARCA: ".$item->product->brand->name, $borde2, "L");
                $pdf::SetXY(111-$dw,$y);
            }
            $pdf::Cell(20, $alto2, $plazo, $borde2, 0, "C");
            $pdf::Cell(15, $alto2, sprintf('%g', $item->quantity), $borde2, 0, "C");
            $pdf::Cell(17, $alto2, $item->unit->name, $borde2, 0, "C");
            $pdf::Cell(17, $alto2, number_format($item->sell_price,2), $borde2, 0, "C");
            if($item_discount){
                $pdf::Cell(12, $alto2, sprintf('%g', $percent)."%", $borde2, 0, "C");
            }
            $pdf::Cell(20, $alto2, $proforma->currency->symbol .' '. number_format($item->sell_price * $item->quantity * (1 - $percent/100),2), $borde2, 0, "C");
            $pdf::SetXY(15, $pdf::GetY()+($alto2-0.4)*5);
            $productosRectHeight += ($alto2-0.4)*5;
            $total = $total + $item->sell_price * $item->quantity;
        }
        $pdf::SetXY(15, $pdf::GetY()+$alto-2);
        $pdf::SetFont('helvetica','B',9);
        //DESCUENTO GLOBAL: SUBTOTAL / DESCUENTO / TOTAL SOLO SI HAY DESCUENTO
        $discount = $proforma->discount ? (float) $proforma->discount : 0;
        if($discount > 0){
            $pdf::Cell(165, $alto, "SUBTOTAL", 'TR', 0, "R");
            $pdf::Cell(20, $alto, $proforma->currency->symbol . ' ' . number_format($total,2), 'T', 0, "R");
            $pdf::SetXY(15, $pdf::GetY()+$alto);
            $pdf::Cell(165, $alto, "DESCUENTO", 'R', 0, "R");
            $pdf::Cell(20, $alto, "- " . $proforma->currency->symbol . ' ' . number_format($discount,2), 0, 0, "R");
            $pdf::SetXY(15, $pdf::GetY()+$alto);
            $total = $total - $discount;
            $productosRectHeight += $alto*2;
        }
        $pdf::Cell(165, $alto, "TOTAL", ($discount > 0) ? 'R' : 'TR', 0, "R");
        $pdf::Cell(20, $alto, $proforma->currency->symbol . ' ' . number_format($total,2), ($discount > 0) ? 0 : 'T', 0, "R");

        $pdf::RoundedRect(15, $productosRectY, 185, $productosRectHeight + $alto*4-4,2,'0110');

        $pdf::SetXY(15, $pdf::GetY()+$alto*2);

        $pdf::SetFont('helvetica','B',9);
        $y=$pdf::GetY();
        $pdf::Multicell(145, $alto, "Son: ".Libreria::convertir($total)." Soles", 0, "L");
        $pdf::SetXY(160,$y);
        $pdf::Cell(40, $alto, "(PRECIOS INCLUYEN IGV)", 0, 0, "C");

        if($proforma->comment()!=""){
            $pdf::SetXY(15, $pdf::GetY()+$alto*1.5);
            $pdf::Cell(42, $alto, "CONDICIONES DE VENTA:", 0, 0, "R");
            $y=$pdf::GetY();
            $pdf::SetFont('helvetica','',9);
            $altura = $pdf::getNumLines(str_replace(array("<p>","&nbsp;","</p>","<ul><li>","</li><li>","</li></ul>"),array("","\n","","","\n","\n"),$proforma->comment()),145);
            $pdf::Multicell(145, $alto, str_replace(array("<p>","&nbsp;","</p>","<ul><li>","</li><li>","</li></ul>"),array("","\n","","","\n","\n"),$proforma->comment()), 0, "L");
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

        $pdf::SetTitle("Proforma ".$proforma->number);
        $pdf::Output('Proforma.pdf');
        //$pdf = PDF::loadView("{$this->directory}.pdf", compact('proforma'))->setPaper('A4', 'Portrait');
        //return $pdf::stream("proforma-{$proforma->number}.pdf");
    }
}
