<?php

namespace App\Http\Livewire\Maintenance\Product;

use App\Http\Traits\General\SortTable;
use App\Http\Traits\Maintenance\ProductTrait;
use App\Models\Brand;
use App\Models\Category;
use App\Models\MenuOption;
use App\Models\Person;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;
use App\Http\Traits\General\SessionFilter;

class AdminComponent extends Component
{
    use WithPagination;
    use SortTable;
    use ProductTrait;
    use SessionFilter;

    protected $allowed_actions;
    protected $table_allowed_actions;
    protected $arr_actions;

    public $name = "";
    public $model = "";
    public $type = "";
    public $category_id = "";
    public $brand_id = "";
    public $supplier_id = "";
    public $status_product = "";

    public $rows = '10';
    public $types, $categories, $brands, $suppliers;

    protected $listeners = [
        'search-product' => 'render',
        'search-and-reset-pages' => 'search_and_reset_page'
    ];

    protected $queryString = [
        'name' => ['except' => ''],
        'model' => ['except' => ''],
        'type' => ['except' => ''],
        'category_id' => ['except' => ''],
        'brand_id' => ['except' => ''],
        'supplier_id' => ['except' => ''],
        'status_product' => ['except' => ''],
        'rows' => ['except' => '10']
    ];

    public function mount()
    {
        $this->sort = "products.name";
        $this->search_event = "search-product";

        $this->types = ["R" => "DISPOSITIVO MEDICO", "M" => "EQUIPO", "S"=>"SERVICIOS", "E"=>"REPUESTOS", "C"=>"COMPUTO", "O"=>"CONSUMIBLE"];
        $this->categories = Category::orderBy('name')->get();
        $this->brands = Brand::orderBy('name')->get();
        $this->suppliers = Person::with_rol('P')->get();
        $this->session_route = 'product';
        $this->init_session_filter(['name', 'type', 'category_id', 'brand_id', 'supplier_id', 'status_product', 'rows']);
    }

    public function updatingName($value)
    {
        $this->resetPage();
    }

    public function updatingType($value)
    {
        $this->resetPage();
    }

    public function updatingCategoryId($value)
    {
        $this->resetPage();
    }

    public function updatingBrandId($value)
    {
        $this->resetPage();
    }

    public function updatingSupplierId($value)
    {
        $this->resetPage();
    }

    public function updatingStatusProduct($value)
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
        $thead[]       = array('value' => 'Código', 'colspan' => '1', 'link' => 'products.code');
        $thead[]       = array('value' => 'Código Barra', 'colspan' => '1', 'link' => 'products.barcode');
        $thead[]       = array('value' => 'Nombre', 'colspan' => '1', 'link' => 'products.name');
        if($this->type=="" || $this->type=="R"){
            $thead[]       = array('value' => 'Presentacion', 'colspan' => '1', 'link' => 'products.presentation');
        }
        if($this->type=="" || $this->type=="M"){
            $thead[]       = array('value' => 'Modelo', 'colspan' => '1', 'link' => 'products.model');
        }
        $thead[]       = array('value' => 'Categoría', 'colspan' => '1', 'link' => 'categories.name');
        $thead[]       = array('value' => 'Distribuidor', 'colspan' => '1', 'link' => 'supplier');
        $thead[]       = array('value' => 'Marca', 'colspan' => '1', 'link' => 'brands.name');
        $thead[]       = array('value' => 'Tipo', 'colspan' => '1', 'link' => 'products.type');
        $thead[]       = array('value' => 'Estado', 'colspan' => '1', 'link' => 'products.status');
        if(count($this->table_allowed_actions)>0){
            $thead[]       = array('value' => 'Operaciones', 'colspan' => count($this->table_allowed_actions), 'link' => null);
        }

        $this->rows = (intval($this->rows) == 0) ? 10 : $this->rows;

        $list_items = [];
        $list_items = $this->search_products($this->type, $this->name, $this->category_id, $this->brand_id, $this->supplier_id, $this->status_product, $this->sort, $this->direction, $this->rows, $this->model);

        return view('livewire.maintenance.product.admin-component')->with(compact('thead', 'list_items', 'arr_actions'));

    }

    public function paginationView()
    {
        return 'components.general.pagination';
    }

    public function create_route()
    {
        redirect()->route('create-product');
    }

    public function search_and_reset_page()
    {
        $this->resetPage();
    }

    protected function load_actions(){
        $menu_option = MenuOption::where('link', 'product')->first();
        $this->allowed_actions = Auth::user()->user_type->actions_by_menu_option($menu_option->id)->get();
        $this->table_allowed_actions = Auth::user()->user_type->actions_by_menu_option($menu_option->id)
            ->whereIn('actions.code', ['edit_product', 'delete_product', 'change_status_product'])->get();

        $this->arr_actions = [];
        foreach ($this->allowed_actions as $key => $value) {
            $this->arr_actions[] = $value->code;
        }
    }
}
