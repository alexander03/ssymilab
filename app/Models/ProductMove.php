<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductMove extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'document_id',
        'document_detail_id',
        'guide_detail_id',
        'branch_office_id',
        'product_id',
        'lot_id',
        'product_serie_id',
        'dispatched_quantity',
        'quantity',
        'type',
        'status',
    ];

    public function document(){
        return $this->belongsTo(Document::class);
    }

    public function document_detail(){
        return $this->belongsTo(DocumentDetail::class);
    }

    public function guide_detail(){
        return $this->belongsTo(DocumentDetail::class, 'guide_detail_id');
    }

    public function product(){
        return $this->belongsTo(Product::class);
    }

    public function branch_office(){
        return $this->belongsTo(BranchOffice::class);
    }

    public function lot(){
        return $this->belongsTo(Lot::class);
    }
    public function product_serie(){
        return $this->belongsTo(ProductSerie::class);
    }
}
