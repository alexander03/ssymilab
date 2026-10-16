<?php

namespace App\Http\Controllers\Security;

use App\Http\Controllers\Controller;
use App\Models\MenuCategory;
use Illuminate\Http\Request;

class MenuCategoryController extends Controller
{
    protected $directory = 'app.security.menu-category';
    protected $title_admin = 'Categorías de Menú';
    protected $title_create = 'Crear categoría de Menú';
    protected $title_edit = 'Editar categoría de Menú';
    protected $category_menu = 'Seguridad';
    protected $option_menu = 'Categorías de Menú';
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

        $menu_category = new MenuCategory();

        return view("{$this->directory}.create")->with(compact('menu_category', 'title_create', 'category_menu', 'option_menu', 'option_create'));
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $menu_category
     * @return \Illuminate\Http\Response
     */
    public function edit($menu_category_id)
    {
        $title_edit = $this->title_edit;
        $category_menu = $this->category_menu;
        $option_menu = $this->option_menu;
        $option_edit = $this->option_edit;

        try {
            $menu_category = MenuCategory::findOrFail($menu_category_id);
        } catch (\Exception $e) {
            return redirect()->route('menu-category');
        }

        return view("{$this->directory}.edit")->with(compact('menu_category', 'title_edit', 'category_menu', 'option_menu', 'option_edit'));
    }

}
