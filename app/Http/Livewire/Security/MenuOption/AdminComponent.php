<?php

namespace App\Http\Livewire\Security\MenuOption;

use App\Http\Traits\General\LoadDelay;
use App\Http\Traits\General\SessionFilter;
use App\Http\Traits\General\SortTable;
use App\Http\Traits\Security\MenuOptionTrait;
use App\Models\MenuCategory;
use App\Models\MenuOption;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class AdminComponent extends Component
{
    use WithPagination;
    use SortTable;
    use MenuOptionTrait;
    use SessionFilter;

    protected $allowed_actions;
    protected $table_allowed_actions;
    protected $arr_actions;

    public $name = "";
    public $menu_category_id = "";

    public $rows = '10';

    public $menu_categories;

    protected $listeners = [
        'search-menu-option' => 'render',
        'search-and-reset-pages' => 'search_and_reset_page'
    ];

    protected $queryString = [
        'name' => ['except' => ''],
        'menu_category_id' => ['except' => ''],
        'rows' => ['except' => '10']
    ];

    public function mount()
    {
        $this->sort = "menu_options.name";
        $this->search_event = "search-menu-option";
        $this->session_route = 'menu_option';

        $this->menu_categories = MenuCategory::orderBy('name')->get();
        $this->init_session_filter(['name', 'menu_category_id', 'rows']);
    }

    public function updatingName($value)
    {
        $this->resetPage();
    }

    public function updatingMenuCategoryId($value)
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
        $thead[]       = array('value' => 'Nombre', 'colspan' => '1', 'link' => 'menu_options.name');
        $thead[]       = array('value' => 'Categoría', 'colspan' => '1', 'link' => 'menu_categories.name');
        $thead[]       = array('value' => 'Orden', 'colspan' => '1', 'link' => 'menu_options.order');
        $thead[]       = array('value' => 'Link', 'colspan' => '1', 'link' => 'menu_options.link');
        if(count($this->table_allowed_actions)>0){
            $thead[]       = array('value' => 'Operaciones', 'colspan' => count($this->table_allowed_actions), 'link' => null);
        }

        $this->rows = (intval($this->rows) == 0) ? 10 : $this->rows;

        $list_items = [];
        $list_items = $this->search_menu_options($this->name, $this->menu_category_id, $this->sort, $this->direction, $this->rows);

        return view('livewire.security.menu-option.admin-component')->with(compact('thead', 'list_items', 'arr_actions'));
    }

    public function paginationView()
    {
        return 'components.general.pagination';
    }

    public function create_route()
    {
        redirect()->route('create-menu-option');
    }

    public function search_and_reset_page()
    {
        $this->resetPage();
    }

    protected function load_actions(){
        $menu_option = MenuOption::where('link', 'menu-option')->first();
        $this->allowed_actions = Auth::user()->user_type->actions_by_menu_option($menu_option->id)->get();
        $this->table_allowed_actions = Auth::user()->user_type->actions_by_menu_option($menu_option->id)
            ->whereIn('actions.code', ['edit_menu_option', 'delete_menu_option'])->get();

        $this->arr_actions = [];
        foreach ($this->allowed_actions as $key => $value) {
            $this->arr_actions[] = $value->code;
        }
    }

}
