<div>
    @if (!$modal)
        <div class="bg-white rounded-md py-8 px-8 shadow-md">
    @endif
        <div class="p-2">
            <label for="name">Nombre: </label>
            <x-general.input type="text" id="name" placeholder="Nombre" wire:model.defer="name" />
            <x-jet-input-error for="name" />
        </div>
        <div class="p-2">
            <label for="address">Dirección: </label>
            <x-general.input type="text" id="address" placeholder="Dirección" wire:model.defer="address" />
            <x-jet-input-error for="address" />
        </div>
        <div class="p-2">
            <label for="phone">Teléfono: </label>
            <x-general.input type="text" id="phone" placeholder="Teléfono" wire:model.defer="phone" />
            <x-jet-input-error for="phone" />
        </div>

        <div class="p-2">
            <label for="department_id">Departamento: </label>
            <x-general.select wire:model="department_id" id="department_id">
                @foreach ($departments as $department)
                    <option value="{{ $department->code }}">{{ mb_strtoupper($department->name) }}</option>
                @endforeach
            </x-general.select>
            <x-jet-input-error for="department_id" />
        </div>

        <div class="p-2">
            <label for="province_id">Provincia: </label>
            <x-general.select wire:model="province_id" id="province_id">
                @foreach ($provinces as $province)
                    <option value="{{ $province->code }}">{{ mb_strtoupper($province->name) }}</option>
                @endforeach
            </x-general.select>
            <x-jet-input-error for="province_id" />
        </div>

        <div class="p-2">
            <label for="district_id">Distrito: </label>
            <x-general.select wire:model.defer="district_id" id="district_id">
                @foreach ($districts as $district)
                    <option value="{{ $district->code }}">{{ mb_strtoupper($district->name) }}</option>
                @endforeach
            </x-general.select>
            <x-jet-input-error for="district_id" />
        </div>
        <div class="mt-4 flex justify-end">
            <x-general.button x-on:click="show=false"  class="bg-gray-200 text-black hover:bg-gray-400 active:bg-gray-500 mx-2" wire:click="back_button">
                <i class="fas fa-arrow-left mr-2" aria-hidden="true"></i> {{ __(' Atrás') }}
            </x-general.button>
            @if ($branch_office->id)
                <x-general.button class="bg-yellow-500 text-white hover:bg-yellow-600 active:bg-yellow-700 mx-2" wire:click="update"
                    wire:loading.attr="disabled" wire:target="update" wire:key="update-branch-office">
                    <i class="fas fa-edit mr-2" aria-hidden="true"></i> {{ __(' Editar') }}
                </x-general.button>
            @else
                <x-general.button class="bg-blue-500 text-white hover:bg-blue-600 active:bg-blue-700 mx-2" wire:click="save"
                    wire:loading.attr="disabled" wire:target="save" wire:key="save-branch-office">
                    <i class="fas fa-save mr-2" aria-hidden="true"></i> {{ __(' Guardar') }}
                </x-general.button>
            @endif
        </div>
    @if (!$modal)
        </div>
    @endif
</div>
