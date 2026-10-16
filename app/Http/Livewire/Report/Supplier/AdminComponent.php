<?php

namespace App\Http\Livewire\Report\Supplier;

use App\Http\Traits\General\SortTable;
use App\Http\Traits\Maintenance\PersonTrait;
use App\Models\MenuOption;
use App\Models\Role;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class AdminComponent extends Component
{
    use WithPagination;
    use SortTable;
    use PersonTrait;

    protected $allowed_actions;
    protected $table_allowed_actions;
    protected $arr_actions;

    public $name = "";
    public $document_number = "";
    public $role_id = "2";

    public $types, $rows = '10';

    protected $listeners = [
        'search-lot' => 'render',
        'search-and-reset-pages' => 'search_and_reset_page'
    ];

    protected $queryString = [
        'name' => ['except' => ''],
        'document_number' => ['except' => ''],
        'role_id' => ['except' => ''],
        'rows' => ['except' => '10']
    ];

    public function mount()
    {
        $this->sort = "name_complete";
        $this->search_event = "search-person";

        $this->roles = Role::where('id','=',2)->orderBy('name')->get();
    }

    public function updatingName($value)
    {
        $this->resetPage();
    }

    public function updatingDocumentNumber($value)
    {
        $this->resetPage();
    }

    public function updatingRoleId($value)
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
        $thead[]       = array('value' => 'Nro. Doc.', 'colspan' => '1', 'link' => 'document_number');
        $thead[]       = array('value' => 'Nombres/Razón Social', 'colspan' => '1', 'link' => 'name_complete');
        $thead[]       = array('value' => 'Email', 'colspan' => '1', 'link' => 'people.email');
        $thead[]       = array('value' => 'Teléfono', 'colspan' => '1', 'link' => 'people.phone');
        
        $list_items = [];
        $list_items = $this->search_people($this->name, $this->document_number, $this->role_id, $this->sort, $this->direction, $this->rows);


        return view('livewire.report.supplier.admin-component')->with(compact('thead', 'list_items', 'arr_actions'));
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

