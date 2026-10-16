<?php

namespace App\Http\Livewire\Maintenance\Reason;

use App\Http\LivewireRequest\Maintenance\Reason\ReasonRequest;
use App\Models\Reason;
use App\Models\DocumentType;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Livewire\Component;

class MantComponent extends Component
{
    public $modal;
    public $event_modal;

    public $reason,$document_types;

    public $name;
    public $document_type_id;

    public function mount(Reason $reason, bool $modal, string $event_modal)
    {
        $this->reason = $reason;
        $this->modal = $modal;
        $this->event_modal = $event_modal;

        $this->name = $this->reason->name;
        if(is_null($this->reason->document_type_id)){
            $this->document_type_id = 6;
        }else{
            $this->document_type_id = $this->reason->document_type_id;
        }
        $this->document_types = DocumentType::whereIn('id',[6,7])->orderBy('name')->get();

    }

    public function render()
    {
        return view('livewire.maintenance.reason.mant-component');
    }

    public function back_button(){
        if (!$this->modal){
            redirect()->route('reason');
        }else{
            // $this->emit($this->event_modal);
        }
    }

    public function save(ReasonRequest $form_request){
        $inputs = [
            "name" => mb_strtoupper($this->name),
            "document_type_id" => $this->document_type_id,
            "module_id" => 2,
        ];

        Validator::make(
            $inputs,
            $form_request->rules(),
            $form_request->messages(),
            $form_request->validationAttributes(),
        )->validate();

        DB::beginTransaction();
        try {

            $reason = Reason::create($inputs);

            DB::commit();

            if (!$this->modal){
                Session::flash('success', 'Se registró correctamente');
                redirect()->route('reason');
            }else{
                $this->reset(['name']);
                $this->emit('alert-success', [
                    'title' => '¡Exito!',
                    'text' => 'Se registró correctamente',
                ]);
                $this->emit($this->event_modal, $reason->id);
            }


        } catch (\Exception $th) {

            DB::rollback();

            $this->emit('alert-error', [
                'title' => '¡Error!',
                'text' => 'Algo salio mal: ' . $th->getMessage(),
            ]);
        }
    }

    public function update(ReasonRequest $form_request){
        $inputs = [
            "name" => mb_strtoupper($this->name),
            "document_type_id" => $this->document_type_id,
            "module_id" => 2,
        ];

        Validator::make(
            $inputs,
            $form_request->rules(),
            $form_request->messages(),
            $form_request->validationAttributes(),
        )->validate();

        DB::beginTransaction();
        try {

            $this->reason->update($inputs);

            DB::commit();

            Session::flash('success', 'Se editó correctamente');
            redirect()->route('reason');

        } catch (\Exception $th) {

            DB::rollback();

            $this->emit('alert-error', [
                'title' => '¡Error!',
                'text' => 'Algo salio mal: ' . $th->getMessage(),
            ]);
        }
    }
}
