<?php

namespace App\Http\Controllers\Maintenance;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade as PDF;
use App\Http\Traits\Maintenance\BrandTrait;

class BrandController extends Controller
{
    use BrandTrait;

    protected $directory = 'app.maintenance.brand';
    protected $title_admin = 'Marcas';
    protected $title_create = 'Crear marca';
    protected $title_edit = 'Editar marca';
    protected $category_menu = 'Mantenimiento';
    protected $option_menu = 'Marcas';
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

        $brand = new Brand();

        return view("{$this->directory}.create")->with(compact('brand', 'title_create', 'category_menu', 'option_menu', 'option_create'));
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $brand
     * @return \Illuminate\Http\Response
     */
    public function edit($brand_id)
    {
        $title_edit = $this->title_edit;
        $category_menu = $this->category_menu;
        $option_menu = $this->option_menu;
        $option_edit = $this->option_edit;

        try {
            $brand = Brand::findOrFail($brand_id);
        } catch (\Exception $e) {
            return redirect()->route('brand');
        }

        return view("{$this->directory}.edit")->with(compact('brand', 'title_edit', 'category_menu', 'option_menu', 'option_edit'));
    }

    public function pdf($id,Request $request)
    {
        $lista = $this->search_brands($request->input('name'), 'name', 'asc', 10000);
        $pdf = PDF::loadView("{$this->directory}.pdf", compact('lista'))->setPaper('A4', 'Portrait');
        return $pdf->stream("brand.pdf");
    }
}
