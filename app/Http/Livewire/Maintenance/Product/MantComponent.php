<?php

namespace App\Http\Livewire\Maintenance\Product;

use App\Http\LivewireRequest\Maintenance\Product\ProductRequest;
use App\Models\ActiveIngredient;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Currency;
use App\Models\Person;
use App\Models\Product;
use App\Models\ProductIngredient;
use App\Models\ProductUnit;
use App\Models\Role;
use App\Models\Unit;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Livewire\Component;
use Livewire\WithFileUploads;

class MantComponent extends Component
{
    use WithFileUploads;

    protected $aditional_actions;
    protected $arr_actions;

    public $modal;
    public $event_modal;

    public $modal_category = false;
    public $modal_brand = false;
    public $modal_supplier = false;
    public $modal_unit = false;
    public $name_modal_unit = null;
    public $modal_active_ingredient = false;

    public $product;

    public $code, $barcode, $type, $name, $abbreviation, $igv, $security_stock, $affects_stock, $specifications, $status;
    public $category_id, $supplier_id, $brand_id, $base_unit_id, $currency_id, $source, $presentation, $model,$rs,$condition,$currency2_id;
    public $sell_price,$purchase_price, $show_purchase_price,$sell_price_min,$win_percentage,$win_percentage_min;

    public $image;
    public $imageUploaded;

    public $types, $categories, $suppliers, $brands, $units, $currencies;

    public $search_ingredient = "";
    public $search_list_ingredient = [];
    public $active_ingredients = [];

    public $presentation_unit_id;
    public $presentation_currency_id;
    public $product_units = [];

    protected $listeners = [
        'toggle-modal-category' => 'toggle_category',
        'toggle-modal-brand' => 'toggle_brand',
        'toggle-modal-supplier' => 'toggle_supplier',
        'toggle-modal-unit' => 'toggle_unit',
        'toggle-modal-active-ingredient' => 'toggle_active_ingredient',
    ];

    public function updatingSearchIngredient($value)
    {
        if($value != ""){
            $this->search_list_ingredient = ActiveIngredient::search($value)->orderBy('active_ingredients.name', 'ASC')->limit(5)->get();
        }else{
            $this->search_list_ingredient = [];
        }
    }

    public function updatedImage()
    {
        $this->validate([
            'image' => 'nullable|image',
        ]);
    }

    public function hydrate(){
        $this->resetValidation();
        $this->emit('select2');
    }

    public function mount(Product $product, bool $modal, string $event_modal)
    {
        $this->product = $product;
        $this->modal = $modal;
        $this->event_modal = $event_modal;

        $this->code = $this->product->code;
        $this->barcode = $this->product->barcode;
        $this->type = $this->product->type;
        $this->name = $this->product->name;
        $this->abbreviation = $this->product->abbreviation;
        $this->igv = $this->product->igv;
        $this->affects_stock = $this->product->affects_stock;
        $this->security_stock = sprintf('%g', $this->product->security_stock);
        $this->specifications = $this->product->specifications;
        $this->status = $this->product->status;
        $this->source = $this->product->source;
        $this->presentation = $this->product->presentation;
        $this->model = $this->product->model;
        $this->rs = $this->product->rs;
        $this->condition = $this->product->condition;
        $this->currency2_id = $this->product->currency_id;

        $this->category_id = $this->product->category_id;
        $this->supplier_id = $this->product->supplier_id;
        $this->brand_id = $this->product->brand_id;
        $this->base_unit_id = $this->product->base_unit_id;
        $this->currency_id = $this->product->base_currency_id;

        $band = Auth::user()->user_type->actions()->whereIn('actions.code', ['purchase_price'])->first();
        if(is_null($band)){
            $this->show_purchase_price=false;
        }else{
            $this->show_purchase_price=true;
        }

        if(count($this->product->getMedia('images')) > 0){
            $this->imageUploaded = true;
        }

        if($product->id){
            /*if($product->id==353){
                $lista = Product::orderBy('name','asc')->whereNotIn('id',[353])->get();
                foreach ($lista as $key => $value) {
                    $value->code = $this->getNextCode();
                    $value->save();
                }
            }*/
            $base_product_unit = $product->base_product_unit();
            $this->sell_price = sprintf('%g', $base_product_unit->sell_price);
            $this->sell_price_min = sprintf('%g', $base_product_unit->sell_price_min);
            $this->win_percentage = sprintf('%g', $base_product_unit->win_percentage);
            $this->win_percentage_min = sprintf('%g', $base_product_unit->win_percentage_min);
            $this->purchase_price = sprintf('%g', $base_product_unit->purchase_price);
            $this->active_ingredients = $product->active_ingredients->toArray();
            foreach ($product->product_units() as $key => $product_unit) {
                $this->product_units[] = [
                    "unit_id" => $product_unit->unit_id,
                    "unit_name" => $product_unit->unit->name,
                    "currency_id" => $product_unit->currency_id,
                    "currency_name" => $product_unit->currency->name . " ({$product_unit->currency->symbol})",
                    "formula" => sprintf('%g', $product_unit->formula),
                    "sell_price" => sprintf('%g', $product_unit->sell_price),
                    "sell_price_min" => sprintf('%g', $product_unit->sell_price_min),
                    "win_percentage" => sprintf('%g', $product_unit->win_percentage),
                    "win_percentage_min" => sprintf('%g', $product_unit->win_percentage_min),
                    "purchase_price" => sprintf('%g', $product_unit->purchase_price),
                ];
            }
        }else{
            $this->code = $this->getNextCode();
        }


        $this->types = ["R" => "DISPOSITIVO MEDICO", "M" => "EQUIPO", "S"=>"SERVICIOS", "E"=>"REPUESTOS", "C"=>"COMPUTO", "O"=>"CONSUMIBLE"];
        $this->categories = Category::orderBy('name')->get();
        $this->brands = Brand::orderBy('name')->get();
        $this->suppliers = Person::with_rol('P')->get();
        $this->units = Unit::orderBy('name')->get();
        $this->currencies = Currency::order_currency()->get();
    }

    public function render()
    {
        $this->load_actions();
        $arr_actions = $this->arr_actions;

        if(!$this->type){
            $this->type = "R";
        }
        if(!$this->category_id && count($this->categories) > 0){
            $this->category_id = $this->categories[0]->id;
        }
        if(!$this->supplier_id && count($this->suppliers) > 0){
            $this->supplier_id = $this->suppliers[0]->id;
        }
        if(!$this->brand_id && count($this->brands) > 0){
            $this->brand_id = $this->brands[0]->id;
        }
        if(!$this->base_unit_id && count($this->units) > 0){
            $this->base_unit_id = $this->units[0]->id;
        }
        if(!$this->currency_id && count($this->currencies) > 0){
            $this->currency_id = $this->currencies[0]->id;
        }
        if(!$this->presentation_unit_id && count($this->units) > 0){
            $this->presentation_unit_id = $this->units[0]->id;
        }
        if(!$this->presentation_currency_id && count($this->currencies) > 0){
            $this->presentation_currency_id = $this->currencies[0]->id;
        }
        if(!$this->security_stock){
            $this->security_stock = 0;
        }
        if(!$this->sell_price){
            $this->sell_price = 0;
        }
        if(!$this->sell_price_min){
            $this->sell_price_min = 0;
        }
        if(!$this->win_percentage){
            $this->win_percentage = 0;
        }
        if(!$this->win_percentage_min){
            $this->win_percentage_min = 0;
        }
        if(!$this->purchase_price){
            $this->purchase_price = 0;
        }
        if($this->affects_stock === null){
            $this->affects_stock = "Y";
        }
        if($this->igv === null){
            $this->igv = "10";
        }
        $category = new Category();
        $brand = new Brand();
        $unit = new Unit();
        $person = new Person();
        $active_ingredient = new ActiveIngredient();
        $role_id = Role::where('code', 'P')->first()->id;
        return view('livewire.maintenance.product.mant-component')->with(compact('category', 'brand', 'unit', 'person', 'active_ingredient', 'role_id', 'arr_actions'));
    }

    public function back_button(){
        if (!$this->modal){
            redirect()->route('product');
        }else{
            // $this->emit($this->event_modal);
        }
    }

    public function calcularPrecioVenta()
    {
        if($this->win_percentage != "" && ($this->win_percentage+0)>0 && ($this->purchase_price+0)>0){
            $this->sell_price = round($this->purchase_price * (1+$this->win_percentage/100),2);
        }
        if($this->win_percentage_min != "" && ($this->win_percentage_min+0)>0 && ($this->purchase_price+0)>0){
            $this->sell_price_min = round($this->purchase_price * (1+$this->win_percentage_min/100),2);
        }
    }

    public function select_active_ingredient($active_ingredient_id){
        $active_ingredient = ActiveIngredient::find($active_ingredient_id);
        if($active_ingredient){
            foreach ($this->active_ingredients as $key => $value) {
                if($value["id"] == $active_ingredient_id){
                    $this->emit('alert-warning', [
                        'title' => '¡Cuidado!',
                        'text' => 'Ya seleccionó ese registro',
                    ]);
                    return true;
                }
            }
            $this->active_ingredients[] = $active_ingredient->toArray();
        }
    }

    public function quit_active_ingredient($active_ingredient_id){
        $key_id = null;
        foreach ($this->active_ingredients as $key => $value) {
            if($value["id"] == $active_ingredient_id){
                $key_id = $key;
            }
        }
        unset($this->active_ingredients[$key_id]);
    }

    public function getNextCode(){
        $user = Auth::user();
        $data = Product::orderBy('code', 'DESC')->orderBy('name','asc')->first();//where('branch_office_id', $user->branch_office_id)
        if($data){
            $number = (int) substr($data->code,0,8);
            return str_pad(($number + 1), 8, '0', STR_PAD_LEFT);
        }else{
            return str_pad((0 + 1), 8, '0', STR_PAD_LEFT);
        }

        return "00000001";
    }

    public function add_product_unit(){
        if($this->base_unit_id == $this->presentation_unit_id && $this->currency_id == $this->presentation_currency_id){
            $this->emit('alert-warning', [
                'title' => '¡Cuidado!',
                'text' => 'Esa presentación es la base.',
            ]);
            return true;
        }
        foreach ($this->product_units as $key => $value) {
            if($value["unit_id"] == $this->presentation_unit_id && $value["currency_id"] == $this->presentation_currency_id){
                $this->emit('alert-warning', [
                    'title' => '¡Cuidado!',
                    'text' => 'Ya seleccionó esa combinación.',
                ]);
                return true;
            }
        }
        $unidad = Unit::find($this->presentation_unit_id);
        $currency = Currency::find($this->presentation_currency_id);
        if($unidad && $currency){
            $this->product_units[] = [
                "unit_id" => $unidad->id,
                "unit_name" => $unidad->name,
                "currency_id" => $currency->id,
                "currency_name" => $currency->name . " ({$currency->symbol})",
                "formula" => "1",
                "sell_price" => "0.00",
                "sell_price_min" => "0.00",
                "win_percentage" => "0.00",
                "win_percentage_min" => "0.00",
                "purchase_price" => "0.00",
            ];
        }else{
            $this->emit('alert-warning', [
                'title' => '¡Cuidado!',
                'text' => 'No se encontró alguna opción seleccionada.',
            ]);
            return true;
        }
    }

    public function quit_product_unit($key){
        unset($this->product_units[$key]);
    }

    public function save($specifications, ProductRequest $form_request){
        $this->specifications = $specifications;
        $this->affects_stock = "Y";
        if($this->type == "S"){
            $this->affects_stock = "N";
        }
        Validator::make(
            $this->inputs_validation(),
            $form_request->rules(),
            $form_request->messages(),
            $form_request->validationAttributes(),
        )->validate();


        DB::beginTransaction();
        try {

            $product = Product::create($this->input_create_product());

            if($this->image){
                $product->clearMediaCollection('images');
                $arr_original_file_name = explode(".",$this->image->getClientOriginalName());
                $name = "product-". $product->id;
                $file_name = $name . "." . $arr_original_file_name[(count($arr_original_file_name) - 1)];
                $product->addMedia($this->image->getRealPath())->usingName($name)->usingFileName($file_name)->toMediaCollection('images');
            }
            /*if(Auth::user()->branch_office_id==1){
                $branch_office_id=3;
            }else{
                $branch_office_id=1;
            }
            $product2 = Product::create($this->input_create_product2($branch_office_id));

            /*if($product->type == "R"){
                foreach ($this->active_ingredients as $key => $active_ingredient) {
                    ProductIngredient::create([
                        'active_ingredient_id' => $active_ingredient['id'],
                        'product_id' => $product->id
                    ]);
                }
            }*/

            /*ProductUnit::create([
                'formula' => 1,
                'purchase_price' => $this->purchase_price,
                'sell_price' => $this->sell_price,
                'sell_price_min' => $this->sell_price_min,
                'win_percentage' => $this->win_percentage,
                'win_percentage_min' => $this->win_percentage_min,
                'whosale_price' => $this->sell_price,
                'branch_office_id' => $product2->branch_office_id,
                'unit_id' => $this->base_unit_id,
                'product_id' => $product2->id,
                'base_unit_id' => $product2->base_unit_id,
                'currency_id' => $this->currency_id,
            ]);*/

            ProductUnit::create([
                'formula' => 1,
                'purchase_price' => $this->purchase_price,
                'sell_price' => $this->sell_price,
                'sell_price_min' => $this->sell_price_min,
                'win_percentage' => $this->win_percentage,
                'win_percentage_min' => $this->win_percentage_min,
                'whosale_price' => $this->sell_price,
                'branch_office_id' => $product->branch_office_id,
                'unit_id' => $this->base_unit_id,
                'product_id' => $product->id,
                'base_unit_id' => $product->base_unit_id,
                'currency_id' => $this->currency_id,
            ]);

            foreach ($this->product_units as $key => $product_unit) {
                ProductUnit::create([
                    'formula' => $product_unit['formula'],
                    'purchase_price' => 0.00,
                    'sell_price' => $product_unit['sell_price'],
                    'sell_price_min' => $product_unit['sell_price_min'],
                    'win_percentage' => $product_unit['win_percentage'],
                    'win_percentage_min' => $product_unit['win_percentage_min'],
                    'whosale_price' => $product_unit['sell_price'],
                    'branch_office_id' => $product->branch_office_id,
                    'unit_id' => $product_unit['unit_id'],
                    'product_id' => $product->id,
                    'base_unit_id' => $product->base_unit_id,
                    'currency_id' => $product_unit['currency_id'],
                ]);

                /*ProductUnit::create([
                    'formula' => $product_unit['formula'],
                    'purchase_price' => 0.00,
                    'sell_price' => $product_unit['sell_price'],
                    'sell_price_min' => $product_unit['sell_price_min'],
                    'win_percentage' => $product_unit['win_percentage'],
                    'win_percentage_min' => $product_unit['win_percentage_min'],
                    'whosale_price' => $product_unit['sell_price'],
                    'branch_office_id' => $product2->branch_office_id,
                    'unit_id' => $product_unit['unit_id'],
                    'product_id' => $product2->id,
                    'base_unit_id' => $product2->base_unit_id,
                    'currency_id' => $product_unit['currency_id'],
                ]);*/
            }

            DB::commit();
            $this->reset(['image']);

            if (!$this->modal){
                Session::flash('success', 'Se registró correctamente');
                redirect()->route('product');
            }else{
                $this->emit('alert-success', [
                    'title' => '¡Exito!',
                    'text' => 'Se registró correctamente',
                ]);
                $this->emit($this->event_modal, $product->id);
            }

        } catch (\Exception $th) {

            DB::rollback();

            $this->emit('alert-error', [
                'title' => '¡Error!',
                'text' => 'Algo salio mal: ' . $th->getMessage(),
            ]);
        }
    }

    public function update($specifications, ProductRequest $form_request){
        $this->specifications = $specifications;
        $this->affects_stock = "Y";
        if($this->type == "S"){
            $this->affects_stock = "N";
        }
        Validator::make(
            $this->inputs_validation(),
            $form_request->rules(),
            $form_request->messages(),
            $form_request->validationAttributes(),
        )->validate();

        DB::beginTransaction();
        try {

            $this->product->update($this->input_edit_product());

            /*if($this->product->type == "R"){
                ProductIngredient::where('product_id', $this->product->id)->delete();
                foreach ($this->active_ingredients as $key => $active_ingredient) {
                    ProductIngredient::create([
                        'active_ingredient_id' => $active_ingredient['id'],
                        'product_id' => $this->product->id
                    ]);
                }
            }*/

            $base_unit = $this->product->base_product_unit();
            if(!is_null($base_unit)){
                $base_unit->update([
                    'sell_price' => $this->sell_price,
                    'sell_price_min' => $this->sell_price_min,
                    'win_percentage' => $this->win_percentage,
                    'win_percentage_min' => $this->win_percentage_min,
                    'purchase_price' => $this->purchase_price,
                    'whosale_price' => $this->sell_price,
                ]);
            }else{
                $base_unit = ProductUnit::create([
                    'formula' => 1,
                    'purchase_price' => $this->purchase_price,
                    'sell_price' => $this->sell_price,
                    'sell_price_min' => $this->sell_price_min,
                    'win_percentage' => $this->win_percentage,
                    'win_percentage_min' => $this->win_percentage_min,
                    'whosale_price' => $this->sell_price,
                    'branch_office_id' => $this->product->branch_office_id,
                    'unit_id' => $this->product->base_unit_id,
                    'product_id' => $this->product->id,
                    'base_unit_id' => $this->product->base_unit_id,
                    'currency_id' => $this->currency_id,
                ]);
            }

            if(!$this->imageUploaded){
                $this->product->clearMediaCollection('images');
            }

            if($this->image){
                $this->product->clearMediaCollection('images');
                $arr_original_file_name = explode(".",$this->image->getClientOriginalName());
                $name = "product-". $this->product->id;
                $file_name = $name . "." . $arr_original_file_name[(count($arr_original_file_name) - 1)];
                $this->product->addMedia($this->image->getRealPath())->usingName($name)->usingFileName($file_name)->toMediaCollection('images');
            }

            ProductUnit::where('product_id', $this->product->id)->where('id', '<>', $base_unit->id)->delete();

            foreach ($this->product_units as $key => $product_unit) {
                ProductUnit::create([
                    'formula' => $product_unit['formula'],
                    'purchase_price' => 0.00,
                    'sell_price' => $product_unit['sell_price'],
                    'sell_price_min' => $product_unit['sell_price_min'],
                    'win_percentage' => $product_unit['win_percentage'],
                    'win_percentage_min' => $product_unit['win_percentage_min'],
                    'whosale_price' => $product_unit['sell_price'],
                    'branch_office_id' => $this->product->branch_office_id,
                    'unit_id' => $product_unit['unit_id'],
                    'product_id' => $this->product->id,
                    'base_unit_id' => $this->product->base_unit_id,
                    'currency_id' => $product_unit['currency_id'],
                ]);
            }

            DB::commit();
            $this->reset(['image']);
            Session::flash('success', 'Se editó correctamente');
            redirect()->route('product');

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
            "type" => $this->type,
            "category_id" => $this->category_id,
            "brand_id" => $this->brand_id,
            "supplier_id" => $this->supplier_id,
            "code" => $this->code,
            "barcode" => $this->barcode,
            "name" => $this->name,
            "specifications" =>  $this->specifications,
            "active_ingredients" => $this->active_ingredients,
            "base_unit_id" => $this->base_unit_id,
            "currency_id" => $this->currency_id,
            "sell_price" => $this->sell_price,
            "sell_price_min" => $this->sell_price_min,
            "win_percentage" => $this->win_percentage,
            "win_percentage_min" => $this->win_percentage_min,
            "purchase_price" => $this->purchase_price,
            "igv" => $this->igv,
            "affects_stock" => $this->affects_stock,
            "security_stock" => $this->security_stock,
            "product_units" => $this->product_units,
        ];
    }

    private function input_create_product(){
        return [
            'code' => trim($this->code=="")?$this->getNextCode():$this->code,
            'barcode' => $this->barcode,
            'type' => $this->type,
            'name' => mb_strtoupper($this->name),
            'abbreviation' => null,
            'igv' => $this->igv,
            'affects_stock' => $this->affects_stock,
            'security_stock' => ($this->affects_stock == "Y") ? $this->security_stock : "0.00",
            'specifications' => $this->specifications,
            'status' => "Y",
            'branch_office_id' => Auth::user()->branch_office_id,
            'brand_id' => $this->brand_id,
            'category_id' => $this->category_id,
            'supplier_id' => $this->supplier_id,
            'base_unit_id' => $this->base_unit_id,
            'base_currency_id' => $this->currency_id,
            'source' => $this->source,
            'presentation' => $this->presentation,
            'model' => $this->model,
            'rs' => $this->rs,
            'condition' => $this->condition,
            'currency_id' => $this->currency2_id,
        ];
    }

    private function input_create_product2($branch_office_id){
        return [
            'code' => trim($this->code=="")?$this->getNextCode():$this->code,
            'barcode' => $this->barcode,
            'type' => $this->type,
            'name' => mb_strtoupper($this->name),
            'abbreviation' => null,
            'igv' => $this->igv,
            'affects_stock' => $this->affects_stock,
            'security_stock' => ($this->affects_stock == "Y") ? $this->security_stock : "0.00",
            'specifications' => $this->specifications,
            'status' => "Y",
            'branch_office_id' => $branch_office_id,
            'brand_id' => $this->brand_id,
            'category_id' => $this->category_id,
            'supplier_id' => $this->supplier_id,
            'base_unit_id' => $this->base_unit_id,
            'base_currency_id' => $this->currency_id,
            'source' => $this->source,
            'presentation' => $this->presentation,
            'model' => $this->model,
            'rs' => $this->rs,
            'condition' => $this->condition,
            'currency_id' => $this->currency2_id,
        ];
    }

    private function input_edit_product(){
        return [
            'code' => $this->code,
            'barcode' => $this->barcode,
            'type' => $this->product->type,
            'name' => mb_strtoupper($this->name),
            'abbreviation' => null,
            'igv' => $this->igv,
            'affects_stock' => $this->affects_stock,
            'security_stock' => ($this->affects_stock == "Y") ? $this->security_stock : "0.00",
            'specifications' => $this->specifications,
            'status' => $this->product->status,
            'branch_office_id' => $this->product->branch_office_id,
            'brand_id' => $this->brand_id,
            'category_id' => $this->category_id,
            'supplier_id' => $this->supplier_id,
            'base_unit_id' => $this->base_unit_id,//$this->product->base_unit_id,
            'base_currency_id' => $this->product->base_currency_id,
            'source' => $this->source,
            'presentation' => $this->presentation,
            'model' => $this->model,
            'rs' => $this->rs,
            'condition' => $this->condition,
            'currency_id' => $this->currency2_id,
        ];
    }

    public function toggle_category ($category_id = null){
        $this->modal_category = !$this->modal_category;
        if($category_id){
            $this->categories = Category::orderBy('name')->get();
            $this->category_id = $category_id;
        }
    }

    public function toggle_brand ($brand_id = null){
        $this->modal_brand = !$this->modal_brand;
        if($brand_id){
            $this->brands = Brand::orderBy('name')->get();
            $this->brand_id = $brand_id;
        }
    }

    public function toggle_supplier ($supplier_id = null){
        $this->modal_supplier = !$this->modal_supplier;
        // dd($supplier_id);
        if($supplier_id){
            $this->suppliers = Person::with_rol('P')->get();
            $this->supplier_id = $supplier_id;
        }
    }

    public function toggle_unit ($base_unit_id = null, $from = null){
        $this->modal_unit = !$this->modal_unit;
        if($from){
            $this->name_modal_unit = $from;
        }
        if($base_unit_id){
            $this->units = Unit::orderBy('name')->get();
            if($this->name_modal_unit == "base_unit"){
                $this->base_unit_id = $base_unit_id;
            }else if($this->name_modal_unit == "presentation_unit") {
                $this->presentation_unit_id = $base_unit_id;
            }
        }
    }

    public function toggle_active_ingredient ($active_ingredient_id = null){
        $this->modal_active_ingredient = !$this->modal_active_ingredient;
        if($active_ingredient_id){
            $this->select_active_ingredient($active_ingredient_id);
        }
    }

    protected function load_actions(){
        $this->aditional_actions = Auth::user()->user_type->actions()
            ->whereIn('actions.code', ['create_category', 'create_brand', 'create_person', 'create_unit', 'create_active_ingredient'])->get();
        $this->arr_actions = [];
        foreach ($this->aditional_actions as $key => $value) {
            $this->arr_actions[] = $value->code;
        }
    }
}
