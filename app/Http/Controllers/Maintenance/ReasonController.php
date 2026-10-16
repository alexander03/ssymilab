<?php

namespace App\Http\Controllers\Maintenance;

use App\Http\Controllers\Controller;
use App\Models\Reason;
use Illuminate\Http\Request;

class ReasonController extends Controller
{
    protected $directory = 'app.maintenance.reason';
    protected $title_admin = 'Motivos';
    protected $title_create = 'Crear motivo';
    protected $title_edit = 'Editar motivo';
    protected $category_menu = 'Mantenimiento';
    protected $option_menu = 'Motivos';
    protected $option_create = 'Crear motivo';
    protected $option_edit = 'Editar motivo';

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

        $reason = new Reason();

        return view("{$this->directory}.create")->with(compact('reason', 'title_create', 'category_menu', 'option_menu', 'option_create'));
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $reason
     * @return \Illuminate\Http\Response
     */
    public function edit($reason_id)
    {
        $title_edit = $this->title_edit;
        $category_menu = $this->category_menu;
        $option_menu = $this->option_menu;
        $option_edit = $this->option_edit;

        try {
            $reason = Reason::findOrFail($reason_id);
        } catch (\Exception $e) {
            return redirect()->route('reason');
        }

        return view("{$this->directory}.edit")->with(compact('reason', 'title_edit', 'category_menu', 'option_menu', 'option_edit'));
    }
}
