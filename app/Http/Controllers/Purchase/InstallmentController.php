<?php

namespace App\Http\Controllers\Purchase;

use App\Http\Controllers\Controller;
use App\Http\Traits\Purchase\InstallmentTrait;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade as PDF;

class InstallmentController extends Controller
{
    use InstallmentTrait;

    protected $directory = 'app.purchase.installment';
    protected $title_admin = 'Cuentas por pagar';
    protected $category_menu = 'Compras';
    protected $option_menu = 'Cuentas por pagar';

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
    public function pdf()
    {
        $lista = $this->search_installments(null,null,null,null, 1, 'document_payments.id', 'desc', 100000);
        $pdf = PDF::loadView("{$this->directory}.pdf", compact('lista'))->setPaper('A4', 'Landscape');
        return $pdf->stream("reporte-cuotas-por-pagar.pdf");
    }

    public function excel(Request $request)
    {
        $lista = $this->search_installments($request->input('date_start'),$request->input('date_end'),$request->input('number'),$request->input('supplier'), 1, 'document_payments.id', 'desc', 100000);
        return view("{$this->directory}.excel")->with(compact('lista'));
    }
}
