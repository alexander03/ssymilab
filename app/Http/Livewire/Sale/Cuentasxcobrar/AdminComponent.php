<?php

namespace App\Http\Livewire\Sale\Cuentasxcobrar;

use App\Http\Traits\General\SortTable;
use App\Http\Traits\Sale\SaleTrait;
use App\Models\MenuOption;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use App\Models\BranchOffice;
use App\Models\Zone;
use Livewire\WithPagination;

class AdminComponent extends Component
{
    use WithPagination;
    use SortTable;
    use SaleTrait;

    protected $allowed_actions;
    protected $arr_actions;

    public $date_start, $date_end, $number, $client, $status_sale;
    public $branch_office_id = "";
    public $branch_office;
    public $zone_id = "";
    public $zones;

    public $rows = '10';

    protected $listeners = [
        'search-installment' => 'render',
        'search-and-reset-pages' => 'search_and_reset_page'
    ];

    protected $queryString = [
        'date_start' => ['except' => ''],
        'date_end' => ['except' => ''],
        'number' => ['except' => ''],
        'client' => ['except' => ''],
        'zone_id' => ['except' => ''],
        'rows' => ['except' => '10']
    ];

    public function mount()
    {
        $this->date_start = date('Y-m-')."01";
        $this->sort = "document_payments.id";
        $this->direction = "desc";
        $this->search_event = "search-installment";
        $this->status_sale = "";
        $this->branch_office = BranchOffice::orderBy('name','asc')->get();
        $this->zones = Zone::orderBy('name','asc')->get();
    }

    public function updatingDateStart($value)
    {
        $this->resetPage();
    }

    public function updatingDateEnd($value)
    {
        $this->resetPage();
    }

    public function updatingNumber($value)
    {
        $this->resetPage();
    }

    public function updatingSupplier($value)
    {
        $this->resetPage();
    }

    public function updatingZoneId($value)
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
        $thead[]       = array('value' => 'Sucursal', 'colspan' => '1', 'link' => 'documents.branch_office_id');
        $thead[]       = array('value' => 'Venta', 'colspan' => '1', 'link' => 'documents.number');
        $thead[]       = array('value' => 'RUC', 'colspan' => '1', 'link' => 'supplier_document');
        $thead[]       = array('value' => 'Cliente', 'colspan' => '1', 'link' => 'supplier_document');
        $thead[]       = array('value' => 'Cuota', 'colspan' => '1', 'link' => 'document_payments.name');
        $thead[]       = array('value' => 'Monto', 'colspan' => '1', 'link' => 'document_payments.total_amount');
        $thead[]       = array('value' => 'Pendiente', 'colspan' => '1', 'link' => '');
        $thead[]       = array('value' => 'Fecha', 'colspan' => '1', 'link' => '');
        $thead[]       = array('value' => 'Fecha de pago', 'colspan' => '1', 'link' => '');
        $thead[]       = array('value' => 'Estado', 'colspan' => '1', 'link' => null);


        $this->rows = (intval($this->rows) == 0) ? 10 : $this->rows;

        $list_items = [];
        $list_items = $this->search_payments($this->branch_office_id,$this->date_start,$this->date_end,$this->number,$this->client, 4, $this->sort, $this->direction, $this->rows, $this->status_sale, $this->zone_id);


        return view('livewire.sale.cuentasxcobrar.admin-component')->with(compact('thead', 'list_items', 'arr_actions'));
    }

    public function paginationView()
    {
        return 'components.general.pagination';
    }

    public function create_route()
    {
        redirect()->route('create-installment');
    }

    public function search_and_reset_page()
    {
        $this->resetPage();
    }

    protected function load_actions(){
        $menu_option = MenuOption::where('link', 'debts-to-cash')->first();
        $this->allowed_actions = Auth::user()->user_type->actions_by_menu_option($menu_option->id)->get();
        $this->table_allowed_actions = Auth::user()->user_type->actions_by_menu_option($menu_option->id)
            ->whereIn('actions.code', ['pdf',  'pdf-vendedor'])->get();
        $this->arr_actions = [];
        foreach ($this->allowed_actions as $key => $value) {
            $this->arr_actions[] = $value->code;
        }
    }
}
