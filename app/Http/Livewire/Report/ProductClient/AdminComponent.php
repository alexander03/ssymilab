<?php

namespace App\Http\Livewire\Report\ProductClient;

use App\Http\Traits\Report\ProductClientTrait;
use App\Models\BranchOffice;
use App\Models\Brand;
use App\Models\MenuOption;
use App\Models\Zone;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class AdminComponent extends Component
{
    use WithPagination;
    use ProductClientTrait;

    protected $allowed_actions;
    protected $arr_actions;

    public $date_start, $date_end, $client, $product;
    public $branch_office_id = "";
    public $brand_id = "";
    public $zone_id = "";
    public $branch_offices, $brands, $zones;

    public $rows = '10';

    protected $queryString = [
        'date_start' => ['except' => ''],
        'date_end' => ['except' => ''],
        'client' => ['except' => ''],
        'product' => ['except' => ''],
        'branch_office_id' => ['except' => ''],
        'brand_id' => ['except' => ''],
        'zone_id' => ['except' => ''],
        'rows' => ['except' => '10']
    ];

    public function mount()
    {
        //POR DEFECTO EL MES ACTUAL
        if(is_null($this->date_start) || $this->date_start == ""){
            $this->date_start = date('Y-m-')."01";
        }
        if(is_null($this->date_end) || $this->date_end == ""){
            $this->date_end = date('Y-m-d');
        }
        $this->branch_offices = BranchOffice::orderBy('name','asc')->get();
        $this->brands = Brand::orderBy('name','asc')->get();
        $this->zones = Zone::orderBy('name','asc')->get();
    }

    //CUALQUIER FILTRO VUELVE A LA PAGINA 1
    public function updating($name, $value)
    {
        if($name != "page"){
            $this->resetPage();
        }
    }

    public function render()
    {
        $this->load_actions();
        $arr_actions = $this->arr_actions;

        $this->rows = (intval($this->rows) == 0) ? 10 : $this->rows;

        //SE PAGINA POR CLIENTE PARA QUE CADA CLIENTE SALGA COMPLETO EN UNA PAGINA
        $filters = [$this->branch_office_id, $this->date_start, $this->date_end, $this->client, $this->brand_id, $this->product, $this->zone_id];
        $clients = $this->search_product_clients(...array_merge($filters, [$this->rows]));
        $details = $this->search_product_client_details(collect($clients->items())->pluck('client_id')->toArray(), ...$filters);
        //TOTAL GENERAL POR MARCA DE TODO EL FILTRO (NO SOLO DE LA PAGINA)
        $brand_totals = $this->search_brand_totals(...$filters);

        return view('livewire.report.product-client.admin-component')->with(compact('clients', 'details', 'brand_totals', 'arr_actions'));
    }

    public function paginationView()
    {
        return 'components.general.pagination';
    }

    protected function load_actions(){
        $menu_option = MenuOption::where('link', 'report-product-client')->first();
        $this->allowed_actions = Auth::user()->user_type->actions_by_menu_option($menu_option->id)->get();

        $this->arr_actions = [];
        foreach ($this->allowed_actions as $key => $value) {
            $this->arr_actions[] = $value->code;
        }
    }
}
