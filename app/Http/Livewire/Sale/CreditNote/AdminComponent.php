<?php

namespace App\Http\Livewire\Sale\Creditnote;

use App\Http\Traits\General\SortTable;
use App\Http\Traits\Sale\CreditNoteTrait;
use App\Models\MenuOption;
use App\Models\CreditNote;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;
use App\Http\Traits\General\SessionFilter;

class AdminComponent extends Component
{
    use WithPagination;
    use SortTable;
    use CreditNoteTrait;
    use SessionFilter;

    protected $allowed_actions;
    protected $table_allowed_actions;
    protected $arr_actions;

    public $date_start, $date_end, $number, $supplier, $document_type_id, $search, $vendedor;
    public $document_types;

    public $rows = '10';

    protected $listeners = [
        'search-creditnote' => 'render',
        'search-and-reset-pages' => 'search_and_reset_page'
    ];

    protected $queryString = [
        'date_start' => ['except' => ''],
        'date_end' => ['except' => ''],
        'document_type_id' => ['except' => ''],
        'number' => ['except' => ''],
        'supplier' => ['except' => ''],
        'vendedor' => ['except' => ''],
        'rows' => ['except' => '10']
    ];

    public function mount()
    {
        $this->date_start = date('Y-m-d');
        $this->sort = "documents.created_at";
        $this->direction = "desc";
        $this->search_event = "search-creditnote";

        $this->document_types = CreditNote::list_document_types();
        $this->document_type_id = "";
        $this->session_route = 'creditnote';
        $this->init_session_filter(['date_start', 'date_end', 'document_type_id', 'number', 'supplier', 'vendedor', 'rows']);
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

    public function updatingPerson($value)
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
        $thead[]       = array('value' => 'Tipo de Doc.', 'colspan' => '1', 'link' => 'documents.document_type_id');
        $thead[]       = array('value' => 'Número', 'colspan' => '1', 'link' => 'documents.number');
        $thead[]       = array('value' => 'Fecha', 'colspan' => '1', 'link' => 'documents.date_time');
        $thead[]       = array('value' => 'Nro Ref', 'colspan' => '1', 'link' => 'documents2.number');
        $thead[]       = array('value' => 'Documento', 'colspan' => '1', 'link' => 'supplier_document');
        $thead[]       = array('value' => 'Cliente', 'colspan' => '1', 'link' => 'supplier_name');
        $thead[]       = array('value' => 'Vendedor', 'colspan' => '1', 'link' => 'documents.person_id');
        $thead[]       = array('value' => 'Moneda', 'colspan' => '1', 'link' => 'documents.currency_id');
        $thead[]       = array('value' => 'Total', 'colspan' => '1', 'link' => 'documents.total');
        $thead[]       = array('value' => 'Entrega', 'colspan' => '1', 'link' => null);
        if(count($this->table_allowed_actions)>0){
            $thead[]       = array('value' => 'Operaciones', 'colspan' => count($this->table_allowed_actions), 'link' => null);
        }

        $this->rows = (intval($this->rows) == 0) ? 10 : $this->rows;

        $list_items = [];
        $list_items = $this->search_creditnotes($this->date_start,$this->date_end,$this->document_type_id, $this->number,$this->supplier, $this->vendedor,null, null, $this->sort, $this->direction, $this->rows);
        return view('livewire.sale.creditnote.admin-component')->with(compact('thead', 'list_items', 'arr_actions'));
    }

    public function paginationView()
    {
        return 'components.general.pagination';
    }

    public function create_route()
    {
        redirect()->route('create-creditnote');
    }

    public function search_and_reset_page()
    {
        $this->resetPage();
    }

    protected function load_actions(){
        $menu_option = MenuOption::where('link', 'creditnote')->first();
        $this->allowed_actions = Auth::user()->user_type->actions_by_menu_option($menu_option->id)->get();
        $this->table_allowed_actions = Auth::user()->user_type->actions_by_menu_option($menu_option->id)
            ->whereIn('actions.code', ['show_creditnote',  'delete_creditnote','pdf_creditnote','create_creditnote','dispatched_creditnote'])->get();

        $this->arr_actions = [];
        foreach ($this->allowed_actions as $key => $value) {
            $this->arr_actions[] = $value->code;
        }
    }

}
