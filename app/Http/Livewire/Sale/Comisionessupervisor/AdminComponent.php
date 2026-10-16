<?php

namespace App\Http\Livewire\Sale\Comisionessupervisor;

use App\Http\Traits\General\SortTable;
use App\Http\Traits\Sale\SaleTrait;
use App\Models\MenuOption;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class AdminComponent extends Component
{
    use WithPagination;
    use SortTable;
    use SaleTrait;

    protected $allowed_actions;
    protected $arr_actions;

    public $date_start, $date_end, $number, $supplier,$supervisor;

    public $rows = '10';

    protected $listeners = [
        'search-installment' => 'render',
        'search-and-reset-pages' => 'search_and_reset_page'
    ];

    protected $queryString = [
        'date_start' => ['except' => ''],
        'date_end' => ['except' => ''],
        'number' => ['except' => ''],
        'supplier' => ['except' => ''],
        'rows' => ['except' => '10']
    ];

    public function mount()
    {
        $this->date_start = '';
        $this->supervisor = '';
        $this->sort = "documents.date_time";
        $this->direction = "desc";
        $this->search_event = "search-comisiones";
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
        $thead[]       = array('value' => 'Venta', 'colspan' => '1', 'link' => 'documents.number');
        $thead[]       = array('value' => 'Fecha Doc.', 'colspan' => '1', 'link' => 'supplier_document');
        $thead[]       = array('value' => 'Cliente', 'colspan' => '1', 'link' => 'supplier_document');
        $thead[]       = array('value' => 'Vendedor', 'colspan' => '1', 'link' => 'supplier_document');
        $thead[]       = array('value' => 'Supervisor', 'colspan' => '1', 'link' => 'supplier_document');
        $thead[]       = array('value' => 'Total', 'colspan' => '1', 'link' => 'document_payments.total_amount');
        $thead[]       = array('value' => 'Mes Pago', 'colspan' => '1', 'link' => 'documents.paid_comision');
        $thead[]       = array('value' => '%', 'colspan' => '1', 'link' => 'documents.percentage_comision');
        $thead[]       = array('value' => 'Comision', 'colspan' => '1', 'link' => '');
        $thead[]       = array('value' => 'Estado', 'colspan' => '1', 'link' => null);
        if(count($this->table_allowed_actions)>0){
            $thead[]       = array('value' => 'Operaciones', 'colspan' => count($this->table_allowed_actions), 'link' => null);
        }

        $this->rows = (intval($this->rows) == 0) ? 10 : $this->rows;

        $list_items = [];
        $list_items = $this->search_sales($this->date_start,$this->date_end,"", $this->number,$this->supplier, null, 'C', $this->supervisor, $this->sort, $this->direction, $this->rows);

        return view('livewire.sale.comisionessupervisor.admin-component')->with(compact('thead', 'list_items', 'arr_actions'));
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
        $menu_option = MenuOption::where('link', 'comisionessupervisor')->first();
        $this->allowed_actions = Auth::user()->user_type->actions_by_menu_option($menu_option->id)->get();
        $this->table_allowed_actions = Auth::user()->user_type->actions_by_menu_option($menu_option->id)
            ->whereIn('actions.code', ['pdf',  'paid'])->get();
        $this->arr_actions = [];
        foreach ($this->allowed_actions as $key => $value) {
            $this->arr_actions[] = $value->code;
        }
    }
}
