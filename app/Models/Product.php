<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Product extends Model implements HasMedia
{
    use HasFactory;
    use SoftDeletes;
    use InteractsWithMedia;

    protected $fillable = [
        'code',
        'barcode',
        'type',
        'name',
        'abbreviation',
        'igv',
        'security_stock',
        'affects_stock',
        'specifications',
        'status',
        'branch_office_id',
        'brand_id',
        'category_id',
        'supplier_id',
        'base_unit_id',
        'base_currency_id',
        'presentation',
        'model',
        'rs',
        'condition',
	    'source',
        'currency_id',
    ];

    public function branch_office(){
        return $this->belongsTo(BranchOffice::class);
    }

    public function brand(){
        return $this->belongsTo(Brand::class);
    }

    public function category(){
        return $this->belongsTo(Category::class);
    }

    public function supplier(){
        return $this->belongsTo(Person::class, 'supplier_id');
    }

    public function base_unit(){
        return $this->belongsTo(Unit::class, 'base_unit_id');
    }

    public function base_currency(){
        return $this->belongsTo(Currency::class, 'base_currency_id');
    }

    public function active_ingredients(){
        return $this->belongsToMany(ActiveIngredient::class, 'product_ingredients', 'product_id', 'active_ingredient_id')->orderBy('active_ingredients.name', 'asc');
    }

    public function stock(){
        return $this->hasOne(Stock::class);
    }

    public function currency(){
        return $this->belongsTo(Currency::class, 'currency_id');
    }

    public function all_product_units(){
        return $this->hasMany(ProductUnit::class, 'product_id')
            ->join('units', 'product_units.unit_id', '=', 'units.id')
            ->orderBy('units.name', 'asc')
            ->select('product_units.*', 'units.name');
    }

    public function all_only_units(){
        return $this->hasMany(ProductUnit::class, 'product_id')
            ->join('units', 'product_units.unit_id', '=', 'units.id')
            ->orderBy('units.name', 'asc')
            ->distinct('units.id', 'units.name', 'units.code')
            ->select('units.id', 'units.name', 'units.code','product_units.sell_price','product_units.sell_price_min');
    }

    public function all_due_dates(){
        return $this->hasMany(Lot::class,'product_id')
                ->orderBy('lots.due_date','asc')
                ->where('lots.remain','>',0)
                ->where('lots.status','like','Y')
                ->select('lots.due_date','lots.id');
    }

    public function product_units(){
        return ProductUnit::join('units', 'product_units.unit_id', '=', 'units.id')
            ->where('product_units.id', '<>', $this->base_product_unit()->id)
            ->where('product_units.product_id', $this->id)
            ->orderBy('units.name', 'asc')
            ->select('product_units.*', 'units.name')
            ->get();

        // return $this->hasMany(ProductUnit::class, 'product_id')
        //     ->join('units', 'product_units.unit_id', '=', 'units.id')
        //     ->where('product_units.id', '<>', $this->base_product_unit()->first()->id)
        //     ->orderBy('units.name', 'asc')
        //     ->select('product_units.*', 'units.name');
    }

    public function base_product_unit(){
        return ProductUnit::join('units', 'product_units.unit_id', '=', 'units.id')
            ->where('product_units.unit_id', $this->base_unit_id)
            ->where('product_units.currency_id', $this->base_currency_id)
            ->where('product_units.product_id', $this->id)
            ->orderBy('units.name', 'asc')
            ->select('product_units.*', 'units.name')
            ->first();

        // return $this->hasMany(ProductUnit::class, 'product_id')
        //     ->join('units', 'product_units.unit_id', '=', 'units.id')
        //     ->where('product_units.unit_id', $this->base_unit_id)
        //     ->where('product_units.currency_id', $this->base_currency_id)
        //     ->orderBy('units.name', 'asc')
        //     ->select('product_units.*', 'units.name');
    }

    public function scopeSearch($query, $type, $name, $category_id, $brand_id, $supplier_id, $status, $branch_office_id){

        $query = $query->join('categories', 'categories.id', 'products.category_id')
        ->join('brands', 'brands.id', 'products.brand_id')
        ->join('people', 'people.id', 'products.supplier_id')
        ->where(function ($subquery) use ($name) {
            if (!is_null($name)) {
                $subquery->where('products.name', 'ILIKE', '%' . $name . '%')->orWhere('products.barcode', 'ILIKE', '%' . $name . '%')->orWhere('products.code', 'ILIKE', '%' . $name . '%');
            }
        })->where(function ($subquery) use ($type) {
            if (!is_null($type)) {
                $subquery->where('products.type', '=', $type);
            }
        })->where(function ($subquery) use ($category_id) {
            if (!is_null($category_id)) {
                $subquery->where('products.category_id', '=', $category_id);
            }
        })->where(function ($subquery) use ($brand_id) {
            if (!is_null($brand_id)) {
                $subquery->where('products.brand_id', '=', $brand_id);
            }
        })->where(function ($subquery) use ($supplier_id) {
            if (!is_null($supplier_id)) {
                $subquery->where('products.supplier_id', '=', $supplier_id);
            }
        })->where(function ($subquery) use ($status) {
            if (!is_null($status)) {
                $subquery->where('products.status', '=', $status);
            }
        })->where(function ($subquery) use ($branch_office_id) {
            if (!is_null($branch_office_id)) {
                $subquery->where('products.branch_office_id', '=', $branch_office_id);
            }
        });

        return $query;
    }

    public function scopesearch_fast($query, $barcode, $search, $branch_office_id, $supplier_id){

        $query = $query->join('categories', 'categories.id', 'products.category_id')
        ->join('units', 'units.id', 'products.base_unit_id')
        ->join('brands', 'brands.id', 'products.brand_id')
        ->join('people', 'people.id', 'products.supplier_id')
        ->leftjoin('stocks',function ($join) use($branch_office_id){
            if (!is_null($branch_office_id)) {
                $join->on('stocks.product_id','=', 'products.id')
                    ->on('stocks.branch_office_id', '=', DB::raw($branch_office_id));
            }else{
                $join->on('stocks.product_id','=', 'products.id');
                
            }
        })
        ->where(function ($subquery) use ($search) {
            if (!is_null($search)) {
                $subquery->where('products.name', 'ILIKE', '%' . $search . '%')
                ->orWhere('products.code', 'ILIKE', '%' . $search . '%')
                ->orWhere('categories.name', 'ILIKE', '%' . $search . '%')
                ->orWhere('brands.name', 'ILIKE', '%' . $search . '%')
                ->orwhere('people.name', 'ILIKE', '%' . $search . '%')
                ->orWhere('surname_father', 'ILIKE', '%' . $search . '%')
                ->orWhere('surname_mother', 'ILIKE', '%' . $search . '%')
                ->orWhere('business_name', 'ILIKE', '%' . $search . '%')
                ->orWhere(DB::raw("CONCAT(people.name, ' ', surname_father, ' ', surname_mother)"), 'ILIKE', '%' . $search . '%')
                ->orWhere(DB::raw("CONCAT(surname_father, ' ', surname_mother, ' ', people.name)"), 'ILIKE', '%' . $search . '%');
            }
        })->where(function ($subquery) use ($barcode) {
            if (!is_null($barcode)) {
                $subquery->where('products.barcode', '=', $barcode);
            }
        })->where(function ($subquery) use ($supplier_id) {
            if (!is_null($supplier_id)) {
                $subquery->where('products.supplier_id', '=', $supplier_id);
            }
        });

        return $query;
    }
}
