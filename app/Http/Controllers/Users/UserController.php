<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    protected $directory = 'app.users.user';
    protected $title_admin = 'Usuarios';
    protected $title_create = 'Crear usuario';
    protected $title_edit = 'Editar usuario';
    protected $category_menu = 'Usuarios';
    protected $option_menu = 'Usuarios';
    protected $option_create = 'Crear usuario';
    protected $option_edit = 'Editar usuario';

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

        $user = new User();

        return view("{$this->directory}.create")->with(compact('user', 'title_create', 'category_menu', 'option_menu', 'option_create'));
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $user_id
     * @return \Illuminate\Http\Response
     */
    public function edit($user_id)
    {
        $title_edit = $this->title_edit;
        $category_menu = $this->category_menu;
        $option_menu = $this->option_menu;
        $option_edit = $this->option_edit;

        try {
            $user = User::findOrFail($user_id);
        } catch (\Exception $e) {
            return redirect()->route('user');
        }

        return view("{$this->directory}.edit")->with(compact('user', 'title_edit', 'category_menu', 'option_menu', 'option_edit'));
    }

    public function assign_permission($user_id)
    {
        $title_assign = $this->title_assign;
        $category_menu = $this->category_menu;
        $option_menu = $this->option_menu;
        $option_assign = $this->option_assign;

        try {
            $user = User::findOrFail($user_id);
        } catch (\Exception $e) {
            return redirect()->route('user-type');
        }

        return view("{$this->directory}.assign")->with(compact('user', 'title_assign', 'category_menu', 'option_menu', 'option_assign'));
    }
}
