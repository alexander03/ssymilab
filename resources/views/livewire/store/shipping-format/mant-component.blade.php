<div>
    @if (!$modal)
        <div class="bg-white rounded-md py-8 px-8 shadow-md">
    @endif
        <div x-data="{ type: @entangle('type').defer }" class="grid grid-cols-6 gap-6">

            <div class="col-span-6 md:col-span-1">
                <x-jet-label for="type" class="mb-3" value="{{ __('Tipo doc.') }}" />
                <x-general.select id="type"
                    class="mt-3 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 block w-full sm:text-sm border-gray-300 rounded-md h-10" readonly=""
                    wire:model.defer="type"
                    x-model="type"
                    x-on:change="console.log(type)">
                    <option value="1">DNI</option>
                    <option value="6">RUC</option>
                    <option value="4">CARNET EXTRANJERIA</option>
                    <option value="0">OTROS</option>
                </x-general.select>
                <x-jet-input-error for="type" class="mt-2" />
            </div>

            <div class="col-span-6 md:col-span-3" x-show="type=='1'">
                <x-jet-label for="dni" value="{{ __('DNI') }}" />
                <div class="flex mt-1 ">
                    <x-general.input id="dni" type="number" class="block placeholder-gray-400 h-10 rounded-tr-none rounded-br-none w-full" placeholder="DNI" wire:model.defer="dni" autocomplete="dni" />
                    <button class="mt-1 bg-blue-500 text-white p-2 hover:bg-blue-600 focus:outline-none w-11 h-10 flex items-center justify-center rounded-md rounded-tl-none rounded-bl-none disabled:opacity-25 transition" wire:key="btn-dni" wire:target="search_document" wire:click="search_document" wire:loading.attr="disabled">
                        <i class="fas fa-search" aria-hidden="true"></i>
                    </button>
                </div>
                <x-jet-input-error for="dni" class="mt-2" />
            </div>
            <div class="col-span-6 md:col-span-3" x-show="type!='1'">
                <div x-show="type=='6'">
                    <x-jet-label for="ruc" value="RUC" />
                </div>
                <div x-show="type=='4'">
                    <x-jet-label for="ruc" value="Carnet de extranjería" />
                </div>
                <div x-show="type=='0'">
                    <x-jet-label for="ruc" value="Nro Documento" />
                </div>
                <div class="flex">
                    <x-general.input id="ruc" type="number" x-bind:class="(type == '6') ? 'block placeholder-gray-400 h-10 w-full rounded-tr-none rounded-br-none' : 'block placeholder-gray-400 h-10 w-full'" x-bind:placeholder="(type=='6') ? 'RUC' : 'Documento'" wire:model.defer="ruc" autocomplete="ruc" readonly=""  />
                    <button x-show="type=='6'" class="mt-1 bg-blue-500 text-white p-2 hover:bg-blue-600 focus:outline-none w-11 h-10 flex items-center justify-center rounded-md rounded-tl-none rounded-bl-none disabled:opacity-25 transition" wire:key="btn-ruc" wire:target="search_document" wire:click="search_document" wire:loading.attr="disabled">
                        <i class="fas fa-search" aria-hidden="true"></i>
                    </button>
                </div>
                <x-jet-input-error for="ruc" class="mt-2" />
            </div>
            <div class="hidden md:visible md:col-span-2">

            </div>

            <div class="col-span-6 md:col-span-3" x-show="type=='1'">
                <x-jet-label for="name" value="{{ __('Nombres') }}" />
                <x-general.input id="name" type="text" class="mt-1 block w-full placeholder-gray-400" placeholder="Nombres" wire:model.defer="name" autocomplete="name" />
                <x-jet-input-error for="name" class="mt-2" />
            </div>

            <div class="col-span-6 md:col-span-3" x-show="type=='1'">
                <x-jet-label for="surname_father" value="{{ __('Apellido Paterno') }}" />
                <x-general.input id="surname_father" type="text" class="mt-1 block w-full placeholder-gray-400" placeholder="Apellido Paterno" wire:model.defer="surname_father" autocomplete="surname_father" />
                <x-jet-input-error for="surname_father" class="mt-2" />
            </div>

            <div class="col-span-6 md:col-span-3" x-show="type=='1'">
                <x-jet-label for="surname_mother" value="{{ __('Apellido Materno') }}" />
                <x-general.input id="surname_mother" type="text" class="mt-1 block w-full placeholder-gray-400" placeholder="Apellido Materno" wire:model.defer="surname_mother" autocomplete="surname_mother" />
                <x-jet-input-error for="surname_mother" class="mt-2" />
            </div>

            <div class="col-span-6 md:col-span-6" x-show="type!='1'">
                <x-jet-label for="business_name" value="{{ __('Razón Social / Nombres Completos') }}" />
                <x-general.input id="business_name" type="text" class="mt-1 block w-full placeholder-gray-400" placeholder="Razón Social / Nombres Completos" wire:model.defer="business_name" autocomplete="business_name" readonly=""/>
                <x-jet-input-error for="business_name" class="mt-2" />
            </div>

            <div class="col-span-6 md:col-span-3">
                <x-jet-label for="email" value="{{ __('Email') }}" />
                <x-general.input id="email" type="email" class="mt-1 block w-full placeholder-gray-400" placeholder="Email" wire:model.defer="email" />
                <x-jet-input-error for="email" class="mt-2" />
            </div>

            <div class="col-span-6 md:col-span-3">
                <x-jet-label for="phone" value="{{ __('Celular') }}" />
                <x-general.input id="phone" type="text" class="mt-1 block w-full placeholder-gray-400" placeholder="Celular" wire:model.defer="phone" autocomplete="phone" />
                <x-jet-input-error for="phone" class="mt-2" />
            </div>

            <div class="col-span-6 md:col-span-3">
                <x-jet-label for="code" value="{{ __('Codigo') }}" />
                <x-general.input id="code" type="text" class="mt-1 block w-full placeholder-gray-400" placeholder="Codigo" wire:model.defer="code" autocomplete="code" />
                <x-jet-input-error for="code" class="mt-2" />
            </div>

            <div class="col-span-6 md:col-span-6">
                <x-jet-label for="address" value="{{ __('Dirección') }}" />
                <x-general.input id="address" type="text" class="mt-1 block w-full placeholder-gray-400" placeholder="Dirección" wire:model.defer="address" autocomplete="address" />
                <x-jet-input-error for="address" class="mt-2" />
            </div>

            <div class="col-span-6 md:col-span-2">
                <x-jet-label for="shipping_person_dni" value="{{ __('DNI Persona Envio') }}" />
                <x-general.input id="shipping_person_dni" type="text" class="mt-1 block w-full placeholder-gray-400" placeholder="DNI Persona de Envio" wire:model.defer="shipping_person_dni"  />
                <x-jet-input-error for="shipping_person_dni" class="mt-2" />
            </div>

            <div class="col-span-6 md:col-span-4">
                <x-jet-label for="shipping_person" value="{{ __('Persona Envio') }}" />
                <x-general.input id="shipping_person" type="text" class="mt-1 block w-full placeholder-gray-400" placeholder="Persona de Envio" wire:model.defer="shipping_person"  />
                <x-jet-input-error for="shipping_person" class="mt-2" />
            </div>

            <div class="col-span-6 md:col-span-6" hidden="">
                <x-jet-label for="" value="{{ __('Roles:') }}" />
                @foreach ($arr_roles as $role)
                <div class="mt-1 ml-2">
                    <label class="flex items-center mt-3 mb-2">
                        <input type="checkbox" class="form-checkbox rounded h-4 w-4 text-blue-600"
                        id="role_{{$role->id}}"
                        wire:key="role_{{$role->id}}"
                        wire:model.defer="roles"
                        value="{{ $role->id }}">
                        <span class="ml-2 text-gray-700 text-sm font-semibold">{{ $role->name }}</span>
                    </label>
                </div>
                @endforeach
                <x-jet-input-error for="roles" class="mt-2" />
                <x-jet-input-error for="roles.*" class="mt-2" />
            </div>
        </div>
        <div class="mt-4 flex justify-end">
            <x-general.button x-on:click="show=false" class="bg-gray-200 text-black hover:bg-gray-400 active:bg-gray-500 mx-2" wire:click="back_button">
                <i class="fas fa-arrow-left mr-2" aria-hidden="true"></i> {{ __(' Atrás') }}
            </x-general.button>
            @if ($person->id)
                <x-general.button class="bg-yellow-500 text-white hover:bg-yellow-600 active:bg-yellow-700 mx-2" wire:click="update"
                    wire:loading.attr="disabled" wire:target="update" wire:key="update-person">
                    <i class="fas fa-edit mr-2" aria-hidden="true"></i> {{ __(' Editar') }}
                </x-general.button>
            @else
                <x-general.button class="bg-blue-500 text-white hover:bg-blue-600 active:bg-blue-700 mx-2" wire:click="save"
                    wire:loading.attr="disabled" wire:target="save" wire:key="save-person">
                    <i class="fas fa-save mr-2" aria-hidden="true"></i> {{ __(' Guardar') }}
                </x-general.button>
            @endif

        </div>
    @if (!$modal)
        </div>
    @endif

</div>
