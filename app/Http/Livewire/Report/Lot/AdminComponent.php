<?php

namespace App\Http\Livewire\Report\Lot;

use App\Http\Traits\General\SortTable;
use App\Http\Traits\Store\LotTrait;
use App\Models\MenuOption;
use App\Models\Brand;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use App\Models\BranchOffice;
use Livewire\WithPagination;

class AdminComponent extends Component
{
    use WithPagination;
    use SortTable;
    use LotTrait;

    protected $allowed_actions;
    protected $table_allowed_actions;
    protected $arr_actions;

    public $product_id = "";
    public $brand_id = "";
    public $product_name = "";
    public $lot = "";
    public $branch_office_id = "";
    public $branch_office;

    public $rows = '10';

    protected $listeners = [
        'search-lot' => 'render',
        'search-and-reset-pages' => 'search_and_reset_page'
    ];

    protected $queryString = [
        'product_name' => ['except' => ''],
        'lot' => ['except' => ''],
        'rows' => ['except' => '10']
    ];

    public function mount()
    {
        $this->search_event = "search-lot";
        $this->brands = Brand::orderBy('name')->get();
        $this->sort = "products.name";
        $this->branch_office = BranchOffice::orderBy('name','asc')->get();
    }

    public function updatingLot($value)
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
        $thead[]       = array('value' => 'Sucursal', 'colspan' => '1', 'link' => 'lots.branch_office_id');
        $thead[]       = array('value' => 'Producto', 'colspan' => '1', 'link' => 'products.name');
        $thead[]       = array('value' => 'Marca', 'colspan' => '1', 'link' => 'brands.name');
        $thead[]       = array('value' => 'Lote', 'colspan' => '1', 'link' => 'lots.lot');
        $thead[]       = array('value' => 'Fecha Vencimiento', 'colspan' => '1', 'link' => 'lots.due_date');
        $thead[]       = array('value' => 'Codigo Barra', 'colspan' => '1', 'link' => 'lots.barcode');
        $thead[]       = array('value' => 'Ingresado', 'colspan' => '1', 'link' => 'lots.quantity');
        $thead[]       = array('value' => 'Restante', 'colspan' => '1', 'link' => 'lots.remain');
        $thead[]       = array('value' => 'Estado', 'colspan' => '1', 'link' => 'lots.status');
        if(count($this->table_allowed_actions)>0){
            $thead[]       = array('value' => 'Operaciones', 'colspan' => count($this->table_allowed_actions), 'link' => null);
        }

        $this->rows = (intval($this->rows) == 0) ? 10 : $this->rows;

        $list_items = [];
        $list_items = $this->search_lotsreport($this->product_id, $this->product_name, $this->lot, null, $this->branch_office_id, $this->brand_id, $this->sort, $this->direction, $this->rows);


        return view('livewire.report.lot.admin-component')->with(compact('thead', 'list_items', 'arr_actions'));
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
        $menu_option = MenuOption::where('link', 'lot')->first();
        $this->allowed_actions = Auth::user()->user_type->actions_by_menu_option($menu_option->id)->get();
        $this->table_allowed_actions = Auth::user()->user_type->actions_by_menu_option($menu_option->id)
             ->whereIn('actions.code', ['pdf_kardex_lot', 'delete_lot','edit_lot'])->get();

        $this->arr_actions = [];
        foreach ($this->allowed_actions as $key => $value) {
            $this->arr_actions[] = $value->code;
        }
    }
}
