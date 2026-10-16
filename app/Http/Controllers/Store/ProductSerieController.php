<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Traits\Store\ProductSerieTrait;
use Barryvdh\DomPDF\Facade as PDF;

class ProductSerieController extends Controller
{
    use ProductSerieTrait;

    protected $directory = 'app.store.product-serie';
    protected $title_admin = 'Series';
    protected $category_menu = 'Almacén';
    protected $option_menu = 'Series';

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
        $list = $this->search_series(null, $request->input('product_name'), $request->input('serie'), null, 'products.name', 'asc', 100000000);
        $pdf = PDF::loadView("{$this->directory}.pdf", compact('list'))->setPaper('A4');
        return $pdf->stream("serie.pdf");
    }
}
