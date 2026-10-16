<?php

namespace App\Http\Livewire\Purchase\Purchase;

use App\Http\Traits\General\SortTable;
use App\Http\Traits\Purchase\PurchaseTrait;
use App\Models\MenuOption;
use App\Models\Purchase;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class AdminComponent extends Component
{
    use WithPagination;
    use SortTable;
    use PurchaseTrait;

    protected $allowed_actions;
    protected $table_allowed_actions;
    protected $arr_actions;

    public $date_start, $date_end, $number, $supplier, $document_type_id;
    public $document_types;

    public $rows = '10';

    protected $listeners = [
        'search-purchase' => 'render',
        'search-and-reset-pages' => 'search_and_reset_page'
    ];

    protected $queryString = [
        'date_start' => ['except' => ''],
        'date_end' => ['except' => ''],
        'document_type_id' => ['except' => ''],
        'number' => ['except' => ''],
        'supplier' => ['except' => ''],
        'rows' => ['except' => '10']
    ];

    public function mount()
    {
        $this->date_start = date('Y-m-d');
        $this->sort = "documents.created_at";
        $this->direction = "desc";
        $this->search_event = "search-purchase";

        $this->document_types = Purchase::list_document_types();
        $this->document_type_id = "";
    }

    public function updatingDateStart($value)
    {
        $this->resetPage();
    }

    public function updatingDateEnd($value)
    {
        $this->resetPage();
    }

    public function updatingDocumentTypeId($value)
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
        $thead[]       = array('value' => 'Tipo de Doc.', 'colspan' => '1', 'link' => 'documents.document_type_id');
        $thead[]       = array('value' => 'Número', 'colspan' => '1', 'link' => 'documents.number');
        $thead[]       = array('value' => 'Fecha', 'colspan' => '1', 'link' => 'documents.date_time');
        $thead[]       = array('value' => 'Documento', 'colspan' => '1', 'link' => 'supplier_document');
        $thead[]       = array('value' => 'Proveedor', 'colspan' => '1', 'link' => 'supplier_name');
        $thead[]       = array('value' => 'Moneda', 'colspan' => '1', 'link' => 'documents.currency_id');
        $thead[]       = array('value' => 'Total Comprobante', 'colspan' => '1', 'link' => 'documents.total');
        $thead[]       = array('value' => 'Total Compra', 'colspan' => '1', 'link' => 'documents.total');
        $thead[]       = array('value' => 'Pendiente', 'colspan' => '1', 'link' => 'documents.pending_amount');
        $thead[]       = array('value' => 'Fecha Entrega', 'colspan' => '1', 'link' => 'documents.schedule_time');
        $thead[]       = array('value' => 'Entrega', 'colspan' => '1', 'link' => null);
        $thead[]       = array('value' => 'Estado', 'colspan' => '1', 'link' => null);
        if(count($this->table_allowed_actions)>0){
            $thead[]       = array('value' => 'Operaciones', 'colspan' => count($this->table_allowed_actions), 'link' => null);
        }

        $this->rows = (intval($this->rows) == 0) ? 10 : $this->rows;

        $list_items = [];
        $list_items = $this->search_purchases($this->date_start,$this->date_end,$this->document_type_id, $this->number,$this->supplier, $this->sort, $this->direction, $this->rows);


        return view('livewire.purchase.purchase.admin-component')->with(compact('thead', 'list_items', 'arr_actions'));
    }

    public function paginationView()
    {
        return 'components.general.pagination';
    }

    public function create_route()
    {
        redirect()->route('create-purchase');
    }

    public function search_and_reset_page()
    {
        $this->resetPage();
    }

    protected function load_actions(){
        $menu_option = MenuOption::where('link', 'purchase')->first();
        $this->allowed_actions = Auth::user()->user_type->actions_by_menu_option($menu_option->id)->get();
        $this->table_allowed_actions = Auth::user()->user_type->actions_by_menu_option($menu_option->id)
            ->whereIn('actions.code', ['show_purchase',  'delete_purchase', 'payment_purchase','pdf_purchase'])->get();

        $this->arr_actions = [];
        foreach ($this->allowed_actions as $key => $value) {
            $this->arr_actions[] = $value->code;
        }
    }

}
