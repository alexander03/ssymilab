<?php

namespace App\Http\Livewire\Report\Exchange;

use App\Http\Traits\General\SortTable;
use App\Http\Traits\Maintenance\ExchangeTrait;
use App\Models\MenuOption;
use App\Models\Purchase;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use App\Models\BranchOffice;
use Livewire\WithPagination;

class AdminComponent extends Component
{
    use WithPagination;
    use SortTable;
    use ExchangeTrait;

    protected $allowed_actions;
    protected $table_allowed_actions;
    protected $arr_actions;

    public $date_start, $date_end;

    public $types, $rows = '10';

    protected $listeners = [
        'search-lot' => 'render',
        'search-and-reset-pages' => 'search_and_reset_page'
    ];

    protected $queryString = [
        'rows' => ['except' => '10']
    ];

    public function mount()
    {
        $this->date_start = date('Y-m-d');
        $this->date_end = date('Y-m-d');
        $this->sort = "exchange_rates.date";
        $this->direction = "asc";
        $this->search_event = "search-exchange";
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
        $thead[]       = array('value' => 'Fecha', 'colspan' => '1', 'link' => 'exchange_rates.date');
        $thead[]       = array('value' => 'Moneda Base', 'colspan' => '1', 'link' => 'exchange_rates.base_currency_id');
        $thead[]       = array('value' => 'Moneda', 'colspan' => '1', 'link' => 'exchange_rates.currency_id');
        $thead[]       = array('value' => 'Valor', 'colspan' => '1', 'link' => 'exchange_rates.value');
        
        $list_items = [];
        $list_items = $this->search_exhange($this->date_start,$this->date_end, $this->sort, $this->direction, $this->rows);


        return view('livewire.report.exchange.admin-component')->with(compact('thead', 'list_items', 'arr_actions'));
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
        $menu_option = MenuOption::where('link', 'purchase')->first();
        $this->allowed_actions = Auth::user()->user_type->actions_by_menu_option($menu_option->id)->get();
        // $this->table_allowed_actions = Auth::user()->user_type->actions_by_menu_option($menu_option->id)
        //     ->whereIn('actions.code', ['edit_lot', 'delete_lot'])->get();

        $this->arr_actions = [];
        foreach ($this->allowed_actions as $key => $value) {
            $this->arr_actions[] = $value->code;
        }
    }
}

