<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Http\Traits\Sale\SaleCheckoutTrait;
use App\Models\Checkout;
use App\Models\Currency;
use App\Models\CheckoutDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class ReportSaleCheckoutController extends Controller
{
    use SaleCheckoutTrait;

    protected $directory = 'app.report.sale-checkout';
    protected $title_admin = 'Caja';
    protected $title_show = 'Ver caja';
    protected $title_create = 'Crear pago';
    protected $category_menu = 'Ventas';
    protected $option_menu = 'Caja';
    protected $option_create = 'Crear pago';

    public $resume;

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


    public function excel(Request $request)
    {
        $currencies = Currency::order_currency()->get();
        foreach ($currencies as $key => $value) {
            $this->resume[] = [
                "currency_id" => $value->id,
                "currency_code" => $value->code,
                "currency_name" => $value->name,
                "currency_symbol" => $value->symbol,
                "currency_base" => $value->base,
                "total_apertura" => 0.00,
                "total_apertura_efectivo" => 0.00,
                "total_apertura_tarjeta" => 0.00,
                "total_apertura_deposito" => 0.00,
                "total_ventas" => 0.00,
                "total_ventas_efectivo" => 0.00,
                "total_ventas_tarjeta" => 0.00,
                "total_ventas_deposito" => 0.00,
                "total_varios" => 0.00,
                "total_varios_efectivo" => 0.00,
                "total_varios_tarjeta" => 0.00,
                "total_varios_deposito" => 0.00,
                "total_egresos" => 0.00,
                "total_egresos_efectivo" => 0.00,
                "total_egresos_tarjeta" => 0.00,
                "total_egresos_deposito" => 0.00,
                "total_saldo" => 0.00,
                "total_saldo_efectivo" => 0.00,
                "total_saldo_tarjeta" => 0.00,
                "total_saldo_deposito" => 0.00,
            ];
        }
        $lista = $this->report_sale_checkouts($request->input('checkout_id'), $request->input('checkout_detail_id'), null, null, null, 'documents.id', 'asc', 1000000);
        
        $this->calculate_resume($request->input('checkout_id'), 'documents.id', 'asc');

        $resume = $this->resume;
        
        return view("{$this->directory}.excel")->with(compact('lista','resume'));
    }
}
