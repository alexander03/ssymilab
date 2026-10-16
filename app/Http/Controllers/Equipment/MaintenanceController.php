<?php

namespace App\Http\Controllers\Equipment;

use App\Http\Controllers\Controller;
use App\Models\ProductMaintenance;
use Illuminate\Http\Request;

class MaintenanceController extends Controller
{
    protected $directory = 'app.equipment.maintenance';
    protected $title_admin = 'Mantenimientos';
    protected $title_create = 'Crear Mantenimiento';
    protected $title_edit = 'Editar Mantenimiento';
    protected $category_menu = 'Equipos';
    protected $option_menu = 'Mantenimientos';
    protected $option_create = 'Crear Mantenimiento';
    protected $option_edit = 'Editar Mantenimiento';

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

        $productmaintenance = new ProductMaintenance();

        return view("{$this->directory}.create")->with(compact('productmaintenance', 'title_create', 'category_menu', 'option_menu', 'option_create'));
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $order
     * @return \Illuminate\Http\Response
     */
    public function edit($productmaintenance_id)
    {
        $title_edit = $this->title_edit;
        $category_menu = $this->category_menu;
        $option_menu = $this->option_menu;
        $option_edit = $this->option_edit;

        try {
            $productmaintenance = ProductMaintenance::findOrFail($productmaintenance_id);
        } catch (\Exception $e) {
            return redirect()->route('maintenance');
        }

        return view("{$this->directory}.edit")->with(compact('productmaintenance', 'title_edit', 'category_menu', 'option_menu', 'option_edit'));
    }
}
