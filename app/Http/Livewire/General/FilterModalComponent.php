<?php

namespace App\Http\Livewire\General;

use Livewire\Component;

class FilterModalComponent extends Component
{
    public $open = false;
    public $array_filter = [];
    public $data = [];
    private $data_types = [
        "string" => ["contains", "equals", "different"],
        "integer" => ["equals", "different", "greater_than", "greater_equals", "less_than", "less_equals"],
        "decimal" => ["equals", "different", "greater_than", "greater_equals", "less_than", "less_equals"],
        "date" => ["equals", "different", "greater_than", "greater_equals", "less_than", "less_equals"],
    ];

    public $column = "";
    public $data_type = "";
    public $operations = [];
    public $operation = "";
    public $value = "";

    public $maxWidth = "3xl";


    public $dictionary = [
        "equals" => "Igual que",
        "contains" => "Contiene",
        "different" => "Diferente que",
        "greater_than" => "Mayor que",
        "greater_equals" => "Mayor igual que",
        "less_than" => "Menor que",
        "less_equals" => "Menor igual que",
    ];


    protected $listeners = ['toggle-modal-filter' => 'toggle_modal_filter'];

    public function mount($data)
    {
        $this->data = $data;
        $this->column = $data[array_key_first($data)]["column"];
        $this->data_type = $data[array_key_first($data)]["data_type"];
        $this->operations = $this->data_types[$data[array_key_first($data)]["data_type"]];
        $this->operation = $this->operations[0];
    }

    public function render()
    {

        $data_types = $this->data_types;
        return view('livewire.general.filter-modal-component')->with(compact("data_types"));
    }

    public function toggle_modal_filter()
    {
        $this->open = !$this->open;
    }

    public function change_column()
    {
        $this->data_type = $this->data[$this->column]["data_type"];
        $this->operations = $this->data_types[$this->data[$this->column]["data_type"]];
        $this->operation = $this->operations[0];
        $this->value = "";
    }

    public function add_filter(){

        if($this->value == ""){
            $this->emit('alert-error', [
                'title' => '¡Error!',
                'text' => 'Debe colocar un valor',
            ]);
        }else{
            $this->array_filter[$this->column][] = [
                "operation" => $this->operation,
                "value" => $this->value
            ];

            $this->emit("filter-apply", $this->array_filter);
        }

    }

}
