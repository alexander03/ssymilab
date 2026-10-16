<?php

namespace App\Http\Controllers\Equipment;

use App\Http\Controllers\Controller;
use App\Models\Maintenance;
use App\Models\Product;
use Illuminate\Http\Request;

class PreventativeController extends Controller
{
    protected $directory = 'app.equipment.preventative';
    protected $title_admin = 'Cronograma';
    protected $title_create = 'Crear Cronograma';
    protected $title_edit = 'Editar Cronograma';
    protected $category_menu = 'Equipos';
    protected $option_menu = 'Cronograma';
    protected $option_create = 'Crear Cronograma';
    protected $option_edit = 'Editar Cronograma';

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

        $maintenance = new Maintenance();

        return view("{$this->directory}.create")->with(compact('maintenance', 'title_create', 'category_menu', 'option_menu', 'option_create'));
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $product
     * @return \Illuminate\Http\Response
     */
    public function edit($product_id)
    {
        $title_edit = $this->title_edit;
        $category_menu = $this->category_menu;
        $option_menu = $this->option_menu;
        $option_edit = $this->option_edit;

        try {
            $product = Product::findOrFail($product_id);
            $maintenance = Maintenance::where('product_id', $product_id)->first();
            if(!$maintenance){
                $maintenance = new Maintenance();
            }
        } catch (\Exception $e) {
            return redirect()->route('preventative');
        }

        return view("{$this->directory}.edit")->with(compact('maintenance', 'product', 'title_edit', 'category_menu', 'option_menu', 'option_edit'));
    }

}
