<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Purchase;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade as PDF;

class ReportProductSupplierController extends Controller
{
    protected $directory = 'app.report.product-supplier';
    protected $title_admin = 'Compras por Producto';
    protected $category_menu = 'Reporte';
    protected $option_menu = 'Reporte';
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
        $list = Purchase::SearchDetail($request->input('date_start'), $request->input('date_end'), null, null, $request->input('supplier'),$request->input('product'), null, $request->input('branch_office_id'))
            ->select(
                "documents.*",
                DB::raw("(CASE WHEN people.type = '1' THEN CONCAT(people.surname_father,' ',people.surname_mother,' ', people.name) ELSE people.business_name END) AS supplier_name"),
                DB::raw("(CASE WHEN people.type = '1' THEN people.dni ELSE people.ruc END) AS supplier_document"),
                DB::raw('products.name as product_name'),'document_details.purchase_price','document_details.quantity','products.model','products.presentation'
            );
        $list = $list->orderBy('date_time', 'asc')->get();
        $fechainicio = $request->input('date_start');
        $fechafin = $request->input('date_end');
        $pdf = PDF::loadView("{$this->directory}.pdf", compact('list','fechainicio','fechafin'))->setPaper('A4','landscape');
        return $pdf->stream("purchase.pdf");
    }

    public function pdfResumen(Request $request)
    {
        $list = Purchase::SearchDetail($request->input('date_start'), $request->input('date_end'), null, null, $request->input('supplier'),$request->input('product'), null, $request->input('branch_office_id'))
            ->select(
                "documents.*",
                DB::raw("(CASE WHEN people.type = '1' THEN CONCAT(people.surname_father,' ',people.surname_mother,' ', people.name) ELSE people.business_name END) AS supplier_name"),
                DB::raw("(CASE WHEN people.type = '1' THEN people.dni ELSE people.ruc END) AS supplier_document"),
                DB::raw('products.name as product_name'),'document_details.purchase_price','document_details.quantity','products.model','products.presentation','products.id as product_id'
            );
        $list = $list->orderBy('products.name', 'asc')
                    ->orderBy('products.model', 'asc')
                    ->orderBy('products.presentation', 'asc')
                    ->orderBy(DB::raw("(CASE WHEN people.type = '1' THEN CONCAT(people.surname_father,' ',people.surname_mother,' ', people.name) ELSE people.business_name END)"), 'asc')
                    ->get();
        $fechainicio = $request->input('date_start');
        $fechafin = $request->input('date_end');
        $pdf = PDF::loadView("{$this->directory}.pdfResumen", compact('list','fechainicio','fechafin'))->setPaper('A4','landscape');
        return $pdf->stream("purchaseResumen.pdf");
    }
}
