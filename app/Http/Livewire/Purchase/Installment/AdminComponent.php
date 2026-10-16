<?php

namespace App\Http\Livewire\Purchase\Installment;

use App\Http\Traits\General\SortTable;
use App\Http\Traits\Purchase\InstallmentTrait;
use App\Models\MenuOption;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class AdminComponent extends Component
{
    use WithPagination;
    use SortTable;
    use InstallmentTrait;

    protected $allowed_actions;
    protected $arr_actions;

    public $date_start, $date_end, $number, $supplier;

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
        $this->date_start = date('Y-m-d');
        $this->sort = "document_payments.id";
        $this->direction = "desc";
        $this->search_event = "search-installment";
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
        $thead[]       = array('value' => 'Compra', 'colspan' => '1', 'link' => 'documents.number');
        $thead[]       = array('value' => 'Proveedor', 'colspan' => '1', 'link' => 'supplier_document');
        $thead[]       = array('value' => 'Cuota', 'colspan' => '1', 'link' => 'document_payments.name');
        $thead[]       = array('value' => 'Monto', 'colspan' => '1', 'link' => 'document_payments.total_amount');
        $thead[]       = array('value' => 'Fecha', 'colspan' => '1', 'link' => '');
        $thead[]       = array('value' => 'Fecha de pago', 'colspan' => '1', 'link' => '');
        $thead[]       = array('value' => 'Estado', 'colspan' => '1', 'link' => null);


        $this->rows = (intval($this->rows) == 0) ? 10 : $this->rows;

        $list_items = [];
        $list_items = $this->search_installments($this->date_start,$this->date_end,$this->number,$this->supplier, 1, $this->sort, $this->direction, $this->rows);


        return view('livewire.purchase.installment.admin-component')->with(compact('thead', 'list_items', 'arr_actions'));
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
        $menu_option = MenuOption::where('link', 'debts-to-pay')->first();
        $this->allowed_actions = Auth::user()->user_type->actions_by_menu_option($menu_option->id)->get();
        $this->arr_actions = [];
        foreach ($this->allowed_actions as $key => $value) {
            $this->arr_actions[] = $value->code;
        }
    }
}
