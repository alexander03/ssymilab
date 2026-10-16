<?php

namespace App\Http\Livewire\Purchase\Purchase;

use App\Models\DocumentDetail;
use App\Models\EntryGuide;
use App\Models\Purchase;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class DeleteComponent extends Component
{
    public $open = false;

    public $model_id = null;

    protected $listeners = [
        'toggle-modal' => 'toggle_modal'
    ];

    public function render()
    {
        return view('livewire.purchase.purchase.delete-component');
    }

    public function toggle_modal($model_id = null){

        $this->open = !$this->open;
        if($model_id){
            $this->model_id = $model_id;
        }

    }

    public function delete()
    {
        DB::beginTransaction();
        try {
            $purchase = Purchase::find($this->model_id);

            $entry_guides = EntryGuide::where('document_id', $this->model_id)->where('status', '<>', 'A')->get();

            if(count($entry_guides) > 0){
                throw new \Exception("Existen guías activas de esta compra.");
            }

            $purchase->update(["status" => "A"]);

            $requirement = $purchase->document;

            if($requirement){
                $requirement->update(["document_id" => null, "status" => "P"]);
            }

            $details = $purchase->details;

            foreach ($details as $detail) {
                $product_moves = DocumentDetail::product_moves($detail->id);
                foreach ($product_moves as $product_move) {
                    if($product_move->lot){
                        $product_move->lot->update(['status' => 'A',]);
                    }
                    if($product_move->product_serie){
                        $product_move->product_serie->update(['status' => 'A']);
                    }
                    $product_move->update(['status' => 'A']);
                }
            }

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
