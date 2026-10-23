<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Http\Traits\Report\ProductClientTrait;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade as PDF;

class ReportProductSaleController extends Controller
{
    use ProductClientTrait;

    protected $directory = 'app.report.product-sale';
    protected $title_admin = 'Ventas por Producto';
    protected $category_menu = 'Reporte';
    protected $option_menu = 'Ventas por Producto';

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
        return $pdf->stream("reporte-ventas-por-producto.pdf");
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
        $brands = $this->search_brand_sales(...$filters);
        $details = $this->search_brand_sale_details($brands->pluck('brand_id')->toArray(), ...$filters);
        $totals = $this->search_sales_total_by_unit(...$filters);
        $date_start = $request->input('date_start');
        $date_end = $request->input('date_end');

        return compact('brands', 'details', 'totals', 'date_start', 'date_end');
    }
}
