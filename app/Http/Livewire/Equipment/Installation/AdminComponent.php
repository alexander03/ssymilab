<?php

namespace App\Http\Livewire\Equipment\Installation;

use App\Http\Traits\Equipment\InstallationTrait;
use App\Http\Traits\General\SortTable;
use Livewire\WithPagination;
use App\Models\Brand;
use Livewire\Component;
use App\Models\MenuOption;
use Illuminate\Support\Facades\Auth;

class AdminComponent extends Component
{
    use WithPagination;
    use SortTable;
    use InstallationTrait;

    protected $allowed_actions;
    protected $table_allowed_actions;
    protected $arr_actions;

    public $product_name = "";
    public $product_serie = "";
    public $product_status = "";
    public $status = "";
    public $customer = "";
    public $date_start = "";
    public $date_end = "";
    public $anio = "2022";
    public $brand_id = "";
    public $product_model = "";

    public $rows = '10';
    public $brands;

    protected $listeners = [
        'search-installation' => 'render',
        'search-and-reset-pages' => 'search_and_reset_page'
    ];

    protected $queryString = [
        'product_name' => ['except' => ''],
        'rows' => ['except' => '10']
    ];

    public function mount()
    {
        $this->search_event = "search-installation";
        $this->sort = "schedules.installed_date";
        $this->direction = "desc";
        $this->product_status = "";
        $this->status = "";
        $this->brands = Brand::orderBy('name')->get();
    }

    public function updatingSearchProductName($value)
    {
        $this->resetPage();
    }

    public function updatingRows($value)
    {
        $this->resetPage();
    }

    public function updatingAnio($value)
    {
        $this->resetPage();
    }

    public function updatingProductStatus($value)
    {
        $this->resetPage();
    }

    public function render()
    {
        $this->load_actions();
        $arr_actions = $this->arr_actions;

        $thead         = array();
        $thead[]       = array('value' => '#', 'colspan' => '1', 'link' => null);
        $thead[]       = array('value' => 'Fecha', 'colspan' => '1', 'link' => 'schedules.installed_date');
        $thead[]       = array('value' => 'Codigo', 'colspan' => '1', 'link' => 'products.code');
        $thead[]       = array('value' => 'Producto', 'colspan' => '1', 'link' => 'products.name');
        $thead[]       = array('value' => 'Modelo', 'colspan' => '1', 'link' => 'products.model');
        $thead[]       = array('value' => 'Marca', 'colspan' => '1', 'link' => 'products.brand_id');
        $thead[]       = array('value' => 'Serie', 'colspan' => '1', 'link' => 'product_series.serie');
        $thead[]       = array('value' => 'Cliente', 'colspan' => '1', 'link' => 'schedules.customer_name');
        $thead[]       = array('value' => 'Lugar', 'colspan' => '1', 'link' => 'schedule.product_place');
        $thead[]       = array('value' => 'Ubicacion', 'colspan' => '1', 'link' => 'schedule.product_location');
        $thead[]       = array('value' => 'Estado', 'colspan' => '1', 'link' => 'schedule.status');
        $thead[]       = array('value' => 'Situacion', 'colspan' => '1', 'link' => 'schedule.product_status');
        if(count($this->table_allowed_actions)>0){
            $thead[]       = array('value' => 'Operaciones', 'colspan' => count($this->table_allowed_actions), 'link' => null);
        }

        $this->rows = (intval($this->rows) == 0) ? 10 : $this->rows;

        $list_items = [];
        $list_items = $this->search_installation($this->date_start, $this->date_end, $this->product_name, $this->product_model, $this->product_serie, $this->customer,$this->product_status, $this->status, $this->brand_id, $this->sort, $this->direction, $this->rows);


        return view('livewire.equipment.installation.admin-component')->with(compact('thead', 'list_items', 'arr_actions'));
    }

    public function paginationView()
    {
        return 'components.general.pagination';
    }

    public function create_route()
    {
        redirect()->route('create-installation');
    }

    public function search_and_reset_page()
    {
        $this->resetPage();
    }

    protected function load_actions(){
        $menu_option = MenuOption::where('link', 'installation')->first();
        $this->allowed_actions = Auth::user()->user_type->actions_by_menu_option($menu_option->id)->get();
        $this->table_allowed_actions = Auth::user()->user_type->actions_by_menu_option($menu_option->id)
            ->whereIn('actions.code', ['edit_installation','delete_installation','pdf_installation','change_status_installation','upload_file_installation','files_installation','pdf_follow','pdf_list_installation'])->get();

        $this->arr_actions = [];
        foreach ($this->allowed_actions as $key => $value) {
            $this->arr_actions[] = $value->code;
        }
    }
}
