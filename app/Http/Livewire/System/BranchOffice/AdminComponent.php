<?php

namespace App\Http\Livewire\System\BranchOffice;

use App\Http\Traits\General\SortTable;
use App\Http\Traits\System\BranchOfficeTrait;
use App\Models\MenuOption;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class AdminComponent extends Component
{
    use WithPagination;
    use SortTable;
    use BranchOfficeTrait;

    protected $allowed_actions;
    protected $table_allowed_actions;
    protected $arr_actions;

    public $name = "";

    public $rows = '10';

    protected $listeners = [
        'search-branch-office' => 'render',
        'search-and-reset-pages' => 'search_and_reset_page'
    ];

    protected $queryString = [
        'name' => ['except' => ''],
        'rows' => ['except' => '10']
    ];

    public function mount()
    {
        $this->search_event = "search-branch-office";
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
        $this->load_actions();
        $arr_actions = $this->arr_actions;

        $thead         = array();
        $thead[]       = array('value' => '#', 'colspan' => '1', 'link' => null);
        $thead[]       = array('value' => 'Nombre', 'colspan' => '1', 'link' => 'branch_offices.name');
        $thead[]       = array('value' => 'Dirección', 'colspan' => '1', 'link' => 'branch_offices.address');
        $thead[]       = array('value' => 'Teléfono', 'colspan' => '1', 'link' => 'branch_offices.phone');
        $thead[]       = array('value' => 'Departamento', 'colspan' => '1', 'link' => 'departments.name');
        $thead[]       = array('value' => 'Provincia', 'colspan' => '1', 'link' => 'provinces.name');
        $thead[]       = array('value' => 'Distrito', 'colspan' => '1', 'link' => 'districts.name');
        if(count($this->table_allowed_actions)>0){
            $thead[]       = array('value' => 'Operaciones', 'colspan' => count($this->table_allowed_actions), 'link' => null);
        }

        $this->rows = (intval($this->rows) == 0) ? 10 : $this->rows;

        $list_items = [];
        $list_items = $this->search_branch_offices($this->name, $this->sort, $this->direction, $this->rows);


        return view('livewire.system.branch-office.admin-component')->with(compact('thead', 'list_items', 'arr_actions'));
    }

    public function paginationView()
    {
        return 'components.general.pagination';
    }

    public function create_route()
    {
        redirect()->route('create-branch-office');
    }

    public function search_and_reset_page()
    {
        $this->resetPage();
    }

    protected function load_actions(){
        $menu_option = MenuOption::where('link', 'branch-office')->first();
        $this->allowed_actions = Auth::user()->user_type->actions_by_menu_option($menu_option->id)->get();
        $this->table_allowed_actions = Auth::user()->user_type->actions_by_menu_option($menu_option->id)
            ->whereIn('actions.code', ['edit_branch_office', 'delete_branch_office'])->get();

        $this->arr_actions = [];
        foreach ($this->allowed_actions as $key => $value) {
            $this->arr_actions[] = $value->code;
        }
    }
}
