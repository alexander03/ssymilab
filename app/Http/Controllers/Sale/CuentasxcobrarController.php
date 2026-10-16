<?php

namespace App\Http\Controllers\Sale;

use App\Http\Controllers\Controller;
use App\Http\Traits\Sale\SaleTrait;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade as PDF;

class CuentasxcobrarController extends Controller
{
    use SaleTrait;

    protected $directory = 'app.sale.cuentasxcobrar';
    protected $title_admin = 'Cuentas por cobrar';
    protected $category_menu = 'Ventas';
    protected $option_menu = 'Cuentas por cobrar';

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
     * Show the form for editing the specified resource.
     *
     * @param  int $proforma
     * @return \Illuminate\Http\Response
     */
    public function pdf(Request $request)
    {
        $lista = $this->search_payments($request->input('branch_office_id'),$request->input('date_start'),$request->input('date_end'),null,$request->input('client'), 4, 'document_payments.id', 'desc', 100000,$request->input('status_sale'));
        $pdf = PDF::loadView("{$this->directory}.pdf", compact('lista'));
        return $pdf->stream("reporte-cuotas-por-cobrar.pdf");
    }

    public function pdf2(Request $request)
    {
        $lista = $this->search_sales($request->input('date_start'),$request->input('date_end'), null, null, $request->input('client'), null, 'P',null, 'documents.person_id', 'desc', 1000000);
        $pdf = PDF::loadView("{$this->directory}.pdf2", compact('lista'))->setPaper('A4');
        return $pdf->stream("reporte-cuotas-por-cobrar-vendedor.pdf");
    }

    public function excel(Request $request)
    {
        $lista = $this->search_payments($request->input('branch_office_id'),$request->input('date_start'),$request->input('date_end'),null,$request->input('client'), 4, 'document_payments.id', 'desc', 100000,$request->input('status_sale'));
        $html = view("{$this->directory}.pdf")->with(compact('lista'))->render();
        return view("{$this->directory}.excel")->with(compact('html'));
    }
}
