<?php

namespace App\Http\Controllers\System;

use App\Http\Controllers\Controller;
use App\Models\Card;
use Illuminate\Http\Request;

class CardController extends Controller
{
    protected $directory = 'app.system.card';
    protected $title_admin = 'Tarjetas';
    protected $title_create = 'Crear tarjeta';
    protected $title_edit = 'Editar tarjeta';
    protected $category_menu = 'Sistema';
    protected $option_menu = 'Tarjetas';
    protected $option_create = 'Crear tarjeta';
    protected $option_edit = 'Editar tarjeta';

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

        $card = new Card();

        return view("{$this->directory}.create")->with(compact('card', 'title_create', 'category_menu', 'option_menu', 'option_create'));
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $card
     * @return \Illuminate\Http\Response
     */
    public function edit($card_id)
    {
        $title_edit = $this->title_edit;
        $category_menu = $this->category_menu;
        $option_menu = $this->option_menu;
        $option_edit = $this->option_edit;

        try {
            $card = Card::findOrFail($card_id);
        } catch (\Exception $e) {
            return redirect()->route('card');
        }

        return view("{$this->directory}.edit")->with(compact('card', 'title_edit', 'category_menu', 'option_menu', 'option_edit'));
    }
}
