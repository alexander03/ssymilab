<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use GuzzleHttp\Client;

class ExchangeRate extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'date',
        'value',
        'currency_id',
        'base_currency_id',
        'user_id',
    ];

    public function base_currency(){
        return $this->belongsTo(Currency::class, 'base_currency_id');
    }

    public function currency(){
        return $this->belongsTo(Currency::class);
    }

    public static function today($currency_id){
        if($currency_id=="2"){
            $exchange = ExchangeRate::where('date', date('Y-m-d'))->where('currency_id', $currency_id)->first();
            if(is_null($exchange)){
           
                /*$client = new Client();//sale error api
                $res = $client->request('GET', 'https://api.apis.net.pe/v1/tipo-cambio-sunat?fecha='.date("Y-m-d"), [
                    'query' =>[
                        'fecha' => date('Y-m-d')
                    ]
                ]);
                $body = json_decode($res->getBody());
                if(!is_null($body->venta)){
                        $exchange = new ExchangeRate();
                        $exchange->date = date("Y-m-d");
                        $exchange->currency_id = $currency_id;
                        $exchange->value = $body->venta;
                        $exchange->base_currency_id = 1;
                        $exchange->user_id = 1;
                        $exchange->save();
                }*/ 
            }
        }
        return ExchangeRate::where('date', date('Y-m-d'))->where('currency_id', $currency_id)->first();
    }

    public static function the_last($currency_id){
        return ExchangeRate::where('currency_id', $currency_id)->orderBy('date', 'DESC')->first();
    }

    public function scopesearch($query,$date_start,$date_end){
        return $query->where(function($sql) use ($date_start){
                if(!is_null($date_start) && $date_start!=""){
                    $sql->where('date','>=',$date_start);
                }
            })  
            ->where(function($sql) use ($date_end){
                if(!is_null($date_end) && $date_end!=""){
                    $sql->where('date','<=',$date_end);
                }
            })  ;
    }
}
