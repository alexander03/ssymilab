<?php

namespace App\Http\Controllers\System;

use App\Http\Controllers\Controller;
use App\Models\TransferMethod;
use Illuminate\Http\Request;

class TransferMethodController extends Controller
{
    protected $directory = 'app.system.transfer-method';
    protected $title_admin = 'Métodos de Transferencia';
    protected $title_create = 'Crear método de transferencia';
    protected $title_edit = 'Editar método de transferencia';
    protected $category_menu = 'Sistema';
    protected $option_menu = 'Métodos de Transferencia';
    protected $option_create = 'Crear método de transferencia';
    protected $option_edit = 'Editar método de transferencia';

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

        $transfer_method = new TransferMethod();

        return view("{$this->directory}.create")->with(compact('transfer_method', 'title_create', 'category_menu', 'option_menu', 'option_create'));
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $transfer_method
     * @return \Illuminate\Http\Response
     */
    public function edit($transfer_method_id)
    {
        $title_edit = $this->title_edit;
        $category_menu = $this->category_menu;
        $option_menu = $this->option_menu;
        $option_edit = $this->option_edit;

        try {
            $transfer_method = TransferMethod::findOrFail($transfer_method_id);
        } catch (\Exception $e) {
            return redirect()->route('transfer-method');
        }

        return view("{$this->directory}.edit")->with(compact('transfer_method', 'title_edit', 'category_menu', 'option_menu', 'option_edit'));
    }
}
