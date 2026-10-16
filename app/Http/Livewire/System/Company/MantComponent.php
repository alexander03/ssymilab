<?php

namespace App\Http\Livewire\System\Company;

use App\Http\LivewireRequest\System\Company\CompanyRequest;
use App\Models\Company;
use App\Models\Department;
use App\Models\District;
use App\Models\Province;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Livewire\Component;

class MantComponent extends Component
{
    public $company;

    public $ruc, $business_name, $trade_name, $address, $password, $authentication_mode, $username_sunat, $password_sunat, $department_id, $province_id, $district_id;

    public $departments, $provinces, $districts;

    public function mount(Company $company)
    {
        $this->company = $company;

        $this->ruc = $this->company->ruc;
        $this->business_name = $this->company->business_name;
        $this->trade_name = $this->company->trade_name;
        $this->address = $this->company->address;
        $this->password = $this->company->password;
        $this->authentication_mode = $this->company->authentication_mode;
        $this->username_sunat = $this->company->username_sunat;
        $this->password_sunat = $this->company->password_sunat;
        $this->department_id = $this->company->department_id;
        $this->province_id = $this->company->province_id;
        $this->district_id = $this->company->district_id;

        $this->departments = Department::orderBy('name')->get();
        $this->provinces = Province::where('department_id', $this->department_id)->orderBy('name')->get();
        $this->districts = District::where('province_id', $this->province_id)->orderBy('name')->get();

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
        return view('livewire.system.company.mant-component');
    }

    public function back_button(){
        redirect()->route('company');
    }

    public function update(CompanyRequest $form_request){
        $inputs = [
            "ruc" => $this->ruc,
            "business_name" => $this->business_name,
            "trade_name" => $this->trade_name,
            "address" => $this->address,
            "password" => $this->password,
            "authentication_mode" => $this->authentication_mode,
            "username_sunat" => $this->username_sunat,
            "password_sunat" => $this->password_sunat,
            "department_id" => $this->department_id,
            "province_id" => $this->province_id,
            "district_id" => $this->district_id,
        ];

        Validator::make(
            $inputs,
            $form_request->rules(),
            $form_request->messages(),
            $form_request->validationAttributes(),
        )->validate();

        DB::beginTransaction();
        try {

            $this->company->update($inputs);

            DB::commit();

            Session::flash('success', 'Se editó correctamente');
            redirect()->route('company');

        } catch (\Exception $th) {

            DB::rollback();

            $this->emit('alert-error', [
                'title' => '¡Error!',
                'text' => 'Algo salio mal: ' . $th->getMessage(),
            ]);
        }
    }
}
