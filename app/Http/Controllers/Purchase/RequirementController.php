<?php

namespace App\Http\Controllers\Purchase;

use App\Http\Controllers\Controller;
use App\Models\PurchaseRequirement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Barryvdh\DomPDF\Facade as PDF;
use Elibyy\TCPDF\Facades\TCPDF;
use App\Librerias\Libreria;


class RequirementController extends Controller
{
    protected $directory = 'app.purchase.requirement';
    protected $title_admin = 'Requerimientos de Compra';
    protected $title_show = 'Ver requerimiento de compra';
    protected $title_create = 'Crear requerimiento de compra';
    protected $title_edit = 'Editar requerimiento de compra';
    protected $category_menu = 'Compras';
    protected $option_menu = 'Requerimientos';
    protected $option_create = 'Crear requerimiento';
    protected $option_show = 'Ver requerimiento';
    protected $option_edit = 'Editar requerimiento';

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

        $requirement = new PurchaseRequirement();

        return view("{$this->directory}.create")->with(compact('requirement', 'title_create', 'category_menu', 'option_menu', 'option_create'));
    }

    /**
     * Show a resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show($requirement_id)
    {
        $title_show = $this->title_show;
        $category_menu = $this->category_menu;
        $option_menu = $this->option_menu;
        $option_show = $this->option_show;

        try {
            $requirement = PurchaseRequirement::findOrFail($requirement_id);
        } catch (\Exception $e) {
            return redirect()->route('requirement');
        }

        return view("{$this->directory}.show")->with(compact('requirement', 'title_show', 'category_menu', 'option_menu', 'option_show'));
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $requirement
     * @return \Illuminate\Http\Response
     */
    public function edit($requirement_id)
    {
        $title_edit = $this->title_edit;
        $category_menu = $this->category_menu;
        $option_menu = $this->option_menu;
        $option_edit = $this->option_edit;

        try {
            $requirement = PurchaseRequirement::findOrFail($requirement_id);
            if($requirement->status == "Y" || $requirement->status == "A"){
                Session::flash('warning', 'El requerimiento se encuentra con factura o anulado!');
                return redirect()->route('requirement');
            }
        } catch (\Exception $e) {
            return redirect()->route('requirement');
        }

        return view("{$this->directory}.edit")->with(compact('requirement', 'title_edit', 'category_menu', 'option_menu', 'option_edit'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $proforma
     * @return \Illuminate\Http\Response
     */
    /*public function pdf($proforma_id)
    {
        try {
            $proforma = PurchaseRequirement::findOrFail($proforma_id);
        } catch (\Exception $e) {
            return redirect()->route('requirement');
        }

        $pdf = PDF::loadView("{$this->directory}.pdf", compact('proforma'))->setPaper('A4', 'Portrait');
        return $pdf->stream("requirement-{$proforma->number}.pdf");
    }*/


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $proforma
     * @return \Illuminate\Http\Response
     */
    public function pdf($proforma_id)
    {
        try {
            $proforma = PurchaseRequirement::findOrFail($proforma_id);
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
        //$pdf::SetFillColor(0,111,173);
        $pdf::SetFillColor(41,50,131);
        $pdf::SetTextColor(255,255,255);
        $alto = 12;
        $pdf::MultiCell(70,$alto,utf8_decode("\nREQUERIMIENTO DE COMPRA"),$borde,'C',true);
        $pdf::SetTextColor(0);
        /*if(substr($Serie,1,3)=="002"){
            //$pdf::Image("logo-chiclayo.jpg",12,8,120,28);
        }else{
            //$pdf::Image("logo-lima.jpg",12,8,120,28);
        }*/
        $pdf::Image(__DIR__."/../../../../public/assets/images/logo.jpeg",45,8,40,20);
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
        
        $pdf::SetXY(15, $pdf::GetY());
        $pdf::SetFont('helvetica','',8);
        $pdf::Cell(100, $alto, "Telf: 972558640", $borde, 0, "C");
        $pdf::SetXY(25, $pdf::GetY()+$alto);
        $pdf::SetFont('helvetica','',8);
        $pdf::Multicell(80, $alto, "Email: ventas@ssymilabperu.com", $borde, "C");
        $pdf::SetXY(15, $pdf::GetY()+$alto+1);

        $details = ($proforma->detail && trim($proforma->detail) != "") ? json_decode($proforma->detail, true) : [];
        
        $pdf::SetXY(15, $pdf::GetY()+$alto-2);
        
        $pdf::SetFont('helvetica','B',9);
        $pdf::Cell(27, $alto, utf8_decode("DNI/RUC"), 'B', 0, "L");
        $pdf::Cell(3, $alto, utf8_decode(":"), 'B', 0, "L");
        $pdf::SetFont('helvetica','',9);
        $pdf::Cell(155, $alto, utf8_decode($proforma->client->getDocumentNumber()), 'B', 0, "L");

        $pdf::SetXY(15, $pdf::GetY()+$alto);
        $pdf::SetFont('helvetica','B',9);
        $pdf::Cell(27, $alto, ("Proveedor"), 'B', 0, "L");
        $pdf::Cell(3, $alto, (":"), 'B', 0, "L");
        $pdf::SetFont('helvetica','',9);
        $alto1 = $pdf::getNumLines($proforma->client->getNameComplete(), 155);
        $pdf::MultiCell(155, $alto, ($proforma->client->getNameComplete()), 'B', "L");
        
        $pdf::SetXY(15, $pdf::GetY());

        $pdf::SetFont('helvetica','B',9);
        $pdf::Cell(30, $alto, (""), '', 0, "L");
        $pdf::SetFont('helvetica','',9);
        $pdf::MultiCell(155, $alto, ("SSYMILAB E.I.R.L. \nATENCIÓN A: ".$proforma->comment()." \n  ventas@ssymilabperu.com \n +51 972558640"), 'B', "L");

        $pdf::SetXY(15, $pdf::GetY()-12);

        $pdf::SetFont('helvetica','B',9);
        $pdf::Cell(30, $alto, ("Enviar a"), '', 0, "L");

        $pdf::SetXY(15, $pdf::GetY()+$alto);
        $pdf::Cell(30, $alto, ("nombre de:"), '', 0, "L");        

        $pdf::SetXY(15, $pdf::GetY()+$alto*3-4);

        $pdf::SetFont('helvetica','B',9);
        $pdf::Cell(27, $alto, ("Transporte"), 'T', 0, "L");
        $pdf::Cell(3, $alto, utf8_decode(":"), 'T', 0, "L");
        $pdf::SetFont('helvetica','',8);
        $pdf::MultiCell(140, $alto, "", $borde,"L");

        $pdf::SetXY(15, $pdf::GetY());

        $pdf::RoundedRect(15, 52, 185, 34,2,'1234');

        $pdf::SetXY(15, $pdf::GetY()+$alto);

        $productosRectY = $pdf::GetY();
        $pdf::SetDrawColor(255, 255, 255);
        //$pdf::SetFillColor(0,111,173);
        $pdf::SetFillColor(41,50,131);
        $pdf::SetTextColor(255,255,255);
        $pdf::SetFont('helvetica','B',8);
        $alto=8;
        $agregar = 15;
        if($proforma->details[0]->product->type=='R'){
            $pdf::Cell(15, $alto, utf8_decode("CODIGO"), 1, 0, "C", true);
            $pdf::Cell(93, $alto, utf8_decode("DESCRIPCION"), 1, 0, "C", true);
        }else{
            $pdf::Cell(20, $alto, utf8_decode("CODIGO"), 1, 0, "C", true);
            $pdf::Cell(88, $alto, utf8_decode("DESCRIPCION"), 1, 0, "C", true);
        }
        $y = $pdf::GetY();
        $pdf::Cell(25, $alto, utf8_decode("PRESENTACION"), 1,0, "C", true);
        $pdf::SetXY(133+$agregar,$y);
        $pdf::Cell(15, $alto, utf8_decode("CANT."), 1, 0, "C", true);
        $y = $pdf::GetY();
        $pdf::Multicell(17, $alto, utf8_decode("PRECIO UNIT"), 1, "C", true);
        $pdf::SetXY(180,$y);
        $y = $pdf::GetY();
        $pdf::Multicell(20, $alto, utf8_decode("PRECIO TOTAL"), 1, "C", true);
        $pdf::SetXY(182+$agregar,$y);
        $pdf::SetFont('helvetica','',8);
        $pdf::Ln();
        $pdf::SetDrawColor(0, 0, 0);
        $total = 0;
        $alto = 4;
        $pdf::SetTextColor(0,0,0);
        $pdf::SetFillColor(255, 255, 255);
        $borde2 = 0;
        $productosRectHeight = 0;$total=0;
        foreach ($proforma->details as $key => $item){
            $details = ($item->detail && trim($item->detail) != "") ? json_decode($item->detail, true) : [];
            $fv = (array_key_exists('due_date', $details)) ? $details["due_date"] : "";
            $plazo = (array_key_exists('delivery_time', $details)) ? $details["delivery_time"] : "";

            $alto2 = $pdf::getNumLines(($item->product->name.' '.$item->product->presentation."\n MARCA: ".$item->product->brand->name) , 93);
            $pdf::SetXY(15, $pdf::GetY());
            if($proforma->details[0]->product->type=='R'){
                $pdf::Cell(15, $alto2, $item->product->code, $borde2, 0, "C");
                $y=$pdf::GetY();
                $pdf::MultiCell(93, $alto, $item->product->name.' '.$item->product->presentation."\n MARCA: ".$item->product->brand->name, $borde2, "L");
                $pdf::SetXY(123,$y);
            }else{
                $pdf::Cell(20, $alto2, $item->product->code, $borde2, 0, "C");
                $y=$pdf::GetY();
                $pdf::MultiCell(88, $alto, $item->product->name.' '.$item->product->presentation."\n MARCA: ".$item->product->brand->name, $borde2, "L");
                $pdf::SetXY(123,$y);
            }
            $pdf::Cell(25, $alto2, $item->unit->name, $borde2, 0, "C");
            $pdf::Cell(15, $alto2, sprintf('%g', $item->quantity), $borde2, 0, "C");
            $dat = $item->product->base_product_unit();
            $total = $total + $dat->purchase_price*$item->quantity;
            $pdf::Cell(17, $alto2, $dat->purchase_price, $borde2, 0, "C");
            $pdf::Cell(20, $alto2,$item->currency->symbol.' '. ($item->quantity*$dat->purchase_price), $borde2, 0, "C");
            $pdf::SetXY(15, $pdf::GetY()+($alto2-0.4)*5);
            $productosRectHeight += ($alto2-0.4)*5;
            $symbol = $item->currency->symbol;
        }
        $pdf::SetXY(15, $pdf::GetY()+$alto-2);
        $pdf::SetFont('helvetica','B',9);
        $pdf::Cell(165, $alto, "TOTAL", 'TR', 0, "R");
        $pdf::Cell(20, $alto,  $symbol.' '.$total, 'T', 0, "C");

        $pdf::RoundedRect(15, $productosRectY, 185, $productosRectHeight + $alto*4,2,'0110');

        $pdf::SetXY(15, $pdf::GetY()+$alto*2);

        $pdf::Image(__DIR__."/../../../../clifacturacion/controlador/firma.jpeg",85,$pdf::GetY()+$alto,50,30);

        $pdf::SetTitle("Requerimiento ".$proforma->number);
        $pdf::Output('Requerimiento.pdf');
        //$pdf = PDF::loadView("{$this->directory}.pdf", compact('proforma'))->setPaper('A4', 'Portrait');
        //return $pdf::stream("proforma-{$proforma->number}.pdf");
    }
}