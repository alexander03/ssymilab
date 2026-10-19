<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Http\Traits\Report\ProductClientTrait;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade as PDF;

class ReportProductClientController extends Controller
{
    use ProductClientTrait;

    protected $directory = 'app.report.product-client';
    protected $title_admin = 'Productos por Cliente';
    protected $category_menu = 'Reporte';
    protected $option_menu = 'Productos por Cliente';

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

    public function pdf(Request $request)
    {
        $data = $this->report_data($request);
        $pdf = PDF::loadView("{$this->directory}.pdf", $data)->setPaper('A4', 'Portrait');
        return $pdf->stream("reporte-productos-por-cliente.pdf");
    }

    public function excel(Request $request)
    {
        $data = $this->report_data($request);
        $html = view("{$this->directory}.pdf")->with($data)->render();
        return view("{$this->directory}.excel")->with(compact('html'));
    }

    private function report_data(Request $request)
    {
        $filters = [
            $request->input('branch_office_id'),
            $request->input('date_start'),
            $request->input('date_end'),
            $request->input('client'),
            $request->input('brand_id'),
            $request->input('product'),
            $request->input('zone_id'),
        ];
        $clients = $this->search_product_clients(...$filters);
        $details = $this->search_product_client_details($clients->pluck('client_id')->toArray(), ...$filters);
        $date_start = $request->input('date_start');
        $date_end = $request->input('date_end');

        return compact('clients', 'details', 'date_start', 'date_end');
    }
}
