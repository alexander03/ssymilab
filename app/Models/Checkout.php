<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Checkout extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'serie',
        'serie_note',
        'status',
        'branch_office_id',
        'user_id',
    ];

    public function branch_office(){
        return $this->belongsTo(BranchOffice::class);
    }

    public function user(){
        return $this->belongsTo(User::class);
    }

    public static function last_datail($checkout_id){
        return CheckoutDetail::where('checkout_id', $checkout_id)->orderBy('created_at', 'DESC')->first();
    }

    public static function closed(){
        return Checkout::where('status', 'C')->orderBy('name', 'ASC')->get();
    }

    public function scopeSearch($query, $name, $branch_office_id){

        $query = $query->where(function ($subquery) use ($name) {
            if (!is_null($name)) {
                $subquery->where('name', 'ILIKE', '%' . $name . '%');
            }
        })->where(function ($subquery) use ($branch_office_id) {
            if (!is_null($branch_office_id)) {
                $subquery->where('branch_office_id', '=', $branch_office_id);
            }
        });

        return $query;
    }
}
