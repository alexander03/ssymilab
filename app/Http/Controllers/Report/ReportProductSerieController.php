<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ReportProductSerieController extends Controller
{
    protected $directory = 'app.report.product-serie';
    protected $title_admin = 'Series';
    protected $category_menu = 'Almacén';
    protected $option_menu = 'Series';

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
}
