<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Lot;
use App\Models\ProductMove;
use Barryvdh\DomPDF\Facade as PDF;

class ReportLotController extends Controller
{
    protected $directory = 'app.report.lot';
    protected $title_admin = 'Lotes';
    protected $category_menu = 'Almacén';
    protected $option_menu = 'Lotes';
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

    public function pdf($id)
    {
        $lot = Lot::find($id);
        $data = ProductMove::where('lot_id','=',$id)->where('status','Y')->orderBy('created_at','asc')->get();
        $pdf = PDF::loadView("{$this->directory}.pdf", compact('lot','data'))->setPaper('A4');
        return $pdf->stream("kardex-lot.pdf");
    }
}
