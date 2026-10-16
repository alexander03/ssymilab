<?php

namespace App\Http\Traits\General;

trait SessionFilter
{
    public $session_route = '';

    private function init_session_filter($filters){
        // 'category' , ['name']
        $data = session($this->session_route);
        if($data){
            foreach ($data as $key => $value) {
                $this->$key = $value;
            }
        }else{
            $data = [];
            foreach ($filters as $value) {
                $data[$value] = $this->$value;
            }
            session([$this->session_route => $data]);
        }
    }

    private function save_filters(){
        $data = session($this->session_route);
        if($data){
            foreach ($data as $key => $value) {
                $data[$key] = $this->$key;
            }
            session([$this->session_route => $data]);
        }
    }
}
