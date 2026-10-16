<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Purchase;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade as PDF;

class ReportPurchaseController extends Controller
{
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
        $list = Purchase::search($request->input('date_start'), $request->input('date_end'), null, null, $request->input('supplier'), null, $request->input('branch_office_id'))
            ->select(
                "documents.*",
                DB::raw("(CASE WHEN people.type = '1' THEN CONCAT(people.surname_father,' ',people.surname_mother,' ', people.name) ELSE people.business_name END) AS supplier_name"),
                DB::raw("(CASE WHEN people.type = '1' THEN people.dni ELSE people.ruc END) AS supplier_document"),
            );
        $list = $list->orderBy('date_time', 'asc')->get();
        $fechainicio = $request->input('date_start');
        $fechafin = $request->input('date_end');
        $pdf = PDF::loadView("{$this->directory}.pdf", compact('list','fechainicio','fechafin'))->setPaper('A4');
        return $pdf->stream("purchase.pdf");
    }
}
