<?php

namespace App\Http\Controllers\Security;

use App\Http\Controllers\Controller;
use App\Models\Action;
use Illuminate\Http\Request;

class ActionController extends Controller
{
    protected $directory = 'app.security.action';
    protected $title_admin = 'Acciones';
    protected $title_create = 'Crear acción';
    protected $title_edit = 'Editar acción';
    protected $category_menu = 'Seguridad';
    protected $option_menu = 'Acciones';
    protected $option_create = 'Crear acción';
    protected $option_edit = 'Editar acción';

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

        $action = new Action();

        return view("{$this->directory}.create")->with(compact('action', 'title_create', 'category_menu', 'option_menu', 'option_create'));
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $menu_option
     * @return \Illuminate\Http\Response
     */
    public function edit($action_id)
    {
        $title_edit = $this->title_edit;
        $category_menu = $this->category_menu;
        $option_menu = $this->option_menu;
        $option_edit = $this->option_edit;

        try {
            $action = Action::findOrFail($action_id);
        } catch (\Exception $e) {
            return redirect()->route('action');
        }

        return view("{$this->directory}.edit")->with(compact('action', 'title_edit', 'category_menu', 'option_menu', 'option_edit'));
    }
}
