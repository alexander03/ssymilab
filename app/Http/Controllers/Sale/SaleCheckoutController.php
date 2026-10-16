<?php

namespace App\Http\Controllers\Sale;

use App\Http\Controllers\Controller;
use App\Http\Traits\Sale\SaleCheckoutTrait;
use App\Models\Checkout;
use App\Models\Currency;
use App\Models\CheckoutDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class SaleCheckoutController extends Controller
{
    use SaleCheckoutTrait;

    protected $directory = 'app.sale.sale-checkout';
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

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $checkout_document
     * @return \Illuminate\Http\Response
     */
    public function opening($checkout_id)
    {
        $title_edit = "Apertura de caja";
        $category_menu = $this->category_menu;
        $option_menu = $this->option_menu;
        $option_edit = "Apertura";

        try {
            $checkout = Checkout::findOrFail($checkout_id);
            if($checkout->status != "C"){
                Session::flash('warning', 'La caja no se encuentra cerrada');
                return redirect()->route('sale-checkout');
            }
        } catch (\Exception $e) {
            return redirect()->route('sale-checkout');
        }

        return view("{$this->directory}.opening")->with(compact('checkout', 'title_edit', 'category_menu', 'option_menu', 'option_edit'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $checkout_document
     * @return \Illuminate\Http\Response
     */
    public function closing($checkout_id)
    {
        $title_edit = "Cierre de caja";
        $category_menu = $this->category_menu;
        $option_menu = $this->option_menu;
        $option_edit = "Cierre";

        try {
            $checkout = Checkout::findOrFail($checkout_id);
            if($checkout->status != "O"){
                Session::flash('warning', 'La caja no se encuentra abierta');
                return redirect()->route('sale-checkout');
            }
        } catch (\Exception $e) {
            return redirect()->route('sale-checkout');
        }

        return view("{$this->directory}.closing")->with(compact('checkout', 'title_edit', 'category_menu', 'option_menu', 'option_edit'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $checkout_document
     * @return \Illuminate\Http\Response
     */
    public function payment($checkout_id)
    {
        $title_edit = "Crear Pago";
        $category_menu = $this->category_menu;
        $option_menu = $this->option_menu;
        $option_edit = "Pago";

        try {
            $checkout = Checkout::findOrFail($checkout_id);
            if($checkout->status != "O"){
                Session::flash('warning', 'La caja no se encuentra abierta');
                return redirect()->route('sale-checkout');
            }
        } catch (\Exception $e) {
            return redirect()->route('sale-checkout');
        }

        return view("{$this->directory}.payment")->with(compact('checkout', 'title_edit', 'category_menu', 'option_menu', 'option_edit'));
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
        $lista = $this->search_sale_checkouts($request->input('checkout_id'), null, null, null, 'documents.id', 'asc', 1000000);
        
        $this->calculate_resume($request->input('checkout_id'), 'documents.id', 'asc');

        $resume = $this->resume;
        
        return view("{$this->directory}.excel")->with(compact('lista','resume'));
    }
}
