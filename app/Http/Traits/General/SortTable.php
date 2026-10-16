<?php

namespace App\Http\Traits\General;


trait SortTable
{
    public $sort = "name";
    public $direction = "asc";
    public $search_event = "search";

    public function order($sort)
    {
        if ($this->sort == $sort) {
            $this->direction = ($this->direction == "asc") ? "desc" : "asc";
        } else {
            $this->direction = "asc";
        }
        $this->sort = $sort;

        $this->emitSelf($this->search_event);
    }

    public function search_and_reset_page()
    {
        $this->resetPage();
        $this->emitSelf($this->search_event);
    }
}
