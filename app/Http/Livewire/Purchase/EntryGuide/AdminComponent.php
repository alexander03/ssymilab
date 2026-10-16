<?php

namespace App\Http\Livewire\Purchase\EntryGuide;

use App\Http\Traits\General\SortTable;
use App\Http\Traits\Purchase\EntryGuideTrait;
use App\Models\EntryGuide;
use App\Models\MenuOption;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class AdminComponent extends Component
{
    use WithPagination;
    use SortTable;
    use EntryGuideTrait;

    protected $allowed_actions;
    protected $table_allowed_actions;
    protected $arr_actions;

    public $date_start, $date_end, $number, $supplier, $document_type_id;
    public $document_types;

    public $rows = '10';

    protected $listeners = [
        'search-entry-guide' => 'render',
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
        $this->search_event = "search-entry-guide";

        $this->document_types = EntryGuide::list_document_types();
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
        $thead[]       = array('value' => 'Compra', 'colspan' => '1', 'link' => 'documents.document_id');
        $thead[]       = array('value' => 'Fecha', 'colspan' => '1', 'link' => 'documents.date_time');
        $thead[]       = array('value' => 'Documento', 'colspan' => '1', 'link' => 'supplier_document');
        $thead[]       = array('value' => 'Proveedor', 'colspan' => '1', 'link' => 'supplier_name');
        $thead[]       = array('value' => 'Comentario', 'colspan' => '1', 'link' => 'supplier_name');
        $thead[]       = array('value' => 'Entrega', 'colspan' => '1', 'link' => null);
        $thead[]       = array('value' => 'Estado', 'colspan' => '1', 'link' => null);
        if(count($this->table_allowed_actions)>0){
            $thead[]       = array('value' => 'Operaciones', 'colspan' => count($this->table_allowed_actions)+1, 'link' => null);
        }

        $this->rows = (intval($this->rows) == 0) ? 10 : $this->rows;

        $list_items = [];
        $list_items = $this->search_entry_guides($this->date_start,$this->date_end,$this->document_type_id, $this->number,$this->supplier, $this->sort, $this->direction, $this->rows);


        return view('livewire.purchase.entry-guide.admin-component')->with(compact('thead', 'list_items', 'arr_actions'));
    }

    public function paginationView()
    {
        return 'components.general.pagination';
    }

    public function create_route()
    {
        redirect()->route('create-entry-guide');
    }

    public function search_and_reset_page()
    {
        $this->resetPage();
    }

    protected function load_actions(){
        $menu_option = MenuOption::where('link', 'entry-guide')->first();
        $this->allowed_actions = Auth::user()->user_type->actions_by_menu_option($menu_option->id)->get();
        $this->table_allowed_actions = Auth::user()->user_type->actions_by_menu_option($menu_option->id)
            ->whereIn('actions.code', ['show_entry_guide', 'delete_entry_guide', 'dispatched_entry_guide','pdf_entryguide','pdf2_entryguide'])->get();

        $this->arr_actions = [];
        foreach ($this->allowed_actions as $key => $value) {
            $this->arr_actions[] = $value->code;
        }
    }

}
