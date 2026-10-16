<?php

namespace App\Http\Livewire\Sale\Comisiones;

use App\Models\Card;
use App\Models\DocumentPayment;
use App\Models\Payment;
use App\Models\Sale;
use App\Models\TransferMethod;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class PaidComponent extends Component
{

    public $open = false;

    public $model_id = null;
    public $month = "";
    public $comision, $porcentaje;

    public $payments;

    protected $listeners = [
        'toggle-modal-paid' => 'toggle_modal'
    ];

    public function mount(){
        $this->comision = 0;
        $this->porcentaje = 3;
    }

    public function render()
    {

        $sale = null;
        if($this->model_id != null){
            $sale = Sale::find($this->model_id);
            $this->comision = round($sale->total*($this->porcentaje/100)/1.18,2);
        }
        return view('livewire.sale.comisiones.paid-component')->with(compact('sale'));
    }

    public function toggle_modal($model_id = null){

        $this->open = !$this->open;
        if($model_id){
            $this->model_id = $model_id;
        }
    }

    private function validate_payments(){
        $total = 0;
        if($this->month==""){
            $this->emit('alert-error', [
                'title' => 'Error!',
                'text' => 'El mes no puede ser vacio',
            ]);
            return false;
            
        }
        return true;
    }

    public function updatingPorcentaje($value)
    {
        $sale = Sale::find($this->model_id);
        $this->comision = round($sale->total*($this->porcentaje/100)/1.18,2);
    }

    public function save(){
        if(!$this->validate_payments()){
            return true;
        }
        DB::beginTransaction();
        try {
            $sale = Sale::find($this->model_id);
            $sale->paid_comision = $this->month."-01";
            $sale->percentage_comision = $this->porcentaje;
            $sale->update(["paid_comision" => $this->month."-01","percentage_comision" => $this->porcentaje]);

            DB::commit();

            $this->comision = 0;
            $this->porcentaje = 3;
            $this->month = "";

            $this->emit('search-and-reset-pages');

        } catch (\Exception $th) {

            DB::rollback();

            $this->emit('alert-error', [
                'title' => '¡Error!',
                'text' => 'Algo salio mal: ' . $th->getMessage(),
            ]);
        }
    }
}
