<?php

namespace App\Http\Controllers\Maintenance;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade as PDF;
use App\Http\Traits\Maintenance\CategoryTrait;

class CategoryController extends Controller
{
    use CategoryTrait;

    protected $directory = 'app.maintenance.category';
    protected $title_admin = 'Categorías';
    protected $title_create = 'Crear categoría';
    protected $title_edit = 'Editar categoría';
    protected $category_menu = 'Mantenimiento';
    protected $option_menu = 'Categorías';
    protected $option_create = 'Crear categoría';
    protected $option_edit = 'Editar categoría';

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

        $category = new Category();

        return view("{$this->directory}.create")->with(compact('category', 'title_create', 'category_menu', 'option_menu', 'option_create'));
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $category
     * @return \Illuminate\Http\Response
     */
    public function edit($category_id)
    {
        $title_edit = $this->title_edit;
        $category_menu = $this->category_menu;
        $option_menu = $this->option_menu;
        $option_edit = $this->option_edit;

        try {
            $category = Category::findOrFail($category_id);
        } catch (\Exception $e) {
            return redirect()->route('category');
        }

        return view("{$this->directory}.edit")->with(compact('category', 'title_edit', 'category_menu', 'option_menu', 'option_edit'));
    }

    public function pdf($id,Request $request)
    {
        $lista = $this->search_categories($request->input('name'), 'name', 'asc', 10000);
        $pdf = PDF::loadView("{$this->directory}.pdf", compact('lista'))->setPaper('A4', 'Portrait');
        return $pdf->stream("category.pdf");
    }
}
