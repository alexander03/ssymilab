<?php

namespace App\Http\Livewire\Sale\SaleCheckout;

use App\Http\Traits\General\SortTable;
use App\Http\Traits\Sale\SaleCheckoutTrait;
use App\Models\Checkout;
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
    public $resume;
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
        if(count($this->table_allowed_actions)>0){
            $thead[]       = array('value' => 'Operaciones', 'colspan' => count($this->table_allowed_actions), 'link' => null);
        }

        $this->rows = (intval($this->rows) == 0) ? 10 : $this->rows;

        $user = Auth::user();
        $checkouts = $user->checkouts;

        if($user->user_type_id==1){//ADMIN
            $checkouts = Checkout::orderBy('name','asc')->get();
        }elseif($user->id==5 || $user->id==12){//FACTURACION
            $checkouts = Checkout::whereIn('id',[5,6,7])->orderBy('name','asc')->get();
        }else{
            if(count($checkouts) == 0){
                $checkouts = Checkout::closed();
            }
        }
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

        $list_items = [];
        $list_items = $this->search_sale_checkouts($this->checkout_id, null, null, null, $this->sort, $this->direction, $this->rows);

        $this->calculate_resume($this->checkout_id, $this->sort, $this->direction);


        return view('livewire.sale.sale-checkout.admin-component')->with(compact('checkout', 'checkouts','thead', 'list_items', 'arr_actions'));
    }

    public function paginationView()
    {
        return 'components.general.pagination';
    }

    public function opening()
    {
        redirect()->route('opening', ["checkout" => $this->checkout_id]);
    }

    public function closing()
    {
        redirect()->route('closing', ["checkout" => $this->checkout_id]);
    }

    public function payment()
    {
        redirect()->route('payment', ["checkout" => $this->checkout_id]);
    }

    public function search_and_reset_page()
    {
        $this->resetPage();
    }

    protected function load_actions(){
        $menu_option = MenuOption::where('link', 'sale-checkout')->first();
        $this->allowed_actions = Auth::user()->user_type->actions_by_menu_option($menu_option->id)->get();
        $this->table_allowed_actions = Auth::user()->user_type->actions_by_menu_option($menu_option->id)
            ->whereIn('actions.code', ['delete_sale_checkout'])->get();

        $this->arr_actions = [];
        foreach ($this->allowed_actions as $key => $value) {
            $this->arr_actions[] = $value->code;
        }
    }
}
