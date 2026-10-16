<?php

namespace App\Http\Livewire\Equipment\Maintenance;

use App\Http\Traits\Equipment\MaintenanceTrait;
use App\Http\Traits\General\SortTable;
use Livewire\WithPagination;
use Livewire\Component;
use App\Models\MenuOption;
use Illuminate\Support\Facades\Auth;

class AdminComponent extends Component
{
    use WithPagination;
    use SortTable;
    use MaintenanceTrait;

    protected $allowed_actions;
    protected $table_allowed_actions;
    protected $arr_actions;

    public $product_name = "";
    public $product_serie = "";
    public $customer = "";
    public $date_start = "";
    public $date_end = "";

    public $rows = '10';

    protected $listeners = [
        'search-preventative' => 'render', 
        'search-and-reset-pages' => 'search_and_reset_page'
    ];

    protected $queryString = [
        'product_name' => ['except' => ''],
        'rows' => ['except' => '10'],
    ];

    public function mount()
    {
        $this->sort = "product_maintenances.done_date";
        $this->direction = "desc";
        $this->search_event = "search-maintenance";
        $this->date_start = date("Y-m")."-01";
    }

    public function updatingSearchProductName($value)
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
        $thead[]       = array('value' => 'Fecha Realizada', 'colspan' => '1', 'link' => 'product_maintenances.done_date');
        $thead[]       = array('value' => 'Tipo', 'colspan' => '1', 'link' => 'product_maintenances.type');
        $thead[]       = array('value' => 'Producto', 'colspan' => '1', 'link' => 'products.name');
        $thead[]       = array('value' => 'Serie', 'colspan' => '1', 'link' => 'product_series.serie');
        $thead[]       = array('value' => 'Cliente', 'colspan' => '1', 'link' => 'schedules.customer_name');
        $thead[]       = array('value' => 'Responsable Ing.', 'colspan' => '1', 'link' => 'product_maintenances.head_name');
        $thead[]       = array('value' => 'Observaciones', 'colspan' => '1', 'link' => 'product_maintenances.observations');
        //$thead[]       = array('value' => 'Situacion', 'colspan' => '1', 'link' => 'product_maintenances.satus');
        //$thead[]       = array('value' => 'Prox. Mantenimiento', 'colspan' => '1', 'link' => null);
        if(count($this->table_allowed_actions)>0){
            $thead[]       = array('value' => 'Operaciones', 'colspan' => count($this->table_allowed_actions), 'link' => null);
        }

        $this->rows = (intval($this->rows) == 0) ? 10 : $this->rows;

        $list_items = [];
        $list_items = $this->search_maintenance($this->date_start, $this->date_end,$this->product_name, $this->product_serie, $this->customer, $this->sort, $this->direction, $this->rows);


        return view('livewire.equipment.maintenance.admin-component')->with(compact('thead', 'list_items', 'arr_actions'));
    }

    public function paginationView()
    {
        return 'components.general.pagination';
    }

    public function create_route()
    {
        redirect()->route('create-maintenance');
    }

    public function search_and_reset_page()
    {
        $this->resetPage();
    }

    protected function load_actions(){
        $menu_option = MenuOption::where('link', 'maintenance')->first();
        $this->allowed_actions = Auth::user()->user_type->actions_by_menu_option($menu_option->id)->get();
        $this->table_allowed_actions = Auth::user()->user_type->actions_by_menu_option($menu_option->id)
            ->whereIn('actions.code', ['edit_maintenance','show_maintenance','delete_maintenance'])->get();

        $this->arr_actions = [];
        foreach ($this->allowed_actions as $key => $value) {
            $this->arr_actions[] = $value->code;
        }
    }
}
