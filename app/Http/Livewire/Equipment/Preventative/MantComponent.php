<?php

namespace App\Http\Livewire\Equipment\Preventative;

use App\Models\Job;
use App\Models\Maintenance;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class MantComponent extends Component
{
    public $modal;
    public $event_modal;

    public $product;
    public $maintenance;

    public $product_name;
    public $period;
    public $job_name;
    public $jobs_list;

    public function mount(Maintenance $maintenance, Product $product, bool $modal, string $event_modal)
    {
        $this->product = $product;
        $this->maintenance = $maintenance;
        $this->modal = $modal;
        $this->event_modal = $event_modal;

        $this->product_name = trim($product->name.' '.$product->model.' '.$product->brand->name);
        if($maintenance->id != null){
            $this->period = $maintenance->period;
            $this->jobs_list = $maintenance->jobs->map(function($job){
                return [
                    "job_name" => $job->name,
                    "job_type" => $job->type,
                ];
            })->toArray();
        }else{
            $this->period = "0";
            $this->jobs_list = [];
        }

    }

    public function render()
    {
        return view('livewire.equipment.preventative.mant-component');
    }

    public function back_button(){
        if (!$this->modal){
            redirect()->route('preventative');
        }else{
            // $this->emit($this->event_modal);
        }
    }

    public function add_job(){
        if($this->job_name != ""){
            $this->jobs_list[] = [
                "job_name" => $this->job_name,
                "job_type" => "R"
            ];
            $this->job_name = "";
        }
    }

    public function quit_job($key){
        unset($this->jobs_list[$key]);
    }

    public function save(){
        $inputs = [
            "product_id" => $this->product->id,
            "period" => $this->period,
        ];

        // Validator::make(
        //     $inputs,
        //     $form_request->rules(),
        //     $form_request->messages(),
        //     $form_request->validationAttributes(),
        // )->validate();

        DB::beginTransaction();
        try {

            if(!$this->maintenance->id){
                $maintenance = Maintenance::create($inputs);
            }else{
                $maintenance = Maintenance::find($this->maintenance->id);
                $maintenance->update($inputs);
                Job::where('maintenance_id', $maintenance->id)->delete();
            }

            foreach ($this->jobs_list as $key => $value) {
                Job::create([
                    "maintenance_id" => $maintenance->id,
                    "name" => $value["job_name"],
                    "type" => $value["job_type"],
                ]);
            }

            DB::commit();

            if (!$this->modal){
                Session::flash('success', 'Se registró correctamente');
                redirect()->route('preventative');
            }


        } catch (\Exception $th) {

            DB::rollback();

            $this->emit('alert-error', [
                'title' => '¡Error!',
                'text' => 'Algo salio mal: ' . $th->getMessage(),
            ]);
        }
    }

}
