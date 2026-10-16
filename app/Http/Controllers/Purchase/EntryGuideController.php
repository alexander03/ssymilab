<?php

namespace App\Http\Controllers\Purchase;

use App\Http\Controllers\Controller;
use App\Models\DocumentDetail;
use App\Models\EntryGuide;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Barryvdh\DomPDF\Facade as PDF;

class EntryGuideController extends Controller
{
    protected $directory = 'app.purchase.entry-guide';
    protected $title_admin = 'Guías de Entrada';
    protected $title_show = 'Ver guía de entrada';
    protected $title_create = 'Crear Guías';
    protected $title_edit = 'Editar Guías';
    protected $category_menu = 'Guía de entrada';
    protected $option_menu = 'Guía de entrada';
    protected $option_create = 'Crear guía de entrada';
    protected $option_show = 'Ver guía de entrada';
    protected $option_edit = 'Editar guía de entrada';

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

        $entry_guide = new EntryGuide();

        return view("{$this->directory}.create")->with(compact('entry_guide', 'title_create', 'category_menu', 'option_menu', 'option_create'));
    }

    /**
     * Show a resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show($entry_guide_id)
    {
        $title_show = $this->title_show;
        $category_menu = $this->category_menu;
        $option_menu = $this->option_menu;
        $option_show = $this->option_show;

        try {
            $entry_guide = EntryGuide::findOrFail($entry_guide_id);
            $details = $entry_guide->details;
            foreach ($details as $key => $value) {
                $product_moves = DocumentDetail::product_moves_guide($value->id);
                $details[$key]->product_moves = $product_moves;
            }
        } catch (\Exception $e) {
            return redirect()->route('entry-guide');
        }

        return view("{$this->directory}.show")->with(compact('entry_guide', 'title_show', 'category_menu', 'option_menu', 'option_show'));
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $entry_guide
     * @return \Illuminate\Http\Response
     */
    // public function edit($entry_guide_id)
    // {
    //     $title_edit = $this->title_edit;
    //     $category_menu = $this->category_menu;
    //     $option_menu = $this->option_menu;
    //     $option_edit = $this->option_edit;

    //     try {
    //         $entry_guide = EntryGuide::findOrFail($entry_guide_id);
    //         if($entry_guide->status == "Y" || $entry_guide->status == "A"){
    //             Session::flash('warning', 'El documento de compra se encuentra anulado o con despachos!');
    //             return redirect()->route('entry_guide');
    //         }
    //     } catch (\Exception $e) {
    //         return redirect()->route('entry_guide');
    //     }

    //     return view("{$this->directory}.edit")->with(compact('entry_guide', 'title_edit', 'category_menu', 'option_menu', 'option_edit'));
    // }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $proforma
     * @return \Illuminate\Http\Response
     */
    public function pdf($entryguide_id)
    {
        try {
            $entryguide = EntryGuide::findOrFail($entryguide_id);
            $details = $entryguide->details;
            foreach ($details as $key => $value) {
                $product_moves = DocumentDetail::product_moves_guide($value->id);
                $details[$key]->product_moves = $product_moves;
            }
        } catch (\Exception $e) {
            return redirect()->route('entry-guide');
        }

        $pdf = PDF::loadView("{$this->directory}.pdf", compact('entryguide'))->setPaper('A4', 'Portrait');
        return $pdf->stream("recepcion-{$entryguide->number}.pdf");
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $proforma
     * @return \Illuminate\Http\Response
     */
    public function pdf2($entryguide_id)
    {
        try {
            $entryguide = EntryGuide::findOrFail($entryguide_id);
            $details = $entryguide->details;
            foreach ($details as $key => $value) {
                $product_moves = DocumentDetail::product_moves_guide($value->id);
                $details[$key]->product_moves = $product_moves;
            }
        } catch (\Exception $e) {
            return redirect()->route('entry-guide');
        }

        $pdf = PDF::loadView("{$this->directory}.pdf2", compact('entryguide'))->setPaper('Legal', 'Landscape');
        return $pdf->stream("analisis-organoleptico-{$entryguide->number}.pdf");
    }

    public function pdf3($entryguide_id)
    {
        try {
            $entryguide = EntryGuide::findOrFail($entryguide_id);
            $details = $entryguide->details;
            foreach ($details as $key => $value) {
                $product_moves = DocumentDetail::product_moves_guide($value->id);
                $details[$key]->product_moves = $product_moves;
            }
        } catch (\Exception $e) {
            return redirect()->route('entry-guide');
        }

        $pdf = PDF::loadView("{$this->directory}.pdf3", compact('entryguide'))->setPaper('A4', 'Portrait');
        return $pdf->stream("guiaentrada-{$entryguide->number}.pdf");
    }
}
