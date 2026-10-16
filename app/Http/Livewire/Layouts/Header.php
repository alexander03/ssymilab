<?php

namespace App\Http\Livewire\Layouts;

use App\Models\BranchOffice;
use App\Models\Currency;
use App\Models\ExchangeRate;
use App\Models\MenuOption;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Livewire\Component;

class Header extends Component
{
    protected $allowed_actions;
    protected $arr_actions;

    public $open;
    public $open_exchange;

    public $name;
    public $branch_office_id;
    public $branch_office_name;

    public $currencies;
    public $base_currency;
    public $exchanges = [];

    public $notifications = [];
    public $notifications_quantity;

    protected $listeners = ['update-profile' => 'update_profile', 'update-notifications' => 'update_notifications'];

    public function mount()
    {
        $this->currencies = Currency::where('base', '<>', 'Y')->get();
        $this->base_currency = Currency::base();

        foreach ($this->currencies as $key => $value) {
            $exchange_rate = ExchangeRate::today($value->id);
            $this->exchanges[$value->code] = $exchange_rate ? $exchange_rate->value : "0.00";
        }

        $this->update_notifications();

        $this->update_profile();
        
        //Session::flash('warning', 'Sres 20538910717  UNIMEDILAB S.A.C\nSu empresa mantiene una deuda de :\n. 3ra cuota del pago por desarrollo de proyecto de software:  500 soles\n\nSu cuenta será suspendida el Jueves 29/02/2024 a horas 12pm, solicitamos se sirva regularizar su deuda y así evitar la aplicación de esta medida, enviando a este WhatsApp el comprobante de pago.\nSi desea conocer más detalles sobre su deuda puede comunicarse con nosotros a los números 992441187 o 979293176\nAgradecemos su pronta atención.\n\nEl equipo de atención al cliente de Garzasoft');
    }

    public function render()
    {
        $this->load_actions();
        $arr_actions = $this->arr_actions;

        $this->branch_office_name = (Auth::user()->branch_office) ? Auth::user()->branch_office->name : "[ERROR - ELIMINADO]";
        $branch_offices = BranchOffice::orderBy('name')->get();

        return view('livewire.layouts.header')->with(compact('arr_actions', 'branch_offices'));
    }

    public function update_profile()
    {
        $user = Auth::user();
        $this->branch_office_id = $user->branch_office_id;
        $this->name = ($user->person->type=="1") ? trim("{$user->person->name} {$user->person->surname_father} {$user->person->surname_mother}") : ($user->person->business_name);
    }

    protected function load_actions(){
        $menu_option = MenuOption::where('link', 'branch-office')->first();
        $this->allowed_actions = Auth::user()->user_type->actions_by_menu_option($menu_option->id)->get();

        $this->arr_actions = [];
        foreach ($this->allowed_actions as $key => $value) {
            $this->arr_actions[] = $value->code;
        }
    }

    public function toggle_branch_modal(){
        $this->open = !$this->open;
    }

    public function toggle_exchange_modal(){
        $this->open_exchange = !$this->open_exchange;
        $this->currencies = Currency::where('base', '<>', 'Y')->get();
        $this->exchanges = [];

        foreach ($this->currencies as $key => $value) {
            $exchange_rate = ExchangeRate::today($value->id);
            $this->exchanges[$value->code] = $exchange_rate ? $exchange_rate->value : "0.00";
        }
    }

    public function change_exchange_rate(){
        $input = ['exchanges' =>  $this->exchanges];
        $rules = [];
        $rules['exchanges'] = ['required', 'array'];

        foreach ($this->currencies as $key => $value) {
            $rules['exchanges.'.$value->code] = ["required", "numeric"];
        }
        Validator::make(
            $input,
            $rules,
            [],
            ['exchanges' => "tipos de cambio", 'exchanges.USD' => "dólares", 'exchanges.EUR' => "euros"],
        )->validate();

        $this->load_actions();
        if (!in_array('change_exchange_rate', $this->arr_actions)){
            $this->emit('alert-error', [
                'title' => '¡Error!',
                'text' => 'Acceso denegado',
            ]);
            return true;
        }
        $this->resetErrorBag();

        DB::beginTransaction();
        try {
            foreach ($this->exchanges as $key => $value) {
                ExchangeRate::updateOrCreate([
                    'date' => date('Y-m-d'),
                    'currency_id' => Currency::where('code', $key)->first()->id,
                    'base_currency_id' => $this->base_currency->id,
                ],
                [
                    'value' => $value,
                    'user_id' => Auth::user()->id,
                ]
                );
            }
            DB::commit();

            $this->open_exchange = false;
            $this->emit('alert-success', [
                'title' => 'Exito!',
                'text' => 'Se registró el tipo de cambio diario',
            ]);

        } catch (\Exception $th) {

            DB::rollback();

            $this->emit('alert-error', [
                'title' => '¡Error!',
                'text' => 'Algo salio mal: ' . $th->getMessage(),
            ]);
        }

    }

    public function change_branch_office(){

        $input = ['branch_office_id' =>  $this->branch_office_id];

        Validator::make(
            $input,
            ['branch_office_id' => ['required', 'integer', 'exists:branch_offices,id']],
            [],
            ['branch_office_id' => "identificador de sucursal"],
        )->validate();

        $this->load_actions();
        if (!in_array('change_branch_office', $this->arr_actions)){
            $this->emit('alert-error', [
                'title' => '¡Error!',
                'text' => 'Acceso denegado',
            ]);
            return true;
        }

        DB::beginTransaction();
        try {
            $user = Auth::user();

            $user->update($input);

            DB::commit();

            $this->open = false;
            Session::flash('success', 'Se cambió la sucursal');
            redirect()->route('dashboard');

        } catch (\Exception $th) {

            DB::rollback();

            $this->emit('alert-error', [
                'title' => '¡Error!',
                'text' => 'Algo salio mal: ' . $th->getMessage(),
            ]);
        }

    }

    public function update_notifications(){

        $user = Auth::user();

        $this->notifications = Notification::search($user->id, null, null)->get();
        $this->notifications_quantity = count(Notification::search($user->id, null, 'N')->get());

    }

}
