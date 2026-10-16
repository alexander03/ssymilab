<?php

namespace App\Http\Controllers\System;

use App\Http\Controllers\Controller;
use App\Models\Checkout;
use Illuminate\Http\Request;

class CheckoutController extends Controller
{
    protected $directory = 'app.system.checkout';
    protected $title_admin = 'Cajas';
    protected $title_create = 'Crear caja';
    protected $title_edit = 'Editar caja';
    protected $category_menu = 'Sistema';
    protected $option_menu = 'Cajas';
    protected $option_create = 'Crear caja';
    protected $option_edit = 'Editar caja';

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

        $checkout = new Checkout();

        return view("{$this->directory}.create")->with(compact('checkout', 'title_create', 'category_menu', 'option_menu', 'option_create'));
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $checkout
     * @return \Illuminate\Http\Response
     */
    public function edit($checkout_id)
    {
        $title_edit = $this->title_edit;
        $category_menu = $this->category_menu;
        $option_menu = $this->option_menu;
        $option_edit = $this->option_edit;

        try {
            $checkout = Checkout::findOrFail($checkout_id);
        } catch (\Exception $e) {
            return redirect()->route('checkout');
        }

        return view("{$this->directory}.edit")->with(compact('checkout', 'title_edit', 'category_menu', 'option_menu', 'option_edit'));
    }
}
