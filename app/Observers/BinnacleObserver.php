<?php

namespace App\Observers;

use App\Models\Binnacle;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class BinnacleObserver
{
    /**
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return void
     */
    public function created(Model $model)
    {
        $user_id = null;
        if(!Auth::guest()){
            $user_id = Auth::user()->id;
        }
        Binnacle::create([
            'user_id' => $user_id,
            'date_time' => date('Y-m-d H:i:s'),
            'model' => get_class($model),
            'model_id' => $model->id,
            'type' => 'created',
            'detail' => json_encode($model->toArray()),
        ]);
    }

    /**
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return void
     */
    public function updating(Model $model)
    {
        $user_id = null;
        if(!Auth::guest()){
            $user_id = Auth::user()->id;
        }
        Binnacle::create([
            'user_id' => $user_id,
            'date_time' => date('Y-m-d H:i:s'),
            'model' => get_class($model),
            'model_id' => $model->id,
            'type' => 'updating',
            'detail' => json_encode($model->getOriginal()),
        ]);
    }

    /**
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return void
     */
    public function updated(Model $model)
    {
        $user_id = null;
        if(!Auth::guest()){
            $user_id = Auth::user()->id;
        }
        Binnacle::create([
            'user_id' => $user_id,
            'date_time' => date('Y-m-d H:i:s'),
            'model' => get_class($model),
            'model_id' => $model->id,
            'type' => 'updated',
            'detail' => json_encode($model->toArray()),
        ]);
    }

    /**
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return void
     */
    public function deleted(Model $model)
    {
        $user_id = null;
        if(!Auth::guest()){
            $user_id = Auth::user()->id;
        }
        Binnacle::create([
            'user_id' => $user_id,
            'date_time' => date('Y-m-d H:i:s'),
            'model' => get_class($model),
            'model_id' => $model->id,
            'type' => 'deleted',
            'detail' => json_encode($model->toArray()),
        ]);
    }
}
