<?php

namespace App\Http\Controllers\Purchase;

use App\Http\Controllers\Controller;
use App\Models\EntryGuide;
use App\Models\Purchase;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade as PDF;
use Elibyy\TCPDF\Facades\TCPDF;
use App\Librerias\Libreria;
use App\Http\Traits\Purchase\PurchaseTrait;

class PurchaseController extends Controller
{
    use PurchaseTrait;

    protected $directory = 'app.purchase.purchase';
    protected $title_admin = 'Compras';
    protected $title_show = 'Ver compra';
    protected $title_create = 'Crear compra';
    protected $title_edit = 'Editar compra';
    protected $category_menu = 'Compras';
    protected $option_menu = 'Compras';
    protected $option_create = 'Crear compra';
    protected $option_show = 'Ver compra';
    protected $option_edit = 'Editar compra';

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

        $purchase = new Purchase();

        return view("{$this->directory}.create")->with(compact('purchase', 'title_create', 'category_menu', 'option_menu', 'option_create'));
    }

    /**
     * Show a resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show($purchase_id)
    {
        $title_show = $this->title_show;
        $category_menu = $this->category_menu;
        $option_menu = $this->option_menu;
        $option_show = $this->option_show;

        try {
            $purchase = Purchase::findOrFail($purchase_id);
            $entry_guides = EntryGuide::where('document_id', $purchase->id)->where('status', '<>', 'A')->get();
            $documents = [];

            if($purchase->document){
                $documents["requirement"] = $purchase->document;
            }

            if(count($entry_guides) > 0){
                $documents["entry_guides"] = $entry_guides;
            }

        } catch (\Exception $e) {
            return redirect()->route('purchase');
        }

        return view("{$this->directory}.show")->with(compact('purchase', 'title_show', 'category_menu', 'option_menu', 'option_show', 'documents'));
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $purchase
     * @return \Illuminate\Http\Response
     */
    // public function edit($purchase_id)
    // {
    //     $title_edit = $this->title_edit;
    //     $category_menu = $this->category_menu;
    //     $option_menu = $this->option_menu;
    //     $option_edit = $this->option_edit;

    //     try {
    //         $purchase = Purchase::findOrFail($purchase_id);
    //         if($purchase->status == "A"){
    //             Session::flash('warning', 'El documento de compra se encuentra anulado o con despachos!');
    //             return redirect()->route('purchase');
    //         }
    //     } catch (\Exception $e) {
    //         return redirect()->route('purchase');
    //     }

    //     return view("{$this->directory}.edit")->with(compact('purchase', 'title_edit', 'category_menu', 'option_menu', 'option_edit'));
    // }

    //DESCARGA EL ARCHIVO (IMAGEN O PDF) ADJUNTO A LA COMPRA
    public function download_file($id)
    {
        $purchase = Purchase::find($id);
        $media = $purchase ? $purchase->getFirstMedia('images') : null;
        if(!$media || !file_exists($media->getPath())){
            return redirect()->route('purchase');
        }
        return response()->download($media->getPath(), $media->file_name);
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
            $proforma = Purchase::findOrFail($proforma_id);
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
        //$pdf::SetTextColor(75,119,227);
        $pdf::SetFillColor(0,111,173);
        $pdf::SetTextColor(255,255,255);
        $alto = 12;
        $pdf::MultiCell(70,$alto,utf8_decode("\nCOMPRA"),$borde,'C',true);
        $pdf::SetTextColor(0);
        if($proforma->branch_office_id==1){
            //$pdf::Image(__DIR__."/../../../../clifacturacion/controlador/logo-chiclayo.jpg",15,4,104,40);
        }else{
            //$pdf::Image(__DIR__."/../../../../clifacturacion/controlador/logo-lima.jpg",15,4,104,40);
        }
        $pdf::Image(__DIR__."/../../../../public/assets/images/logo.jpeg",45,8,40,20);
        $pdf::SetFont('helvetica','',10);
        $pdf::SetXY(120, $pdf::GetY());
        $pdf::Cell(70, $alto, utf8_decode("Nro. ".$proforma->number), $borde, 0, "C");
        //$pdf::Rect(140, 12, 58, 25,'F');
        $pdf::RoundedRect(120, 7, 70, 35,2,'1234');
        
        $pdf::SetXY(15, 47);
        $alto = 4;
        /*$pdf::SetFont('helvetica','B',11);
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
        $pdf::SetXY(15, $pdf::GetY()+$alto+1);

        $details = ($proforma->detail && trim($proforma->detail) != "") ? json_decode($proforma->detail, true) : [];
        
        $pdf::SetXY(15, $pdf::GetY()+$alto);*/
        
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
        $pdf::Cell(27, $alto, ("Comentario"), 'T', 0, "L");
        $pdf::Cell(3, $alto, utf8_decode(":"), 'T', 0, "L");
        $pdf::SetFont('helvetica','',8);
        $pdf::MultiCell(140, $alto, $proforma->comment(), $borde,"L");

        $pdf::SetXY(15, $pdf::GetY());

        $pdf::RoundedRect(15, 47, 185, 15,2,'1234');

        $pdf::SetXY(15, $pdf::GetY()+$alto);

        $productosRectY = $pdf::GetY();
        $pdf::SetDrawColor(255, 255, 255);
        $pdf::SetFillColor(0,111,173);
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
        $alto = 5;
        $pdf::SetTextColor(0,0,0);
        $pdf::SetFillColor(255, 255, 255);
        $borde2 = 0;
        $productosRectHeight = 0;
        foreach ($proforma->details as $key => $item){
            $details = ($item->detail && trim($item->detail) != "") ? json_decode($item->detail, true) : [];
            $fv = (array_key_exists('due_date', $details)) ? $details["due_date"] : "";
            $plazo = (array_key_exists('delivery_time', $details)) ? $details["delivery_time"] : "";

            $pdf::SetXY(15, $pdf::GetY());
            if($proforma->details[0]->product->type=='R'){
                $alto2 = $pdf::getNumLines(($item->product->name.' '.$item->product->presentation.' '.$item->product->model."\n MARCA: ".$item->product->brand->name) , 93);
                $pdf::Cell(15, $alto2, $item->product->code, $borde2, 0, "C");
                $y=$pdf::GetY();
                $pdf::MultiCell(93, $alto, $item->product->name.' '.$item->product->presentation.' '.$item->product->model."\n MARCA: ".$item->product->brand->name, $borde2, "L");
                $pdf::SetXY(123,$y);
            }else{
                $alto2 = $pdf::getNumLines(($item->product->name.' '.$item->product->presentation.' '.$item->product->model."\n MARCA: ".$item->product->brand->name) , 88);
                $pdf::Cell(20, $alto2, $item->product->code, $borde2, 0, "C");
                $y=$pdf::GetY();
                $pdf::MultiCell(88, $alto, $item->product->name.' '.$item->product->presentation.' '.$item->product->model."\n MARCA: ".$item->product->brand->name, $borde2, "L");
                $pdf::SetXY(123,$y);
            }
            $pdf::Cell(25, $alto2, $item->unit->name, $borde2, 0, "C");
            $pdf::Cell(15, $alto2, sprintf('%g', $item->quantity), $borde2, 0, "C");
            $pdf::Cell(17, $alto2, $item->currency->symbol . " " . number_format($item->purchase_price, 2), $borde2, 0, "C");
            $pdf::Cell(20, $alto2, $item->currency->symbol . " " . number_format($item->quantity * $item->purchase_price, 2), $borde2, 0, "L");
            $pdf::SetXY(15, $pdf::GetY()+($alto2-0.4)*5);
            if($pdf::GetY()>265){
                $pdf::RoundedRect(15, $productosRectY, 185, $productosRectHeight + $alto*3 + $alto2,2,'0110');
                $pdf::AddPage('P','A4');
                $pdf::SetXY(15, 10);
                $productosRectHeight = -($alto2-0.4)*5*2;
                $productosRectY = 10;
            }
            $productosRectHeight += ($alto2-0.4)*5;
        }
        $pdf::SetXY(15, $pdf::GetY()+$alto-2);
        $pdf::SetFont('helvetica','B',9);
        $pdf::Cell(165, $alto, "OTROS", 'TR', 0, "R");
        $pdf::Cell(20, $alto,  $item->currency->symbol . " " . number_format($proforma->other, 2) . "", 'T', 0, "L");

        $pdf::SetXY(15, $pdf::GetY()+$alto);
        $pdf::SetFont('helvetica','B',9);
        $pdf::Cell(165, $alto, "FLETE", 'TR', 0, "R");
        $pdf::Cell(20, $alto,  $item->currency->symbol . " " . number_format($proforma->flete, 2) . "", 'T', 0, "L");

        $pdf::SetXY(15, $pdf::GetY()+$alto);
        $pdf::SetFont('helvetica','B',9);
        $pdf::Cell(165, $alto, "SEGURO", 'TR', 0, "R");
        $pdf::Cell(20, $alto,  $item->currency->symbol . " " . number_format($proforma->seguro, 2) . "", 'T', 0, "L");

        $pdf::SetXY(15, $pdf::GetY()+$alto);
        $pdf::SetFont('helvetica','B',9);
        $pdf::Cell(165, $alto, "TOTAL", 'TR', 0, "R");
        $pdf::Cell(20, $alto,  $item->currency->symbol . " " . number_format($proforma->total, 2) . "", 'T', 0, "L");

        $pdf::RoundedRect(15, $productosRectY, 185, $productosRectHeight + $alto*7-4,2,'0110');

        $pdf::SetXY(15, $pdf::GetY()+$alto*2);

        $pdf::SetTitle("Compra ".$proforma->number);
        $pdf::Output('Compra.pdf');
        //$pdf = PDF::loadView("{$this->directory}.pdf", compact('proforma'))->setPaper('A4', 'Portrait');
        //return $pdf::stream("proforma-{$proforma->number}.pdf");
    }

    public function excel(Request $request)
    { 
        $lista = $this->search_purchases($request->input('date_start'),$request->input('date_end'),$request->input('document_type_id'), $request->input('number'),$request->input('supplier'),'documents.created_at', 'asc', 1000000);
        
        return view("{$this->directory}.excel")->with(compact('lista'));
    }
}
