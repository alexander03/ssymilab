<?php

namespace App\Http\Controllers\Maintenance;

use App\Http\Controllers\Controller;
use App\Models\Unit;
use Illuminate\Http\Request;

class UnitController extends Controller
{
    protected $directory = 'app.maintenance.unit';
    protected $title_admin = 'Unidades';
    protected $title_create = 'Crear unidad';
    protected $title_edit = 'Editar unidad';
    protected $category_menu = 'Mantenimiento';
    protected $option_menu = 'Unidades';
    protected $option_create = 'Crear unidad';
    protected $option_edit = 'Editar unidad';

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

        $unit = new Unit();

        return view("{$this->directory}.create")->with(compact('unit', 'title_create', 'category_menu', 'option_menu', 'option_create'));
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $unit
     * @return \Illuminate\Http\Response
     */
    public function edit($unit_id)
    {
        $title_edit = $this->title_edit;
        $category_menu = $this->category_menu;
        $option_menu = $this->option_menu;
        $option_edit = $this->option_edit;

        try {
            $unit = Unit::findOrFail($unit_id);
        } catch (\Exception $e) {
            return redirect()->route('unit');
        }

        return view("{$this->directory}.edit")->with(compact('unit', 'title_edit', 'category_menu', 'option_menu', 'option_edit'));
    }
}
