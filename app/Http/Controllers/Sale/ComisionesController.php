<?php

namespace App\Http\Controllers\Sale;

use App\Http\Controllers\Controller;
use App\Models\SaleGuide;
use App\Models\Sale;
use App\Models\DocumentDetail;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade as PDF;

class ComisionesController extends Controller
{
    protected $directory = 'app.sale.comisiones';
    protected $title_admin = 'Comisiones';
    protected $title_show = 'Ver venta';
    protected $title_create = 'Crear venta';
    protected $title_edit = 'Editar venta';
    protected $category_menu = 'Ventas';
    protected $option_menu = 'Comisiones';
    protected $option_create = 'Crear venta';
    protected $option_show = 'Ver venta';
    protected $option_edit = 'Editar venta';

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

        $sale = new Sale();

        return view("{$this->directory}.create")->with(compact('sale', 'title_create', 'category_menu', 'option_menu', 'option_create'));
    }

    /**
     * Show a resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show($sale_id)
    {
        $title_show = $this->title_show;
        $category_menu = $this->category_menu;
        $option_menu = $this->option_menu;
        $option_show = $this->option_show;

        try {
            $sale = Sale::findOrFail($sale_id);
            $details = $sale->details;
            foreach ($details as $key => $value) {
                $product_moves = DocumentDetail::product_moves($value->id);
                $details[$key]->product_moves = $product_moves;
            }
            $sale_guides = SaleGuide::where('document_id', $sale->id)->where('status', '<>', 'A')->get();
            $documents = [];

            if($sale->document){
                $documents["document"] = $sale->document;
            }

            if(count($sale_guides) > 0){
                $documents["sale_guides"] = $sale_guides;
            }

        } catch (\Exception $e) {
            return redirect()->route('sale');
        }

        return view("{$this->directory}.show")->with(compact('sale', 'title_show', 'category_menu', 'option_menu', 'option_show', 'documents'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $proforma
     * @return \Illuminate\Http\Response
     */
    public function pdf(Request $request)
    {
        $fecha = $request->input('mes')."-01";
        try {
            $sale = Sale::where('paid_comision','=',$fecha)->orderBy('person_id','desc')->orderBy('date_time','desc')->get();
        } catch (\Exception $e) {
            return redirect()->route('comisiones');
        }
        $pdf = PDF::loadView("{$this->directory}.pdf", compact('sale','fecha'))->setPaper('A4', 'Portrait');
        return $pdf->stream("comisiones.pdf");
    }

}
