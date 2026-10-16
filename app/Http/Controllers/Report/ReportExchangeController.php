<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Purchase;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade as PDF;
use App\Http\Traits\Maintenance\ExchangeTrait;

class ReportExchangeController extends Controller
{
    use ExchangeTrait;

    protected $directory = 'app.report.exchange';
    protected $title_admin = 'Tipo Cambio';
    protected $category_menu = 'Reporte';
    protected $option_menu = 'Tipo Cambio';
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
        $list = $this->search_exhange($request->date_start,$request->date_end, 'date', 'desc', 100000);

        $fechainicio = $request->input('date_start');
        $fechafin = $request->input('date_end');
        $pdf = PDF::loadView("{$this->directory}.pdf", compact('list','fechainicio','fechafin'))->setPaper('A4');
        return $pdf->stream("exchange.pdf");
    }
}
