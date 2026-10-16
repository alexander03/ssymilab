<div>
    <div class="vertical-menu shadow bg-gray-900" style="z-index: 2">

        <!-- LOGO -->
        <div class="navbar-brand-box p-3 bg-transparent">
            <a href="{{ route('dashboard') }}" class="logo logo-dark  ml-2">
                <span class="logo-lg font-bold">
                    <img src="{{ asset('assets/images/logo.jpeg') }}" width="180">
                </span>
                <span class="logo-sm font-bold">
                    <img src="{{ asset('assets/images/logo.jpeg') }}" width="50">
                </span>
            </a>

            <a href="{{ route('dashboard') }}" class="logo logo-light  ml-2">
                <span class="logo-lg font-bold">
                    <img src="{{ asset('assets/images/logo.jpeg') }}" width="180">
                </span>
                <span class="logo-sm font-bold">
                    <img src="{{ asset('assets/images/logo.jpeg') }}" width="50">
                </span>
            </a>
        </div>

        <button type="button"
            class="btn btn-sm px-3 font-size-16 header-item waves-effect vertical-menu-btn text-white">
            <i class="fa fa-fw fa-bars"></i>
        </button>

        <div data-simplebar class="sidebar-menu-scroll" style="padding-top:60px">

            <!--- Sidemenu -->
            <div id="sidebar-menu">
                <!-- Left Menu Start -->
                <ul class="metismenu list-unstyled" id="side-menu">
                    <li class="menu-title text-white">Menu</li>
                    @php
                        $last_category = 0;
                        $count_per_category = 0;
                    @endphp
                    @foreach ($menu_options_sidebar as $key => $item)
                        @php
                            $count_per_category++;
                        @endphp
                        @if ($count_per_category == 1 && ($key == count($menu_options_sidebar) - 1 || ($key < count($menu_options_sidebar) - 1 && $item->menu_category_id != $menu_options_sidebar[$key + 1]->menu_category_id)))
                            @php
                                $count_per_category = 0;
                            @endphp
                            <li>
                                <a href="{{ route($item->link) }}">
                                    <i class="{{ $item->menu_category_icon }}"></i>
                                    <span>{{ $item->name }}</span>
                                </a>
                            </li>
                        @else
                            @if ($last_category != $item->menu_category_id)
                                <li class="text-white">
                                    <a href="javascript: void(0);" class="has-arrow waves-effect ">
                                        <i class="{{ $item->menu_category_icon }}"></i>
                                        <span class="">{{ $item->menu_category_name }}</span>
                                    </a>
                                    <ul class="sub-menu" aria-expanded="false">
                            @endif
                            <li><a href="{{ route($item->link) }}" class="li-active">{{ $item->name }}</a></li>
                            @php
                                $last_category = $item->menu_category_id;
                            @endphp
                            @if ($key < count($menu_options_sidebar) - 1)
                                @if ($last_category != $menu_options_sidebar[$key + 1]->menu_category_id)
                                    </ul>
                                </li>
                                    @php
                                        $count_per_category = 0;
                                    @endphp
                                @endif

                            @endif
                            @if ($key == count($menu_options_sidebar) - 1)
                                    </ul>
                                </li>
                            @endif
                        @endif
                    @endforeach

                </ul>
            </div>
            <!-- Sidebar -->

        </div>
    </div>
</div>
