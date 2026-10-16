<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DocumentDetail extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'document_id',
        'document_detail_id',
        'product_id',
        'unit_id',
        'currency_id',
        'bags',
        'unit_value',
        'igv',
        'sell_price',
        'quantity',
        'icbper',
        'subtotal',
        'purchase_price',
        'dispatched_quantity',
        'status',
        'detail',
    ];

    public function document(){
        return $this->belongsTo(Document::class);
    }

    public function document_detail(){
        return $this->belongsTo(DocumentDetail::class);
    }

    public function product(){
        return $this->belongsTo(Product::class)->withTrashed();
    }

    public function unit(){
        return $this->belongsTo(Unit::class);
    }

    public function currency(){
        return $this->belongsTo(Currency::class);
    }

    public static function product_moves_guide($id,$document_id=null){
        if(is_null($document_id)){
            return ProductMove::where('guide_detail_id', $id)->where('status', '<>', 'A')->get();
        }else{
            return ProductMove::where('guide_detail_id', $id)->where('document_id',$document_id)->where('status', '<>', 'A')->get();
        }
    }

    public static function product_moves($id){
        return ProductMove::where('document_detail_id', $id)->where('status', '<>', 'A')->get();
    }

    public static function product_moves_pending($id){
        return ProductMove::where('document_detail_id', $id)->where('status', 'P')->get();
    }
}
