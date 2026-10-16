<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Http\Traits\General\StockTrait;
use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Support\Facades\Auth;

class ReportStockController extends Controller
{
    use StockTrait;

    protected $directory = 'app.report.stock';
    protected $title_admin = 'Stock';
    protected $category_menu = 'Almacén';
    protected $option_menu = 'Stock';
    protected $option_create = 'Crear marca';
    protected $option_edit = 'Editar marca';

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

    public function pdf(Request $request){
        ini_set("memory_limit",-1);
        $lista = Product::where('products.name','ILIKE','%'.$request->input('product_name').'%')
                        ->orderBy('products.brand_id', 'asc')
                        ->orderBy('products.name', 'asc')
                        ->select('products.*');
        if($request->input('brand_id')!=""){
            $lista = $lista->where('products.brand_id','=',$request->input('brand_id'));
        }
        if($request->input('type')!=""){
            $lista = $lista->where('products.type','=',$request->input('type'));
        }
        $lista = $lista->get();

        $pdf = PDF::loadView("{$this->directory}.pdf", compact('lista'))->setPaper('A4');
        return $pdf->stream("stock.pdf");
    }
}
