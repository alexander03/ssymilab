<div>
    <header id="page-topbar" style="z-index: 1">
        <div class="navbar-header shadow">
            <div class="d-flex">
                <!-- LOGO -->
                <div class="navbar-brand-box bg-transparent pt-3">
                    <a href="{{ route('dashboard') }}" class="logo logo-dark">
                        <div class="font-bold">
                            <img src="{{ asset('assets/images/logomin.png') }}" width="40">
                        </div>
                        <span class="logo-lg font-bold">
                            <img src="{{ asset('assets/images/logo-gs.png') }}" width="110">
                        </span>
                    </a>
                    <a href="{{ route('dashboard') }}" class="logo logo-light">
                        <span class="logo-sm font-bold">
                            <img src="{{ asset('assets/images/logomin.png') }}" width="40">
                        </span>
                        <span class="logo-lg font-bold">
                            <img src="{{ asset('assets/images/logo-gs.png') }}" width="110">
                        </span>
                    </a>
                </div>

                <button type="button" class="btn btn-sm px-3 font-size-16 header-item waves-effect vertical-menu-btn">
                    <i class="fa fa-fw fa-bars"></i>
                </button>

            </div>

            <div class="d-flex" style="background-color: white !important">
                <div class="dropdown d-inline-block" style="background-color: white !important">
                    @if (in_array('change_exchange_rate', $arr_actions))
                        <button type="button" class="btn header-item waves-effect invisible sm:visible" wire:click="toggle_exchange_modal">
                            <i class="fas fa-dollar-sign d-inline-block text-sm"></i>
                        </button>
                    @endif
                    <button type="button" class="btn header-item waves-effect invisible sm:visible">
                        <i class="fas fa-home d-inline-block text-xs mr-2"></i>
                        <span class="d-none d-xl-inline-block fw-medium text-xs">{{ $branch_office_name }}</span>
                    </button>

                    <button type="button" class="btn header-item waves-effect" id="page-header-user-dropdown"
                        data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        {{-- <img class="rounded-circle header-profile-user" src="assets/images/users/avatar-4.jpg"
                            alt="Header Avatar"> --}}
                        <i class="fas fa-cog d-inline-block text-sm mr-2 "></i>
                        <span class="d-none d-xl-inline-block fw-medium text-sm">{{ $name }}</span>
                    </button>
                    <div class="dropdown-menu dropdown-menu-end mr-10" style="left: auto!important; right: auto!important; top: 100%!important;">
                        <!-- item-->
                        <a class="dropdown-item" href="javascript:void(0)"><i
                            class="uil uil-user-circle font-size-18 align-middle text-muted me-1"></i> <span
                            class="align-middle font-bold"> {{ $branch_office_name }} </span></a>

                        <a class="dropdown-item" href="{{ route('profile') }}"><i
                            class="uil uil-user-circle font-size-18 align-middle text-muted me-1"></i> <span
                            class="align-middle">Ver Perfil</span></a>

                        @if (in_array('change_branch_office', $arr_actions))
                            <a class="dropdown-item" href="javascript:void(0)" wire:click="toggle_branch_modal" ><i
                                class="uil uil-user-circle font-size-18 align-middle text-muted me-1"></i> <span
                                class="align-middle">Cambiar Sucursal</span></a>
                        @endif


                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" href="{{ route('logout') }}" class="dropdown-item" href="#"><i
                                    class="uil uil-sign-out-alt font-size-18 align-middle me-1 text-muted"></i> <span
                                    class="align-middle">Cerrar Sesión</span></button>
                        </form>
                    </div>
                    <button type="button" class="btn header-item waves-effect" id="page-header-user-dropdown"
                        data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <span class="relative inline-block">
                            <i class="fas fa-bell d-inline-block text-sm mr-2 "></i>
                            @if ($notifications_quantity > 0)
                                <span class="absolute top-0 right-1 inline-block w-2 h-2 transform translate-x-1/2 -translate-y-1/2 bg-red-600 rounded-full"></span>
                            @endif
                          </span>
                    </button>
                    <div class="dropdown-menu dropdown-menu-end mr-10" style="left: auto!important; right: auto!important; top: 100%!important;">
                        <!-- item-->
                        @foreach ($notifications as $notification)
                        <a class="dropdown-item whitespace-normal w-60" href="{{ route('order') }}"><i
                            class="uil uil-user-circle font-size-18 align-middle text-muted me-1"></i> <span
                            class="align-middle">{{$notification->message}}</span></a>
                        @endforeach


                    </div>
                </div>
            </div>
        </div>
    </header>
    @if (in_array('change_branch_office', $arr_actions))
        @php
            $branch_office_width = "lg";
            $modal_branch_office_id = "modal-branch-office-id";
        @endphp
        <x-jet-dialog-modal wire:model="open" :id="$modal_branch_office_id" :maxWidth="$branch_office_width">
            <x-slot name="title">
                CAMBIAR DE SUCURSAL
            </x-slot>
            <x-slot name="content">
                <div class="px-3">
                    <label class="font-bold">ACTUAL: </label>
                    <label>{{ $branch_office_name }}</label>
                </div>
                <div class="p-3">
                    <label for="branch_office_id">Sucursales: </label>
                    <x-general.select id="branch_office_id" wire:model.defer="branch_office_id">
                        @foreach ($branch_offices as $item)
                            <option value="{{ $item->id }}"> {{ $item->name }} </option>
                        @endforeach
                    </x-general.select>
                    <x-jet-input-error for="branch_office_id" />
                </div>
            </x-slot>

            <x-slot name="footer">
                <x-general.button x-on:click="show=false" class="bg-gray-200 text-black hover:bg-gray-400 active:bg-gray-500 mx-2">
                    <i class="fas fa-arrow-left mr-2" aria-hidden="true"></i> {{ __(' Atrás') }}
                </x-general.button>

                <x-general.button class="bg-blue-500 text-white hover:bg-blue-600 active:bg-blue-700 mx-2" wire:click="change_branch_office"
                    wire:loading.attr="disabled" wire:target="change_branch_office" wire:key="change-branch-office">
                    <i class="fas fa-save mr-2" aria-hidden="true"></i> {{ __(' Cambiar') }}
                </x-general.button>
            </x-slot>
        </x-jet-dialog-modal>
    @endif
    @if (in_array('change_exchange_rate', $arr_actions))
        @php
            $exchange_rate_width = "sm";
            $modal_exchange_rate_id = "modal-branch-office-id";
        @endphp
        <x-jet-dialog-modal wire:model="open_exchange" :id="$modal_exchange_rate_id" :maxWidth="$exchange_rate_width">
            <x-slot name="title">
                CAMBIAR DE TIPO DE CAMBIO
            </x-slot>
            <x-slot name="content">
                <div class="px-3">
                    <label class="font-bold">MONEDA BASE: </label>
                    <label>{{ $base_currency->name }}</label>
                </div>
                <div class="px-3">
                    <label class="font-bold">FECHA: </label>
                    <label>{{ date_format(new DateTime(date('Y-m-d')),'d/m/Y') }}</label>
                </div>
                @foreach ($currencies as $currency)
                    <div class="p-3">
                        <label for="name">{{ $currency->name }}: </label>
                        <x-general.icon-input>
                            <x-slot name="icon">
                                <span class="font-bold text-base">{{ $base_currency->symbol }}</span>
                            </x-slot>
                            <x-general.input type="number" name="exchanges.{{ $currency->code }}" wire:model.defer="exchanges.{{ $currency->code }}" class="col-span-10 h-10 rounded-tl-none rounded-bl-none text-right" />
                            </x-general.icon-input>
                        <x-jet-input-error for="exchanges.{{ $currency->code }}" />
                    </div>
                @endforeach
            </x-slot>

            <x-slot name="footer">
                <x-general.button x-on:click="show=false" class="bg-gray-200 text-black hover:bg-gray-400 active:bg-gray-500 mx-2">
                    <i class="fas fa-arrow-left mr-2" aria-hidden="true"></i> {{ __(' Atrás') }}
                </x-general.button>

                <x-general.button class="bg-blue-500 text-white hover:bg-blue-600 active:bg-blue-700 mx-2" wire:click="change_exchange_rate"
                    wire:loading.attr="disabled" wire:target="change_exchange_rate" wire:key="change-exchange-rate">
                    <i class="fas fa-save mr-2" aria-hidden="true"></i> {{ __(' Cambiar') }}
                </x-general.button>
            </x-slot>
        </x-jet-dialog-modal>
    @endif
</div>
