<?php

namespace App\Http\Livewire\System\BranchOffice;

use App\Http\LivewireRequest\System\BranchOffice\BranchOfficeRequest;
use App\Models\BranchOffice;
use App\Models\Department;
use App\Models\District;
use App\Models\Province;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Livewire\Component;

class MantComponent extends Component
{
    public $modal;
    public $event_modal;

    public $branch_office;

    public $name, $address, $phone, $company_id, $department_id, $province_id, $district_id;

    public $departments, $provinces, $districts;

    public function mount(BranchOffice $branch_office, bool $modal, string $event_modal)
    {
        $this->branch_office = $branch_office;
        $this->modal = $modal;
        $this->event_modal = $event_modal;

        $this->name = $this->branch_office->name;
        $this->address = $this->branch_office->address;
        $this->phone = $this->branch_office->phone;
        $this->company_id = $this->branch_office->company_id;
        $this->department_id = $this->branch_office->department_id;
        $this->province_id = $this->branch_office->province_id;
        $this->district_id = $this->branch_office->district_id;

        $this->departments = Department::orderBy('name')->get();
        if(!$this->department_id && count($this->departments) > 0){
            $this->department_id = $this->departments[0]->code;
        }
        $this->provinces = Province::where('department_id', $this->department_id)->orderBy('name')->get();
        if(!$this->province_id && count($this->provinces) > 0){
            $this->province_id = $this->provinces[0]->code;
        }
        $this->districts = District::where('province_id', $this->province_id)->orderBy('name')->get();
        if(!$this->district_id && count($this->districts) > 0){
            $this->district_id = $this->districts[0]->code;
        }

    }

    public function updatedDepartmentId($value){
        $this->provinces = Province::where('department_id', $value)->orderBy('name')->get();
        $this->province_id = (count($this->provinces) > 0) ? $this->provinces[0]->code : null;

        $this->districts = District::where('province_id', $this->province_id)->orderBy('name')->get();
        $this->district_id = (count($this->districts) > 0) ? $this->districts[0]->code : null;
    }

    public function updatedProvinceId($value){
        $this->districts = District::where('province_id', $value)->orderBy('name')->get();
        $this->district_id = (count($this->districts) > 0) ? $this->districts[0]->code : null;
    }

    public function render()
    {
        return view('livewire.system.branch-office.mant-component');
    }

    public function back_button(){
        if (!$this->modal){
            redirect()->route('branch-office');
        }else{
            // $this->emit($this->event_modal);
        }
    }

    public function save(BranchOfficeRequest $form_request){
        $inputs = $this->inputs_validation();

        Validator::make(
            $inputs,
            $form_request->rules(),
            $form_request->messages(),
            $form_request->validationAttributes(),
        )->validate();

        DB::beginTransaction();
        try {

            $branch_office = BranchOffice::create($inputs);

            DB::commit();

            if (!$this->modal){
                Session::flash('success', 'Se registró correctamente');
                redirect()->route('branch-office');
            }else{
                $this->reset(['name', 'address', 'phone', 'company_id', 'department_id', 'province_id', 'district_id']);
                $this->emit('alert-success', [
                    'title' => '¡Exito!',
                    'text' => 'Se registró correctamente',
                ]);
                $this->emit($this->event_modal, $branch_office->id);
            }


        } catch (\Exception $th) {

            DB::rollback();

            $this->emit('alert-error', [
                'title' => '¡Error!',
                'text' => 'Algo salio mal: ' . $th->getMessage(),
            ]);
        }
    }

    public function update(BranchOfficeRequest $form_request){
        $inputs = $this->inputs_validation();

        Validator::make(
            $inputs,
            $form_request->rules(),
            $form_request->messages(),
            $form_request->validationAttributes(),
        )->validate();

        DB::beginTransaction();
        try {

            $this->branch_office->update($inputs);

            DB::commit();

            Session::flash('success', 'Se editó correctamente');
            redirect()->route('branch-office');

        } catch (\Exception $th) {

            DB::rollback();

            $this->emit('alert-error', [
                'title' => '¡Error!',
                'text' => 'Algo salio mal: ' . $th->getMessage(),
            ]);
        }
    }

    private function inputs_validation(){
        return [
            "name" => mb_strtoupper($this->name),
            "address" => mb_strtoupper($this->address),
            "phone" => $this->phone,
            "company_id" => Auth::user()->branch_office->company_id,
            "department_id" => $this->department_id,
            "province_id" => $this->province_id,
            "district_id" => $this->district_id,
        ];
    }
}
