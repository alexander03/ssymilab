<?php

namespace App\Http\Livewire\Equipment\Installation;

use App\Models\Schedule;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithFileUploads;

class UploadFilesComponent extends Component
{
    use WithFileUploads;

    public $open = false;
    public $image;

    public $model_id = null;

    protected $listeners = [
        'toggle-modal-upload' => 'toggle_modal_upload'
    ];

    public function render()
    {
        $schedule = null;
        if($this->model_id){
            $schedule = Schedule::find($this->model_id);
        }
        return view('livewire.equipment.installation.upload-files-component')->with(compact('schedule'));
    }

    public function toggle_modal_upload($model_id = null){

        $this->open = !$this->open;
        if($model_id){
            $this->model_id = $model_id;
        }

    }

    public function updatedImage()
    {
        $this->validate([
            'image' => 'nullable',
        ]);
    }

    public function upload_files()
    {
        DB::beginTransaction();
        try {
            $schedule = Schedule::find($this->model_id);

            if($this->image){
                $arr_original_file_name = explode(".",$this->image->getClientOriginalName());
                $name = "installation-". $schedule->id;
                $file_name = $name . "." . $arr_original_file_name[(count($arr_original_file_name) - 1)];
                $schedule->addMedia($this->image->getRealPath())->usingName($this->image->getClientOriginalName())->usingFileName($file_name)->toMediaCollection('installation');
            }

            DB::commit();

            $this->emit('search-and-reset-pages');

            $this->open = false;

            $this->emit('alert-success', [
                'title' => '¡Exito!',
                'text' => 'Se cargo el archivo correctamente',
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
