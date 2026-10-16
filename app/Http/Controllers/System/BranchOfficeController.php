<?php

namespace App\Http\Controllers\System;

use App\Http\Controllers\Controller;
use App\Models\BranchOffice;
use Illuminate\Http\Request;

class BranchOfficeController extends Controller
{
    protected $directory = 'app.system.branch-office';
    protected $title_admin = 'Sucursales';
    protected $title_create = 'Crear sucursal';
    protected $title_edit = 'Editar sucursal';
    protected $category_menu = 'Mantenimiento';
    protected $option_menu = 'Sucursales';
    protected $option_create = 'Crear sucursal';
    protected $option_edit = 'Editar sucursal';

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

        $branch_office = new BranchOffice();

        return view("{$this->directory}.create")->with(compact('branch_office', 'title_create', 'category_menu', 'option_menu', 'option_create'));
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $branch_office
     * @return \Illuminate\Http\Response
     */
    public function edit($branch_office_id)
    {
        $title_edit = $this->title_edit;
        $category_menu = $this->category_menu;
        $option_menu = $this->option_menu;
        $option_edit = $this->option_edit;

        try {
            $branch_office = BranchOffice::findOrFail($branch_office_id);
        } catch (\Exception $e) {
            return redirect()->route('branch-office');
        }

        return view("{$this->directory}.edit")->with(compact('branch_office', 'title_edit', 'category_menu', 'option_menu', 'option_edit'));
    }


}
