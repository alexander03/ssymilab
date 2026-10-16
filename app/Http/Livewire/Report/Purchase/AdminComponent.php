<?php

namespace App\Http\Livewire\Report\Purchase;

use App\Http\Traits\General\SortTable;
use App\Http\Traits\Purchase\PurchaseTrait;
use App\Models\MenuOption;
use App\Models\Purchase;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use App\Models\BranchOffice;
use Livewire\WithPagination;

class AdminComponent extends Component
{
    use WithPagination;
    use SortTable;
    use PurchaseTrait;

    protected $allowed_actions;
    protected $table_allowed_actions;
    protected $arr_actions;

    public $branch_office_id = "";
    public $branch_office;

    public $date_start, $date_end, $supplier;

    public $types, $rows = '10';

    protected $listeners = [
        'search-lot' => 'render',
        'search-and-reset-pages' => 'search_and_reset_page'
    ];

    protected $queryString = [
        'rows' => ['except' => '10']
    ];

    public function mount()
    {
        $this->date_start = date('Y-m-d');
        $this->date_end = date('Y-m-d');
        $this->supplier = "";
        $this->sort = "documents.created_at";
        $this->direction = "desc";
        $this->search_event = "search-purchase";

        $this->branch_office = BranchOffice::orderBy('name','asc')->get();
    }

    public function updatingDateStart($value)
    {
        $this->resetPage();
    }

    public function updatingDateEnd($value)
    {
        $this->resetPage();
    }

    public function updatingSupplier($value)
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
        $thead[]       = array('value' => 'Tipo de Doc.', 'colspan' => '1', 'link' => 'documents.document_type_id');
        $thead[]       = array('value' => 'Número', 'colspan' => '1', 'link' => 'documents.number');
        $thead[]       = array('value' => 'Fecha', 'colspan' => '1', 'link' => 'documents.date_time');
        $thead[]       = array('value' => 'Documento', 'colspan' => '1', 'link' => 'supplier_document');
        $thead[]       = array('value' => 'Proveedor', 'colspan' => '1', 'link' => 'supplier_name');
        $thead[]       = array('value' => 'Moneda', 'colspan' => '1', 'link' => 'documents.currency_id');
        $thead[]       = array('value' => 'Total', 'colspan' => '1', 'link' => 'documents.total');
        $thead[]       = array('value' => 'Pendiente', 'colspan' => '1', 'link' => 'documents.pending_amount');
        
        $list_items = [];
        $list_items = $this->search_purchases_report($this->branch_office_id,$this->date_start,$this->date_end,'', '',$this->supplier, $this->sort, $this->direction, $this->rows);


        return view('livewire.report.purchase.admin-component')->with(compact('thead', 'list_items', 'arr_actions'));
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

