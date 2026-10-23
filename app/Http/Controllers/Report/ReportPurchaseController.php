<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Purchase;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade as PDF;
use App\Http\Traits\Purchase\PurchaseTrait;

class ReportPurchaseController extends Controller
{
    use PurchaseTrait;

    protected $directory = 'app.report.purchase';
    protected $title_admin = 'Compras';
    protected $category_menu = 'Reporte';
    protected $option_menu = 'Compras';
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
        $data = $this->report_data($request);
        $pdf = PDF::loadView("{$this->directory}.pdf", $data)->setPaper('A4');
        return $pdf->stream("purchase.pdf");
    }

    //MISMOS FILTROS Y COLUMNAS QUE EL PDF
    public function excel(Request $request)
    {
        $data = $this->report_data($request);
        $html = view("{$this->directory}.pdf")->with($data)->render();
        return view("{$this->directory}.excel")->with(compact('html'));
    }

    private function report_data(Request $request)
    {
        $list = Purchase::search($request->input('date_start'), $request->input('date_end'), null, null, $request->input('supplier'), null, $request->input('branch_office_id'))
            ->select(
                "documents.*",
                DB::raw("(CASE WHEN people.type = '1' THEN CONCAT(people.surname_father,' ',people.surname_mother,' ', people.name) ELSE people.business_name END) AS supplier_name"),
                DB::raw("(CASE WHEN people.type = '1' THEN people.dni ELSE people.ruc END) AS supplier_document"),
            );
        $list = $this->filter_purchase_origin($list, $request->input('origin'));
        $list = $list->orderBy('date_time', 'asc')->get();
        $fechainicio = $request->input('date_start');
        $fechafin = $request->input('date_end');

        return compact('list','fechainicio','fechafin');
    }
}
