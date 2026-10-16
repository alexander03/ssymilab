<x-general.form-section submit="updateProfileInformation">
    <x-slot name="title">
        {{ __('Actualizar Datos') }}
    </x-slot>
    <x-slot name="form">

        <div x-data="{ type: @entangle('inputs.type').defer }" class="grid grid-cols-6 gap-6">
            <div class="col-span-6 sm:col-span-6">
                <span>
                    <strong for=""> Tipo de Usuario: </strong>
                    {{ $user_type_name }}
                </span>
            </div>

            <div class="col-span-6 md:col-span-1">
                <x-jet-label for="type" value="{{ __('Tipo doc.') }}" />
                <select id="type"
                    class="mt-2 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 block w-full sm:text-sm border-gray-300 rounded-md h-10"
                    wire:model.defer="inputs.type"
                    x-model="type"
                    x-on:change="console.log(type)">
                    <option value="1">DNI</option>
                    <option value="6">RUC</option>
                </select>
                <x-jet-input-error for="type" class="mt-2" />
            </div>

            <div class="col-span-6 md:col-span-2" x-show="type=='1'">
                <x-jet-label for="dni" value="{{ __('DNI') }}" />
                <x-jet-input id="dni" type="number" class="mt-1 block w-full" wire:model.defer="inputs.dni" autocomplete="dni" />
                <x-jet-input-error for="dni" class="mt-2" />
            </div>
            <div class="col-span-6 md:col-span-2" x-show="type=='6'">
                <x-jet-label for="ruc" value="{{ __('RUC') }}" />
                <x-jet-input id="ruc" type="number" class="mt-1 block w-full" wire:model.defer="inputs.ruc" autocomplete="ruc" />
                <x-jet-input-error for="ruc" class="mt-2" />
            </div>
            <div class="md:col-span-3">
            </div>


            <div class="col-span-6 md:col-span-3" x-show="type=='1'">
                <x-jet-label for="name" value="{{ __('Nombres') }}" />
                <x-jet-input id="name" type="text" class="mt-1 block w-full" wire:model.defer="inputs.name" autocomplete="name" />
                <x-jet-input-error for="name" class="mt-2" />
            </div>

            <div class="col-span-6 md:col-span-3" x-show="type=='1'">
                <x-jet-label for="surname_father" value="{{ __('Apellido Paterno') }}" />
                <x-jet-input id="surname_father" type="text" class="mt-1 block w-full" wire:model.defer="inputs.surname_father" autocomplete="surname_father" />
                <x-jet-input-error for="surname_father" class="mt-2" />
            </div>

            <div class="col-span-6 md:col-span-3" x-show="type=='1'">
                <x-jet-label for="surname_mother" value="{{ __('Apellido Materno') }}" />
                <x-jet-input id="surname_mother" type="text" class="mt-1 block w-full" wire:model.defer="inputs.surname_mother" autocomplete="surname_mother" />
                <x-jet-input-error for="surname_mother" class="mt-2" />
            </div>

            <div class="col-span-6 md:col-span-3" x-show="type=='6'">
                <x-jet-label for="business_name" value="{{ __('Razón Social / Nombres Completos') }}" />
                <x-jet-input id="business_name" type="text" class="mt-1 block w-full" wire:model.defer="inputs.business_name" autocomplete="business_name" />
                <x-jet-input-error for="business_name" class="mt-2" />
            </div>

            <div class="col-span-6 md:col-span-3">
                <x-jet-label for="email" value="{{ __('Email') }}" />
                <x-jet-input id="email" type="email" class="mt-1 block w-full" wire:model.defer="inputs.email" />
                <x-jet-input-error for="email" class="mt-2" />
            </div>

            <div class="col-span-6 md:col-span-3">
                <x-jet-label for="phone" value="{{ __('Celular') }}" />
                <x-jet-input id="phone" type="text" class="mt-1 block w-full" wire:model.defer="inputs.phone" autocomplete="phone" />
                <x-jet-input-error for="phone" class="mt-2" />
            </div>

            <div class="col-span-6 md:col-span-3">
                <x-jet-label for="address" value="{{ __('Dirección') }}" />
                <x-jet-input id="address" type="text" class="mt-1 block w-full" wire:model.defer="inputs.address" autocomplete="address" />
                <x-jet-input-error for="address" class="mt-2" />
            </div>
        </div>

    </x-slot>

    <x-slot name="actions">
        <x-jet-action-message class="mr-3" on="saved">
            {{ __('Guardado.') }}
        </x-jet-action-message>

        <x-jet-button wire:loading.attr="disabled" wire:target="photo">
            {{ __('Guardar') }}
        </x-jet-button>
    </x-slot>
</x-general.form-section>
