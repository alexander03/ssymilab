<?php

namespace App\View\Components\Layouts;

use App\Models\MenuOption;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\Component;

class SidebarApp extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {

        $menu_options_sidebar = Auth::user()->get_menu_options()->get();

        return view('components.layouts.sidebar-app')->with(compact('menu_options_sidebar'));
    }
}
