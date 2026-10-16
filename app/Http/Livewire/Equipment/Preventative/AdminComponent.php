<?php

namespace App\Http\Livewire\Equipment\Preventative;

use App\Http\Traits\Equipment\PreventativeTrait;
use App\Http\Traits\General\SortTable;
use App\Models\MenuOption;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class AdminComponent extends Component
{
    use WithPagination;
    use SortTable;
    use PreventativeTrait;

    protected $allowed_actions;
    protected $table_allowed_actions;
    protected $arr_actions;

    public $product_name = "";

    public $rows = '10';

    protected $listeners = [
        'search-preventative' => 'render',
        'search-and-reset-pages' => 'search_and_reset_page'
    ];

    protected $queryString = [
        'product_name' => ['except' => ''],
        'rows' => ['except' => '10']
    ];

    public function mount()
    {
        $this->search_event = "search-preventative";
        $this->sort="products.name";
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
        $thead[]       = array('value' => 'Producto', 'colspan' => '1', 'link' => 'products.name');
        $thead[]       = array('value' => 'Modelo', 'colspan' => '1', 'link' => 'products.model');
        $thead[]       = array('value' => 'Marca', 'colspan' => '1', 'link' => 'brands.name');
        $thead[]       = array('value' => 'Estado', 'colspan' => '1', 'link' => null);
        if(count($this->table_allowed_actions)>0){
            $thead[]       = array('value' => 'Operaciones', 'colspan' => count($this->table_allowed_actions), 'link' => null);
        }

        $this->rows = (intval($this->rows) == 0) ? 10 : $this->rows;

        $list_items = [];
        $list_items = $this->search_maintenance($this->product_name, $this->sort, $this->direction, $this->rows);


        return view('livewire.equipment.preventative.admin-component')->with(compact('thead', 'list_items', 'arr_actions'));
    }

    public function paginationView()
    {
        return 'components.general.pagination';
    }

    // public function crete_route()
    // {
    //     redirect()->route('create-brand');
    // }

    public function search_and_reset_page()
    {
        $this->resetPage();
    }

    protected function load_actions(){
        $menu_option = MenuOption::where('link', 'preventative')->first();
        $this->allowed_actions = Auth::user()->user_type->actions_by_menu_option($menu_option->id)->get();
        $this->table_allowed_actions = Auth::user()->user_type->actions_by_menu_option($menu_option->id)
            ->whereIn('actions.code', ['edit_preventative'])->get();

        $this->arr_actions = [];
        foreach ($this->allowed_actions as $key => $value) {
            $this->arr_actions[] = $value->code;
        }
    }
}
