<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Kardex;
use App\Models\Product;
use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Support\Facades\Auth;

class KardexController extends Controller
{
    protected $directory = 'app.store.kardex';
    protected $title_admin = 'Kardex';
    protected $category_menu = 'Almacén';
    protected $option_menu = 'Kardex';
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

    public function pdf(Request $request)
    {
        $user = Auth::user();
        $list = Kardex::search(null, $request->input('product_name'), $request->input('date_start'), $request->input('date_end'), $user->branch_office_id)
            ->select('kardexes.*', 'products.name as product_name','documents.date_time','documents.number','people.business_name','people.name','people.surname_father','people.surname_mother','products.presentation','products.model','brand.name as brand_name')
            ;
        $list = $list->orderBy('products.id','asc')->orderBy('documents.date_time','asc')->get();
        $fechainicio = $request->input('date_start');
        $fechafin = $request->input('date_end');
        $pdf = PDF::loadView("{$this->directory}.pdf", compact('list','fechainicio','fechafin'))->setPaper('A4');
        return $pdf->stream("kardex-valorizado.pdf");
    }

    public function pdf2(Request $request)
    {
        $user = Auth::user();
        $list = Kardex::search(null, $request->input('product_name'), $request->input('date_start'), $request->input('date_end'), $user->branch_office_id)
            ->select('kardexes.*', 'products.name as product_name','documents.date_time','documents.number','people.business_name','people.name','people.surname_father','people.surname_mother','products.presentation','products.model','brand.name as brand_name')
            ;
        $list = $list->orderBy('products.id','asc')->orderBy('documents.date_time','asc')->get();
        $fechainicio = $request->input('date_start');
        $fechafin = $request->input('date_end');
        $pdf = PDF::loadView("{$this->directory}.pdf2", compact('list','fechainicio','fechafin'))->setPaper('A4');
        return $pdf->stream("kardex.pdf");
    }
}
