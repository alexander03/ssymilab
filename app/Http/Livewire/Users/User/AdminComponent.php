<?php

namespace App\Http\Livewire\Users\User;

use App\Http\Traits\General\SessionFilter;
use App\Http\Traits\General\SortTable;
use App\Http\Traits\Users\UserTrait;
use App\Models\BranchOffice;
use App\Models\MenuOption;
use App\Models\UserType;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class AdminComponent extends Component
{
    use WithPagination;
    use SortTable;
    use UserTrait;
    use SessionFilter;

    protected $allowed_actions;
    protected $table_allowed_actions;
    protected $arr_actions;

    public $username = "";
    public $person_name = "";
    public $branch_office_id = "";
    public $user_type_id = "";

    public $branch_offices;
    public $user_types;

    public $rows = '10';

    protected $listeners = [
        'search-user' => 'render',
        'search-and-reset-pages' => 'search_and_reset_page'
    ];

    protected $queryString = [
        'username' => ['except' => ''],
        'person_name' => ['except' => ''],
        'branch_office_id' => ['except' => ''],
        'user_type_id' => ['except' => ''],
        'rows' => ['except' => '10']
    ];


    public function mount()
    {
        $this->sort = "users.username";
        $this->search_event = "search-user";
        $this->session_route = 'user';

        $this->branch_offices = BranchOffice::orderBy('name')->get();
        $this->user_types = UserType::orderBy('name')->get();
        $this->init_session_filter(['username', 'person_name','branch_office_id', 'user_type_id', 'rows']);
    }

    public function updatingUsername($value)
    {
        $this->resetPage();
    }

    public function updatingBranchOfficeId($value)
    {
        $this->resetPage();
    }

    public function updatingUserTypeId($value)
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
        $thead[]       = array('value' => 'Usuario', 'colspan' => '1', 'link' => 'users.username');
        $thead[]       = array('value' => 'Nombre', 'colspan' => '1', 'link' => 'users.username');
        $thead[]       = array('value' => 'Tipo de Usuario', 'colspan' => '1', 'link' => 'user_types.name');
        $thead[]       = array('value' => 'Sucursal', 'colspan' => '1', 'link' => 'branch_offices.name');
        if(count($this->table_allowed_actions)>0){
            $thead[]       = array('value' => 'Operaciones', 'colspan' => count($this->table_allowed_actions), 'link' => null);
        }

        $this->rows = (intval($this->rows) == 0) ? 10 : $this->rows;

        $list_items = [];
        $list_items = $this->search_users($this->username, $this->branch_office_id, $this->user_type_id, $this->person_name, $this->sort, $this->direction, $this->rows);

        return view('livewire.users.user.admin-component')->with(compact('thead', 'list_items', 'arr_actions'));
    }

    public function paginationView()
    {
        return 'components.general.pagination';
    }

    public function create_route()
    {
        redirect()->route('create-user');
    }

    public function search_and_reset_page()
    {
        $this->resetPage();
    }

    protected function load_actions(){
        $menu_option = MenuOption::where('link', 'user')->first();
        $this->allowed_actions = Auth::user()->user_type->actions_by_menu_option($menu_option->id)->get();
        $this->table_allowed_actions = Auth::user()->user_type->actions_by_menu_option($menu_option->id)
            ->whereIn('actions.code', ['edit_user', 'delete_user'])->get();

        $this->arr_actions = [];
        foreach ($this->allowed_actions as $key => $value) {
            $this->arr_actions[] = $value->code;
        }
    }
}
