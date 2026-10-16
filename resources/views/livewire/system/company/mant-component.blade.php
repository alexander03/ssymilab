<div>
    <div class="bg-white rounded-md py-8 px-8 shadow-md">
        <div class="grid grid-cols-1 sm:grid-cols-2">
            <div class="p-2 col-span-1">
                <label for="ruc">RUC: </label>
                <x-general.input type="number" id="ruc" placeholder="RUC" wire:model.defer="ruc" />
                <x-jet-input-error for="ruc" />
            </div>

            <div class="p-2 col-span-1">
                <label for="password">Contraseña: </label>
                <x-general.input type="text" id="password" placeholder="Contraseña" wire:model.defer="password" />
                <x-jet-input-error for="password" />
            </div>

            <div class="p-2 col-span-1 sm:col-span-2">
                <label for="business_name">Razón social: </label>
                <x-general.input type="text" id="business_name" placeholder="Razón social" wire:model.defer="business_name" />
                <x-jet-input-error for="business_name" />
            </div>

            <div class="p-2 col-span-1 sm:col-span-2">
                <label for="trade_name">Nombre comercial: </label>
                <x-general.input type="text" id="trade_name" placeholder="Nombre comercial" wire:model.defer="trade_name" />
                <x-jet-input-error for="trade_name" />
            </div>

            <div class="p-2 col-span-1 sm:col-span-2">
                <label for="address">Dirección: </label>
                <x-general.input type="text" id="address" placeholder="Dirección" wire:model.defer="address" />
                <x-jet-input-error for="address" />
            </div>

            <div class="p-2 col-span-1">
                <label for="username_sunat">Usuario SUNAT: </label>
                <x-general.input type="text" id="username_sunat" placeholder="Usuario SUNAT" wire:model.defer="username_sunat" />
                <x-jet-input-error for="username_sunat" />
            </div>

            <div class="p-2 col-span-1">
                <label for="password_sunat">Contraseña SUNAT: </label>
                <x-general.input type="text" id="password_sunat" placeholder="Contraseña SUNAT" wire:model.defer="password_sunat" />
                <x-jet-input-error for="password_sunat" />
            </div>

            <div class="p-2 col-span-1">
                <label for="authentication_mode">Modo autenticación: </label>
                <x-general.input type="text" id="authentication_mode" placeholder="Modo autenticación" wire:model.defer="authentication_mode" />
                <x-jet-input-error for="authentication_mode" />
            </div>

            <div class="p-2 col-span-1">
                <label for="department_id">Departamento: </label>
                <x-general.select wire:model="department_id" id="department_id">
                    @foreach ($departments as $department)
                        <option value="{{ $department->code }}">{{ mb_strtoupper($department->name) }}</option>
                    @endforeach
                </x-general.select>
                <x-jet-input-error for="department_id" />
            </div>

            <div class="p-2 col-span-1">
                <label for="province_id">Provincia: </label>
                <x-general.select wire:model="province_id" id="province_id">
                    @foreach ($provinces as $province)
                        <option value="{{ $province->code }}">{{ mb_strtoupper($province->name) }}</option>
                    @endforeach
                </x-general.select>
                <x-jet-input-error for="province_id" />
            </div>

            <div class="p-2 col-span-1">
                <label for="district_id">Distrito: </label>
                <x-general.select wire:model.defer="district_id" id="district_id">
                    @foreach ($districts as $district)
                        <option value="{{ $district->code }}">{{ mb_strtoupper($district->name) }}</option>
                    @endforeach
                </x-general.select>
                <x-jet-input-error for="district_id" />
            </div>

        </div>
        <div class="mt-4 flex justify-end">
            <x-general.button x-on:click="show=false" class="bg-gray-200 text-black hover:bg-gray-400 active:bg-gray-500 mx-2" wire:click="back_button">
                <i class="fas fa-arrow-left mr-2" aria-hidden="true"></i> {{ __(' Atrás') }}
            </x-general.button>
            @if ($company->id)
                <x-general.button class="bg-yellow-500 text-white hover:bg-yellow-600 active:bg-yellow-700 mx-2" wire:click="update"
                    wire:loading.attr="disabled" wire:target="update" wire:key="update-menu-option">
                    <i class="fas fa-edit mr-2" aria-hidden="true"></i> {{ __(' Editar') }}
                </x-general.button>
            @endif
        </div>
    </div>
</div>
