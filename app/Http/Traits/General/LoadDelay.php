<?php

namespace App\Http\Traits\General;

trait LoadDelay {

    /**
     * Variable que controla la carga inicial del componente
     * @var boolean $is_loaded
     */
    public $is_loaded = false;


    /**
     * Función que se ejecuta despues de iniciar el componente
     * @return void
     */
    public function loaded() : void
    {
        $this->is_loaded = true;
    }
}
