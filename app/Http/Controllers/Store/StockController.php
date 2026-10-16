<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade as PDF;
use App\Http\Traits\General\StockTrait;

class StockController extends Controller
{
    use StockTrait;

    protected $directory = 'app.store.stock';
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
        //sleep(5);
        $title_admin = $this->title_admin;
        $category_menu = $this->category_menu;
        $option_menu = $this->option_menu;

        return view("{$this->directory}.admin")->with(compact('title_admin', 'category_menu', 'option_menu'));
    }

    public function pdf(Request $request)
    {
        //sleep(4);
        $lista = $this->search_stock(null, $request->input('product_name'),$request->input('type'),$request->input('brand_id'), 'products.name', 'asc', 10000);
        $pdf = PDF::loadView("{$this->directory}.pdf", compact('lista'))->setPaper('A4');
        return $pdf->stream("stock.pdf");
    }

    public function excel(Request $request)
    {
        $lista = $this->search_stock(null, $request->input('product_name'),$request->input('type'),$request->input('brand_id'), 'products.name', 'asc', 10000);
        return view("{$this->directory}.excel")->with(compact('lista'));
    }
}
