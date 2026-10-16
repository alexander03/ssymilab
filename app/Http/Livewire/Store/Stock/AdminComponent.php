<?php

namespace App\Http\Livewire\Store\Stock;

use App\Http\Traits\General\SortTable;
use App\Http\Traits\General\StockTrait;
use App\Models\MenuOption;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use App\Models\Brand;
use Livewire\WithPagination;

class AdminComponent extends Component
{
    use WithPagination;
    use SortTable;
    use StockTrait;

    protected $allowed_actions;
    protected $table_allowed_actions;
    protected $arr_actions;

    public $product_id = "";
    public $product_name = "";
    public $brand_id = "";
    public $type = "";

    public $types,$rows = '10';

    protected $listeners = [
        'search-lot' => 'render',
        'search-and-reset-pages' => 'search_and_reset_page'
    ];

    protected $queryString = [
        'product_name' => ['except' => ''],
        'rows' => ['except' => '10']
    ];

    public function mount()
    {
        $this->search_event = "search-stock";
        $this->types = ["R" => "REACTIVOS", "M" => "MAQUINARIA", "S"=>"SERVICIOS", "E"=>"REPUESTOS", "C"=>"COMPUTO", "O"=>"CONSUMIBLE"];
        $this->brands = Brand::orderBy('name')->get();
        $this->sort = "products.name";
    }

    public function updatingProductId($value)
    {
        $this->resetPage();
    }

    public function updatingProductName($value)
    {
        $this->resetPage();
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
        $thead[]       = array('value' => 'Código', 'colspan' => '1', 'link' => 'products.code');
        $thead[]       = array('value' => 'Producto', 'colspan' => '1', 'link' => 'products.name');
        if($this->type=="" || $this->type=="R"){
            $thead[]       = array('value' => 'Presentacion', 'colspan' => '1', 'link' => 'products.presentation');
        }
        if($this->type=="" || $this->type=="M"){
            $thead[]       = array('value' => 'Modelo', 'colspan' => '1', 'link' => 'products.model');
        }
        $thead[]       = array('value' => 'Marca', 'colspan' => '1', 'link' => 'brands.name');
        $thead[]       = array('value' => 'Cantidad', 'colspan' => '1', 'link' => 'stock.real');
        $thead[]       = array('value' => 'Separado', 'colspan' => '1', 'link' => 'stock.separated');
        $thead[]       = array('value' => 'Disponible', 'colspan' => '1', 'link' => 'stock.available');
        // if(count($this->table_allowed_actions)>0){
        //     $thead[]       = array('value' => 'Operaciones', 'colspan' => count($this->table_allowed_actions), 'link' => null);
        // }

        $this->rows = (intval($this->rows) == 0) ? 10 : $this->rows;

        $list_items = [];
        $list_items = $this->search_stock($this->product_id, $this->product_name, $this->type, $this->brand_id, $this->sort, $this->direction, $this->rows);


        return view('livewire.store.stock.admin-component')->with(compact('thead', 'list_items', 'arr_actions'));
    }

    public function paginationView()
    {
        return 'components.general.pagination';
    }

    public function search_and_reset_page()
    {
        $this->resetPage();
    }

    protected function load_actions(){
        $menu_option = MenuOption::where('link', 'stock')->first();
        $this->allowed_actions = Auth::user()->user_type->actions_by_menu_option($menu_option->id)->get();
        // $this->table_allowed_actions = Auth::user()->user_type->actions_by_menu_option($menu_option->id)
        //     ->whereIn('actions.code', ['edit_lot', 'delete_lot'])->get();

        $this->arr_actions = [];
        foreach ($this->allowed_actions as $key => $value) {
            $this->arr_actions[] = $value->code;
        }
    }
}

