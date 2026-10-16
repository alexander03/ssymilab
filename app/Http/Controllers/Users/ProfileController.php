<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    protected $directory = 'app.users.profile';
    protected $title_admin = 'Perfil';
    protected $category_menu = 'Usuarios';
    protected $option_menu = 'Perfil';

    public function index(){

        $title_admin = $this->title_admin;
        $category_menu = $this->category_menu;
        $option_menu = $this->option_menu;

        return view("{$this->directory}.admin")->with(compact('title_admin', 'category_menu', 'option_menu'));

    }
}
