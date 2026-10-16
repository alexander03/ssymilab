<?php

namespace App\Http\Controllers\System;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\MenuOption;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CompanyController extends Controller
{
    protected $directory = 'app.system.company';
    protected $title_admin = 'Empresa';
    protected $title_edit = 'Editar empresa';
    protected $category_menu = 'Sistema';
    protected $option_menu = 'Empresa';
    protected $option_edit = 'Editar empresa';

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

        $user = Auth::user();

        $menu_option = MenuOption::where('link', 'company')->first();
        $allowed_actions = $user->user_type->actions_by_menu_option($menu_option->id)->get();

        $arr_actions = [];
        foreach ($allowed_actions as $key => $value) {
            $arr_actions[] = $value->code;
        }

        $company = $user->branch_office->company;

        return view("{$this->directory}.admin")->with(compact('title_admin', 'category_menu', 'option_menu', 'company', 'arr_actions'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $company
     * @return \Illuminate\Http\Response
     */
    public function edit($company_id)
    {
        $title_edit = $this->title_edit;
        $category_menu = $this->category_menu;
        $option_menu = $this->option_menu;
        $option_edit = $this->option_edit;

        try {
            $company = Company::findOrFail($company_id);
        } catch (\Exception $e) {
            return redirect()->route('company');
        }

        return view("{$this->directory}.edit")->with(compact('company', 'title_edit', 'category_menu', 'option_menu', 'option_edit'));
    }

}
