<?php

namespace App\Http\Livewire\Report\SaleCheckout;

use App\Http\Traits\General\SortTable;
use App\Http\Traits\Sale\SaleCheckoutTrait;
use App\Models\Checkout;
use App\Models\CheckoutDetail;
use App\Models\Currency;
use App\Models\MenuOption;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class AdminComponent extends Component
{
    use WithPagination;
    use SortTable;
    use SaleCheckoutTrait;

    protected $allowed_actions;
    protected $table_allowed_actions;
    protected $arr_actions;

    public $checkout_id;
    public $checkout_detail_id;
    public $checkout_details;
    public $resume;
    public $band = false;
    public $rows = '10';

    protected $listeners = [
        'search-sale-checkout' => 'render',
        'search-and-reset-pages' => 'search_and_reset_page'
    ];

    protected $queryString = [
        'rows' => ['except' => '10']
    ];

    public function mount()
    {
        $this->search_event = "search-sale-checkout";
        $this->sort = "documents.id";
        $this->direction = "desc";

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
    }

    public function updatingRows($value)
    {
        $this->resetPage();
    }


    public function render()
    {
        $this->load_actions();
        $arr_actions = $this->arr_actions;

        $thead         = array();
        $thead[]       = array('value' => '#', 'colspan' => '1', 'link' => null);
        $thead[]       = array('value' => 'Fecha', 'colspan' => '1', 'link' => null);
        $thead[]       = array('value' => 'Número', 'colspan' => '1', 'link' => null);
        $thead[]       = array('value' => 'Concepto', 'colspan' => '1', 'link' => null);
        $thead[]       = array('value' => 'Persona', 'colspan' => '1', 'link' => null);
        $thead[]       = array('value' => 'Moneda', 'colspan' => '1', 'link' => null);
        $thead[]       = array('value' => 'Efectivo', 'colspan' => '1', 'link' => null);
        $thead[]       = array('value' => 'Tarjeta', 'colspan' => '1', 'link' => null);
        $thead[]       = array('value' => 'Depósito', 'colspan' => '1', 'link' => null);
        $thead[]       = array('value' => 'Tipo Doc.', 'colspan' => '1', 'link' => null);
        $thead[]       = array('value' => 'Nro. Doc.', 'colspan' => '1', 'link' => null);
        $thead[]       = array('value' => 'Fecha Doc.', 'colspan' => '1', 'link' => null);
        $thead[]       = array('value' => 'Comentario', 'colspan' => '1', 'link' => null);
        
        $this->rows = (intval($this->rows) == 0) ? 10 : $this->rows;

        $user = Auth::user();

        $checkouts = Checkout::orderBy('name','asc')->get();
        
        $in_list = false;
        foreach ($checkouts as $key => $value) {
            if($value->id == $this->checkout_id){
                $in_list = true;
            }
        }
        if(!$in_list && count($checkouts) > 0){
            $this->checkout_id = $checkouts[0]->id;
        }

        $checkout = Checkout::find($this->checkout_id);

        $this->checkout_details = CheckoutDetail::where('checkout_id','=',$this->checkout_id)->orderBy('open_date_time','desc')->get();
        if(count($this->checkout_details)>0){
            if($this->band==false){
                $this->checkout_detail_id = $this->checkout_details[0]->id;
                $this->band = true;
            }
        }else{
            $this->checkout_detail_id = 0;
        }
        $list_items = [];
        $list_items = $this->report_sale_checkouts($this->checkout_id,$this->checkout_detail_id, null, null, null, $this->sort, $this->direction, $this->rows);

        $this->calculate_resume($this->checkout_id, $this->sort, $this->direction);


        return view('livewire.report.sale-checkout.admin-component')->with(compact('checkout', 'checkouts','thead', 'list_items', 'arr_actions'));
    }

    public function paginationView()
    {
        return 'components.general.pagination';
    }

    /**
     * Funciones de actualizacion de fields
     */
    public function updatedCheckout_id($value)
    {
        $this->checkout_details = CheckoutDetail::where('checkout_id','=',$value)->orderBy('open_date_time','desc')->get();
        if(count($this->checkout_details)>0){
            $this->checkout_detail_id = $this->checkout_details[0]->id;
            $this->band = false;
        }else{
            $this->checkout_detail_id = 0;
        }
    }

    public function search_and_reset_page()
    {
        $this->resetPage();
    }

    protected function load_actions(){
        $menu_option = MenuOption::where('link', 'sale-checkout')->first();
        $this->allowed_actions = Auth::user()->user_type->actions_by_menu_option($menu_option->id)->get();
        $this->arr_actions = [];
    }
}
