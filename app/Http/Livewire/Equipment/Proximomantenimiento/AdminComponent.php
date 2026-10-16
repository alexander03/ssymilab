<?php

namespace App\Http\Livewire\Equipment\Proximomantenimiento;

use App\Http\Traits\General\SortTable;
use App\Http\Traits\Equipment\MaintenanceTrait;
use App\Models\MenuOption;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class AdminComponent extends Component
{
    use WithPagination;
    use SortTable;
    use MaintenanceTrait;

    protected $allowed_actions;
    protected $arr_actions;

    public $date_start, $date_end, $product_name, $serie;

    public $rows = '10';

    protected $listeners = [
        'search-proximomantenimiento' => 'render',
        'search-and-reset-pages' => 'search_and_reset_page'
    ];

    protected $queryString = [
        'date_start' => ['except' => ''],
        'date_end' => ['except' => ''],
        'rows' => ['except' => '10']
    ];

    public function mount()
    {
        $this->date_start = date('Y-').'01-01';
        $this->date_end = date('Y-m-d');
        $this->sort = "schedules.id";
        $this->direction = "desc";
        $this->search_event = "search-proximomantenimiento";
    }

    public function updatingDateStart($value)
    {
        $this->resetPage();
    }

    public function updatingDateEnd($value)
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
        $thead[]       = array('value' => 'PRODUCTO', 'colspan' => '1', 'link' => 'products.name');
        $thead[]       = array('value' => 'SERIE', 'colspan' => '1', 'link' => 'product_series.serie');
        $thead[]       = array('value' => 'CLIENTE', 'colspan' => '1', 'link' => 'schedules.customer_name');
        $thead[]       = array('value' => 'LUGAR', 'colspan' => '1', 'link' => 'schedules.product_place');
        $thead[]       = array('value' => 'UBICACION', 'colspan' => '1', 'link' => 'schedules.product_location');
        $thead[]       = array('value' => 'ESTADO', 'colspan' => '1', 'link' => 'schedules.product_status');
        $thead[]       = array('value' => 'ULTIMO MANT.', 'colspan' => '1', 'link' => null);
        $thead[]       = array('value' => 'PROXIMO MANT.', 'colspan' => '1', 'link' => null);

        $this->rows = (intval($this->rows) == 0) ? 10 : $this->rows;

        $list_items = [];
        $list_items = $this->search_proximomantenimiento($this->date_start,$this->date_end, $this->product_name, $this->serie, $this->sort, $this->direction, $this->rows);

        return view('livewire.equipment.proximomantenimiento.admin-component')->with(compact('thead', 'list_items', 'arr_actions'));
    }

    public function paginationView()
    {
        return 'components.general.pagination';
    }

    public function create_route()
    {
        redirect()->route('create-installment');
    }

    public function search_and_reset_page()
    {
        $this->resetPage();
    }

    protected function load_actions(){
        $menu_option = MenuOption::where('link', 'debts-to-pay')->first();
        /*$this->allowed_actions = Auth::user()->user_type->actions_by_menu_option($menu_option->id)->get();
        $this->arr_actions = [];
        foreach ($this->allowed_actions as $key => $value) {
            $this->arr_actions[] = $value->code;
        }*/
    }
}
