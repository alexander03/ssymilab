<?php

namespace App\Http\Livewire\Store\ReferralGuide;

use App\Http\Traits\General\StockTrait;
use App\Models\DocumentDetail;
use App\Models\Lot;
use App\Models\ReferralGuide;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class DeleteComponent extends Component
{
    use StockTrait;

    public $open = false;

    public $model_id = null;

    protected $listeners = [
        'toggle-modal' => 'toggle_modal'
    ];

    public function toggle_modal($model_id = null){

        $this->open = !$this->open;
        if($model_id){
            $this->model_id = $model_id;
        }

    }

    public function render()
    {
        return view('livewire.store.referral-guide.delete-component');
    }

    public function delete()
    {
        DB::beginTransaction();
        try {
            $referralGuide = ReferralGuide::find($this->model_id);

            $details = $referralGuide->details;

            foreach ($details as $detail) {
                $product_moves = DocumentDetail::product_moves($detail->id);
                foreach ($product_moves as $product_move) {
                    $product_move->update(['status' => 'A']);
                }
                if($referralGuide->dispatched=="Y" || $referralGuide->dispatched=="E"){//si esta despachado
                    $this->increase_stock($referralGuide, $detail);
                }
            }

            $referralGuide->update(["status" => "A"]);

            DB::commit();

            $this->emit('search-and-reset-pages');

            $this->open = false;

            $this->emit('alert-success', [
                'title' => '¡Exito!',
                'text' => 'Se anuló correctamente',
            ]);

        } catch (\Exception $th) {

            DB::rollback();

            $this->emit('alert-error', [
                'title' => '¡Error!',
                'text' => 'Algo salio mal: ' . $th->getMessage(),
            ]);
        }
    }
}
