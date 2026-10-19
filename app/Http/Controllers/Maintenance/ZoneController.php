<?php

namespace App\Http\Controllers\Maintenance;

use App\Http\Controllers\Controller;
use App\Models\Zone;

class ZoneController extends Controller
{
    protected $directory = 'app.maintenance.zone';
    protected $title_admin = 'Zonas';
    protected $title_create = 'Crear zona';
    protected $title_edit = 'Editar zona';
    protected $category_menu = 'Mantenimiento';
    protected $option_menu = 'Zonas';
    protected $option_create = 'Crear zona';
    protected $option_edit = 'Editar zona';

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

        $zone = new Zone();

        return view("{$this->directory}.create")->with(compact('zone', 'title_create', 'category_menu', 'option_menu', 'option_create'));
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $zone
     * @return \Illuminate\Http\Response
     */
    public function edit($zone_id)
    {
        $title_edit = $this->title_edit;
        $category_menu = $this->category_menu;
        $option_menu = $this->option_menu;
        $option_edit = $this->option_edit;

        try {
            $zone = Zone::findOrFail($zone_id);
        } catch (\Exception $e) {
            return redirect()->route('zone');
        }

        return view("{$this->directory}.edit")->with(compact('zone', 'title_edit', 'category_menu', 'option_menu', 'option_edit'));
    }
}
