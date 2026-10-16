<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Lot;
use App\Models\ProductMove;
use Barryvdh\DomPDF\Facade as PDF;

class LotController extends Controller
{
    protected $directory = 'app.store.lot';
    protected $title_admin = 'Lotes';
    protected $title_edit= 'Editar Lote';
    protected $category_menu = 'Almacén';
    protected $option_menu = 'Lotes';
    protected $option_create = 'Crear marca';
    protected $option_edit = 'Editar lote';

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

    public function edit($lot_id)
    {
        //sleep(4);
        $title_edit = $this->title_edit;
        $category_menu = $this->category_menu;
        $option_menu = $this->option_menu;
        $option_edit = $this->option_edit;

        try {
            $lot = Lot::findOrFail($lot_id);
        } catch (\Exception $e) {
            return redirect()->route('lot');
        }

        return view("{$this->directory}.edit")->with(compact('lot', 'title_edit', 'category_menu', 'option_menu', 'option_edit'));
    }

    public function pdf($id)
    {
        //sleep(4);
        $lot = Lot::find($id);
        $data = ProductMove::where('lot_id','=',$id)->where('status','Y')->orderBy('created_at','asc')->get();
        $pdf = PDF::loadView("{$this->directory}.pdf", compact('lot','data'))->setPaper('A4');
        return $pdf->stream("kardex-lot.pdf");
    }
}
