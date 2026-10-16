<?php

namespace App\Http\Livewire\Store\Kardex;

use App\Http\Traits\General\SortTable;
use App\Http\Traits\Store\KardexTrait;
use App\Models\MenuOption;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class AdminComponent extends Component
{
    use WithPagination;
    use SortTable;
    use KardexTrait;

    protected $allowed_actions;
    protected $table_allowed_actions;
    protected $arr_actions;

    public $product_id = "";
    public $product_name = "";
    public $date_start = "";
    public $date_end = "";

    public $rows = '1000';

    protected $listeners = [
        'search-lot' => 'render',
        'search-and-reset-pages' => 'search_and_reset_page'
    ];

    protected $queryString = [
        'product_name' => ['except' => ''],
        'date_start' => ['except' => ''],
        'date_end' => ['except' => ''],
        'rows' => ['except' => '1000'],
    ];

    public function mount()
    {
        $this->search_event = "search-kardex";
        $this->sort = "products.name";
        $this->date_start = date("Y-m-")."01";
        $this->date_end = date("Y-m-d");
    }

    public function updatingProductId($value)
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
        //$thead[]       = array('value' => '#', 'colspan' => '1', 'link' => null);
        $thead[]       = array('value' => 'Producto', 'colspan' => '1', 'link' => null);
        $thead[]       = array('value' => 'Fecha', 'colspan' => '1', 'link' => null);
        $thead[]       = array('value' => 'Nro. Doc.', 'colspan' => '1', 'link' => null);
        $thead[]       = array('value' => 'Proveedor/Cliente', 'colspan' => '1', 'link' => null);
        $thead[]       = array('value' => 'Ingreso', 'colspan' => '1', 'link' => null);
        $thead[]       = array('value' => 'Egreso', 'colspan' => '1', 'link' => null);
        $thead[]       = array('value' => 'Saldo', 'colspan' => '1', 'link' => null);
        // if(count($this->table_allowed_actions)>0){
        //     $thead[]       = array('value' => 'Operaciones', 'colspan' => count($this->table_allowed_actions), 'link' => null);
        // }

        $this->rows = (intval($this->rows) == 0) ? 10 : $this->rows;

        $list_items = [];
        $list_items = $this->search_kardex($this->product_id, $this->product_name, $this->date_start, $this->date_end, $this->sort, $this->direction, $this->rows);


        return view('livewire.store.kardex.admin-component')->with(compact('thead', 'list_items', 'arr_actions'));
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
        $menu_option = MenuOption::where('link', 'kardex')->first();
        $this->allowed_actions = Auth::user()->user_type->actions_by_menu_option($menu_option->id)->get();
        $this->table_allowed_actions = Auth::user()->user_type->actions_by_menu_option($menu_option->id)
             ->whereIn('actions.code', ['pdf', 'pdf-valorizado'])->get();

        $this->arr_actions = [];
        foreach ($this->allowed_actions as $key => $value) {
            $this->arr_actions[] = $value->code;
        }
    }
}
