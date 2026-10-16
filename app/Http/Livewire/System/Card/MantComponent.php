<?php

namespace App\Http\Livewire\System\Card;

use App\Http\LivewireRequest\System\Card\CardRequest;
use App\Models\Card;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Livewire\Component;

class MantComponent extends Component
{
    public $modal;
    public $event_modal;

    public $card;

    public $name;

    public function mount(Card $card, bool $modal, string $event_modal)
    {
        $this->card = $card;
        $this->modal = $modal;
        $this->event_modal = $event_modal;

        $this->name = $this->card->name;

    }

    public function render()
    {
        return view('livewire.system.card.mant-component');
    }

    public function back_button(){
        if (!$this->modal){
            redirect()->route('card');
        }else{
            // $this->emit($this->event_modal);
        }
    }

    public function save(CardRequest $form_request){
        $inputs = [
            "name" => mb_strtoupper($this->name),
        ];

        Validator::make(
            $inputs,
            $form_request->rules(),
            $form_request->messages(),
            $form_request->validationAttributes(),
        )->validate();

        DB::beginTransaction();
        try {

            $card = Card::updateOrCreate($inputs);

            DB::commit();

            if (!$this->modal){
                Session::flash('success', 'Se registró correctamente');
                redirect()->route('card');
            }else{
                $this->reset(['name']);
                $this->emit('alert-success', [
                    'title' => '¡Exito!',
                    'text' => 'Se registró correctamente',
                ]);
                $this->emit($this->event_modal, $card->id);
            }


        } catch (\Exception $th) {

            DB::rollback();

            $this->emit('alert-error', [
                'title' => '¡Error!',
                'text' => 'Algo salio mal: ' . $th->getMessage(),
            ]);
        }
    }

    public function update(CardRequest $form_request){
        $inputs = [
            "name" => mb_strtoupper($this->name),
        ];

        Validator::make(
            $inputs,
            $form_request->rules(),
            $form_request->messages(),
            $form_request->validationAttributes(),
        )->validate();

        DB::beginTransaction();
        try {

            $this->card->update($inputs);

            DB::commit();

            Session::flash('success', 'Se editó correctamente');
            redirect()->route('card');

        } catch (\Exception $th) {

            DB::rollback();

            $this->emit('alert-error', [
                'title' => '¡Error!',
                'text' => 'Algo salio mal: ' . $th->getMessage(),
            ]);
        }
    }
}
