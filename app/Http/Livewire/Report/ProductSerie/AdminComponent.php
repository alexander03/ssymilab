<?php

namespace App\Http\Livewire\Report\ProductSerie;

use App\Http\Traits\General\SortTable;
use App\Http\Traits\Store\ProductSerieTrait;
use App\Models\MenuOption;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use App\Models\BranchOffice;
use Livewire\WithPagination;

class AdminComponent extends Component
{
    use WithPagination;
    use SortTable;
    use ProductSerieTrait;

    protected $allowed_actions;
    protected $table_allowed_actions;
    protected $arr_actions;

    public $product_id = "";
    public $product_name = "";
    public $serie = "";
    public $branch_office_id = "";
    public $branch_office;

    public $rows = '10';

    protected $listeners = [
        'search-lot' => 'render',
        'search-and-reset-pages' => 'search_and_reset_page'
    ];

    protected $queryString = [
        'product_name' => ['except' => ''],
        'serie' => ['except' => ''],
        'rows' => ['except' => '10']
    ];

    public function mount()
    {
        $this->search_event = "search-lot";
        $this->sort = "products.name";
        $this->branch_office = BranchOffice::orderBy('name','asc')->get();
    }

    public function updatingSerie($value)
    {
        $this->resetPage();
    }

    public function updatingProductId($value)
    {
        $this->resetPage();
    }

    public function updatingProductName($value)
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
        $thead[]       = array('value' => 'Sucursal', 'colspan' => '1', 'link' => 'product_series.branch_office_id');
        $thead[]       = array('value' => 'Producto', 'colspan' => '1', 'link' => 'products.name');
        $thead[]       = array('value' => 'Modelo', 'colspan' => '1', 'link' => 'products.model');
        $thead[]       = array('value' => 'Serie', 'colspan' => '1', 'link' => 'product_series.serie');
        $thead[]       = array('value' => 'Estado', 'colspan' => '1', 'link' => 'product_series.status');
        // if(count($this->table_allowed_actions)>0){
        //     $thead[]       = array('value' => 'Operaciones', 'colspan' => count($this->table_allowed_actions), 'link' => null);
        // }

        $this->rows = (intval($this->rows) == 0) ? 10 : $this->rows;

        $list_items = [];
        $list_items = $this->search_seriesreport($this->product_id, $this->product_name, $this->serie, null, $this->branch_office_id, $this->sort, $this->direction, $this->rows);


        return view('livewire.report.product-serie.admin-component')->with(compact('thead', 'list_items', 'arr_actions'));
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
        $menu_option = MenuOption::where('link', 'serie')->first();
        $this->allowed_actions = Auth::user()->user_type->actions_by_menu_option($menu_option->id)->get();
        // $this->table_allowed_actions = Auth::user()->user_type->actions_by_menu_option($menu_option->id)
        //     ->whereIn('actions.code', ['edit_lot', 'delete_lot'])->get();

        $this->arr_actions = [];
        foreach ($this->allowed_actions as $key => $value) {
            $this->arr_actions[] = $value->code;
        }
    }
}
