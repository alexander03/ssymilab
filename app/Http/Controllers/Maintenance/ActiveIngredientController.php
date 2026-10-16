<?php

namespace App\Http\Controllers\Maintenance;

use App\Http\Controllers\Controller;
use App\Models\ActiveIngredient;
use Illuminate\Http\Request;

class ActiveIngredientController extends Controller
{
    protected $directory = 'app.maintenance.active-ingredient';
    protected $title_admin = 'Principios Activos';
    protected $title_create = 'Crear principio activo';
    protected $title_edit = 'Editar principio activo';
    protected $category_menu = 'Mantenimiento';
    protected $option_menu = 'Principios Activos';
    protected $option_create = 'Crear principio activo';
    protected $option_edit = 'Editar principio activo';

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

        $active_ingredient = new ActiveIngredient();

        return view("{$this->directory}.create")->with(compact('active_ingredient', 'title_create', 'category_menu', 'option_menu', 'option_create'));
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $active_ingredient
     * @return \Illuminate\Http\Response
     */
    public function edit($active_ingredient_id)
    {
        $title_edit = $this->title_edit;
        $category_menu = $this->category_menu;
        $option_menu = $this->option_menu;
        $option_edit = $this->option_edit;

        try {
            $active_ingredient = ActiveIngredient::findOrFail($active_ingredient_id);
        } catch (\Exception $e) {
            return redirect()->route('active_ingredient');
        }

        return view("{$this->directory}.edit")->with(compact('active_ingredient', 'title_edit', 'category_menu', 'option_menu', 'option_edit'));
    }
}
