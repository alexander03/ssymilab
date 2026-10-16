<?php

namespace App\Http\Controllers\Security;

use App\Http\Controllers\Controller;
use App\Models\MenuOption;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class MenuOptionController extends Controller
{
    protected $directory = 'app.security.menu-option';
    protected $title_admin = 'Opciones de Menú';
    protected $title_create = 'Crear opcion de Menú';
    protected $title_edit = 'Editar opcion de Menú';
    protected $category_menu = 'Seguridad';
    protected $option_menu = 'Opciones de Menú';
    protected $option_create = 'Crear categoría de Menú';
    protected $option_edit = 'Editar categoría de Menú';

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

        $menu_option = new MenuOption();

        return view("{$this->directory}.create")->with(compact('menu_option', 'title_create', 'category_menu', 'option_menu', 'option_create'));
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $menu_option_id
     * @return \Illuminate\Http\Response
     */
    public function edit($menu_option_id)
    {
        $title_edit = $this->title_edit;
        $category_menu = $this->category_menu;
        $option_menu = $this->option_menu;
        $option_edit = $this->option_edit;

        try {
            $menu_option = MenuOption::findOrFail($menu_option_id);
        } catch (\Exception $e) {
            return redirect()->route('menu-option');
        }

        return view("{$this->directory}.edit")->with(compact('menu_option', 'title_edit', 'category_menu', 'option_menu', 'option_edit'));
    }
}
