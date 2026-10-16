<?php

namespace App\Http\Livewire\Maintenance\Reason;

use App\Http\Traits\General\SortTable;
use App\Http\Traits\Maintenance\ReasonTrait;
use App\Models\MenuOption;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class AdminComponent extends Component
{
    use WithPagination;
    use SortTable;
    use ReasonTrait;

    protected $allowed_actions;
    protected $table_allowed_actions;
    protected $arr_actions;

    public $name = "";

    public $rows = '10';

    protected $listeners = [
        'search-reason' => 'render',
        'search-and-reset-pages' => 'search_and_reset_page'
    ];

    protected $queryString = [
        'name' => ['except' => ''],
        'rows' => ['except' => '10']
    ];

    public function mount()
    {
        $this->search_event = "search-reason";
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
        $thead[]       = array('value' => 'Tipo Documento', 'colspan' => '1', 'link' => 'document_type_id');
        $thead[]       = array('value' => 'Nombre', 'colspan' => '1', 'link' => 'name');
        if(count($this->table_allowed_actions)>0){
            $thead[]       = array('value' => 'Operaciones', 'colspan' => count($this->table_allowed_actions), 'link' => null);
        }

        $this->rows = (intval($this->rows) == 0) ? 10 : $this->rows;

        $list_items = [];
        $list_items = $this->search_reasons($this->name, [6,7], $this->sort, $this->direction, $this->rows);


        return view('livewire.maintenance.reason.admin-component')->with(compact('thead', 'list_items', 'arr_actions'));
    }

    public function paginationView()
    {
        return 'components.general.pagination';
    }

    public function crete_route()
    {
        redirect()->route('create-reason');
    }

    public function search_and_reset_page()
    {
        $this->resetPage();
    }

    protected function load_actions(){
        $menu_option = MenuOption::where('link', 'reason')->first();
        $this->allowed_actions = Auth::user()->user_type->actions_by_menu_option($menu_option->id)->get();
        $this->table_allowed_actions = Auth::user()->user_type->actions_by_menu_option($menu_option->id)
            ->whereIn('actions.code', ['edit_reason', 'delete_reason'])->get();

        $this->arr_actions = [];
        foreach ($this->allowed_actions as $key => $value) {
            $this->arr_actions[] = $value->code;
        }
    }

}
