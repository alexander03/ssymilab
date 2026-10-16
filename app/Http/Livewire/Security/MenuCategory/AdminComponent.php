<?php

namespace App\Http\Livewire\Security\MenuCategory;

use App\Http\Traits\General\LoadDelay;
use App\Http\Traits\General\SessionFilter;
use App\Http\Traits\General\SortTable;
use App\Http\Traits\Security\MenuCategoryTrait;
use App\Models\MenuOption;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Livewire\Component;
use Livewire\WithPagination;

class AdminComponent extends Component
{
    use WithPagination;
    use SortTable;
    use MenuCategoryTrait;
    use SessionFilter;

    protected $allowed_actions;
    protected $table_allowed_actions;
    protected $arr_actions;

    public $search_name = "";

    public $rows = '10';

    // public $data_filter = [
    //     "menu_categories.name" => ["column" => "menu_categories.name", "data_type" => "string", "name" => "Nombre"],
    //     "menu_categories.order" => ["column" => "menu_categories.order", "data_type" => "integer", "name" => "Orden"],
    //     "menu_categories.date" => ["column" => "menu_categories.date", "data_type" => "date", "name" => "Fecha"],
    // ];
    //
    //public $arr_filter = [];

    protected $listeners = [
        'search-menu-category' => 'render',
        'search-and-reset-pages' => 'search_and_reset_page'
    ];

    protected $queryString = [
        'search_name' => ['except' => ''],
        'rows' => ['except' => '10']
    ];

    public function mount()
    {
        $this->search_event = "search-menu-category";
        $this->session_route = 'menu_category';

        $this->init_session_filter(['search_name', 'rows']);
    }

    public function updatingSearchName($value)
    {
        $this->resetPage();
    }

    public function updatingRows($value)
    {
        $this->resetPage();
    }

    public function render()
    {
        $this->save_filters();
        $this->load_actions();
        $arr_actions = $this->arr_actions;

        $thead         = array();
        $thead[]       = array('value' => '#', 'colspan' => '1', 'link' => null);
        $thead[]       = array('value' => 'Nombre', 'colspan' => '1', 'link' => 'name');
        $thead[]       = array('value' => 'Orden', 'colspan' => '1', 'link' => 'order');
        $thead[]       = array('value' => 'Icono', 'colspan' => '1', 'link' => 'icon');
        if(count($this->table_allowed_actions)>0){
            $thead[]       = array('value' => 'Operaciones', 'colspan' => count($this->table_allowed_actions), 'link' => null);
        }

        $this->rows = (intval($this->rows) == 0) ? 10 : $this->rows;

        $list_items = [];
        $list_items = $this->search_menu_categories($this->search_name, $this->sort, $this->direction, $this->rows);


        return view('livewire.security.menu-category.admin-component')->with(compact('thead', 'list_items', 'arr_actions'));
    }

    public function paginationView()
    {
        return 'components.general.pagination';
    }

    public function crete_route()
    {
        redirect()->route('create-menu-category');
    }

    public function search_and_reset_page()
    {
        $this->resetPage();
    }

    protected function load_actions(){
        $menu_option = MenuOption::where('link', 'menu-category')->first();
        $this->allowed_actions = Auth::user()->user_type->actions_by_menu_option($menu_option->id)->get();
        $this->table_allowed_actions = Auth::user()->user_type->actions_by_menu_option($menu_option->id)
            ->whereIn('actions.code', ['edit_menu_category', 'delete_menu_category'])->get();

        $this->arr_actions = [];
        foreach ($this->allowed_actions as $key => $value) {
            $this->arr_actions[] = $value->code;
        }
    }

    // public function filter_apply(Array $arr_filter = []){
    //     $this->arr_filter = $arr_filter;
    // }
}
