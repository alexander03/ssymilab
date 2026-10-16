<?php

namespace App\Http\Controllers\Sale;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Barryvdh\DomPDF\Facade as PDF;
use Elibyy\TCPDF\Facades\TCPDF;
use App\Librerias\Libreria;

class AnularfacturaController extends Controller
{
    protected $directory = 'app.sale.anularfactura';
    protected $title_admin = 'Anular Facturas';
    protected $category_menu = 'Anular Facturas';
    protected $option_menu = 'Pedido';
    protected $option_create = 'Crear pedido';
    protected $option_show = 'Ver pedido';
    protected $option_edit = 'Editar pedido';

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

    public function index2()
    {
        $title_admin = "Comprobantes";
        $category_menu = "Comprobantes";
        $option_menu = "Comprobantes";

        return view("app.sale.comprobante.admin")->with(compact('title_admin', 'category_menu', 'option_menu'));
    }

    public function indexNota()
    {
        $title_admin = "Comprobantes";
        $category_menu = "Comprobantes";
        $option_menu = "Comprobantes";

        return view("app.sale.anularnota.admin")->with(compact('title_admin', 'category_menu', 'option_menu'));
    }
}
