<?php

namespace App\Http\Controllers\Equipment;

use App\Http\Controllers\Controller;
use App\Http\Traits\Equipment\MaintenanceTrait;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade as PDF;

class ProximomantenimientoController extends Controller
{
    use MaintenanceTrait;

    protected $directory = 'app.equipment.proximomantenimiento';
    protected $title_admin = 'Proximos Mantenimientos';
    protected $category_menu = 'Equipos';
    protected $option_menu = 'Proximos Mantenimientos';

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
     * Show the form for editing the specified resource.
     *
     * @param  int $proforma
     * @return \Illuminate\Http\Response
     */
    public function pdf()
    {
        $lista = $this->search_proximomantenimiento(null,null, null, null, 'schedules.id','desc', 100000);
        $pdf = PDF::loadView("{$this->directory}.pdf", compact('lista'))->setPaper('A4', 'Landscape');
        return $pdf->stream("reporte-proximomantenimiento.pdf");
    }
}
