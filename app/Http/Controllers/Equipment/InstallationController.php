<?php

namespace App\Http\Controllers\Equipment;

use App\Http\Controllers\Controller;
use App\Models\Schedule;
use App\Models\Maintenance;
use App\Models\ProductMoveMaintenance;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade as PDF;

class InstallationController extends Controller
{
    protected $directory = 'app.equipment.installation';
    protected $title_admin = 'Instalacion';
    protected $title_create = 'Crear Instalacion';
    protected $title_edit = 'Editar Instalacion';
    protected $category_menu = 'Equipos';
    protected $option_menu = 'Instalacion';
    protected $option_create = 'Crear Instalacion';
    protected $option_edit = 'Editar Instalacion';

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
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $title_create = $this->title_create;
        $category_menu = $this->category_menu;
        $option_menu = $this->option_menu;
        $option_create = $this->option_create;

        $schedule = new Schedule();

        return view("{$this->directory}.create")->with(compact('schedule', 'title_create', 'category_menu', 'option_menu', 'option_create'));
    }

        /**
     * Show the form for editing the specified resource.
     *
     * @param  int $order
     * @return \Illuminate\Http\Response
     */
    public function edit($schedule_id)
    {
        $title_edit = $this->title_edit;
        $category_menu = $this->category_menu;
        $option_menu = $this->option_menu;
        $option_edit = $this->option_edit;
        try {
            $schedule = Schedule::findOrFail($schedule_id);
        } catch (\Exception $e) {
            return redirect()->route('installation');
        }
        return view("{$this->directory}.edit")->with(compact('schedule', 'title_edit', 'category_menu', 'option_menu', 'option_edit'));
    }

    public function download_file($id,$name)
    {
        //COLOCAR SEGURIDAD SI ES ADMINISTRADOR O SI LO TIENE COMPRADO
        try {
            $schedule = Schedule::findOrFail($id);
            $media = $schedule->getMedia('installation');
            foreach ($schedule->getMedia('installation') as $key => $value){
                if($value->name==$name){
                    return response()->download($value->getPath(), $value->file_name);
                }
            }
            
        } catch (\Exception $e) {
            return redirect()->route('product');
        }
    }

        /**
     * Show the form for editing the specified resource.
     *
     * @param  int $proforma
     * @return \Illuminate\Http\Response
     */
    public function pdf($schedule_id,Request $request)
    {
        try {
            $schedule = Schedule::findOrFail($schedule_id);
        } catch (\Exception $e) {
            return redirect()->route('installation');
        }
        $anio = $request->input('anio');
        $maintenance = Maintenance::where('product_id','=',$schedule->product_serie->product_id)->first();
        $pdf = PDF::loadView("{$this->directory}.pdf", compact('schedule','maintenance','anio'))->setPaper('A4', 'Landscape');
        return $pdf->stream("installation-{$schedule->id}.pdf");
    }

    public function pdfList($schedule_id,Request $request)
    {
        $list = Schedule::search($request->input('date_start'),$request->input('date_end'),$request->input('product_name'),$request->input('product_serie'),$request->input('customer'),'',$request->input('status'),$request->input('brand_id'),$request->input('product_model'));

        $list = $list->orderBy('schedules.installed_date', 'asc')
            ->select('schedules.*', 'products.name AS product_name', 'products.id AS product_id')->get();
        $pdf = PDF::loadView("{$this->directory}.pdfList", compact('list'))->setPaper('A4');
        return $pdf->stream("installation-list.pdf");
    }

    public function pdfFollow($schedule_id,Request $request)
    {
        try {
            $schedule = Schedule::findOrFail($schedule_id);
        } catch (\Exception $e) {
            return redirect()->route('installation');
        }
        $list = ProductMoveMaintenance::search($schedule_id)->orderBy('product_maintenances.done_date','asc')->select('product_move_maintenances.*')->get();
        $pdf = PDF::loadView("{$this->directory}.pdfFollow", compact('schedule','list'))->setPaper('A4', 'Landscape');
        return $pdf->stream("follow-{$schedule->id}.pdf");
    }
}
