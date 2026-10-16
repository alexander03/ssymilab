<?php

namespace App\Http\Livewire\System\Checkout;

use App\Http\LivewireRequest\System\Checkout\CheckoutRequest;
use App\Models\Checkout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Livewire\Component;

class MantComponent extends Component
{

    public $modal;
    public $event_modal;

    public $checkout;

    public $name, $serie, $serie_note;

    public function mount(Checkout $checkout, bool $modal, string $event_modal)
    {
        $this->checkout = $checkout;
        $this->modal = $modal;
        $this->event_modal = $event_modal;

        $this->name = $this->checkout->name;
        $this->serie = $this->checkout->serie;
        $this->serie_note = $this->checkout->serie_note;

    }
    public function render()
    {
        return view('livewire.system.checkout.mant-component');
    }

    public function back_button(){
        if (!$this->modal){
            redirect()->route('checkout');
        }else{
            // $this->emit($this->event_modal);
        }
    }

    public function save(CheckoutRequest $form_request){
        $user = Auth::user();

        $inputs = [
            "name" => mb_strtoupper($this->name),
            "serie" => mb_strtoupper($this->serie),
            "serie_note" => mb_strtoupper($this->serie_note),
            "status" => "C",
            "branch_office_id" => $user->branch_office_id,
        ];

        Validator::make(
            $inputs,
            $form_request->rules(),
            $form_request->messages(),
            $form_request->validationAttributes(),
        )->validate();

        DB::beginTransaction();
        try {

            $checkout = Checkout::updateOrCreate($inputs);

            DB::commit();

            if (!$this->modal){
                Session::flash('success', 'Se registró correctamente');
                redirect()->route('checkout');
            }else{
                $this->reset(['name']);
                $this->emit('alert-success', [
                    'title' => '¡Exito!',
                    'text' => 'Se registró correctamente',
                ]);
                $this->emit($this->event_modal, $checkout->id);
            }


        } catch (\Exception $th) {

            DB::rollback();

            $this->emit('alert-error', [
                'title' => '¡Error!',
                'text' => 'Algo salio mal: ' . $th->getMessage(),
            ]);
        }
    }

    public function update(CheckoutRequest $form_request){
        $inputs = [
            "name" => mb_strtoupper($this->name),
            "serie" => mb_strtoupper($this->serie),
            "serie_note" => mb_strtoupper($this->serie_note),
        ];

        Validator::make(
            $inputs,
            $form_request->rules(),
            $form_request->messages(),
            $form_request->validationAttributes(),
        )->validate();

        DB::beginTransaction();
        try {

            $this->checkout->update($inputs);

            DB::commit();

            Session::flash('success', 'Se editó correctamente');
            redirect()->route('checkout');

        } catch (\Exception $th) {

            DB::rollback();

            $this->emit('alert-error', [
                'title' => '¡Error!',
                'text' => 'Algo salio mal: ' . $th->getMessage(),
            ]);
        }
    }
}
