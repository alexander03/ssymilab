<div>
    @if (!$modal)
        <div class="bg-white rounded-md py-8 px-8 shadow-md">
    @endif
        <div class="p-3">
            <label for="branch_office_id" class="font-semibold text-gray-600">Sucursal: </label>
            <x-general.select id="branch_office_id" wire:model.defer="branch_office_id">
                @foreach ($branch_offices as $item)
                    <option value="{{ $item->id }}" @if($item->id == $branch_office_id) selected @endif > {{ $item->name }} </option>
                @endforeach
            </x-general.select>
            <x-jet-input-error for="branch_office_id" />
        </div>
        <div class="p-3">
            <label for="user_type_id" class="font-semibold text-gray-600">Tipo de Usuario: </label>
            <x-general.select id="user_type_id" class="select2" wire:model.defer="user_type_id">
                @foreach ($user_types as $item)
                    <option value="{{ $item->id }}" @if($item->id == $user_type_id) selected @endif > {{ $item->name }} </option>
                @endforeach
            </x-general.select>
            <x-jet-input-error for="user_type_id" />
        </div>
        <div class="p-3">
            <div class="flex justify-between">
                <label for="person_id" class="font-semibold text-gray-600">Persona: </label>
                @if (in_array('create_person', $arr_actions))
                    <button type="button" class="text-blue-500 hover:underline" wire:click="toggle_person">Nuevo</button>
                @endif
            </div>
            <x-general.select id="person_id" class="select2" wire:model.defer="person_id">
                @foreach ($people as $item)
                    <option value="{{ $item->id }}" @if($item->id == $person_id) selected @endif > {{ $item->getNameComplete() }} </option>
                @endforeach
            </x-general.select>
            <x-jet-input-error for="person_id" />
        </div>


        <div class="p-3">
            <label for="username">Nombre de Usuario: </label>
            <x-general.input :disabled="$user->id" type="text" id="username" placeholder="Nombre de Usuario" wire:model.defer="username" />
            <x-jet-input-error for="username" />
        </div>

        <div class="p-3">
            <label for="password">Contraseña: </label>
            <x-general.input type="password" id="password" placeholder="Contraseña" wire:model.defer="password" />
            <x-jet-input-error for="password" />
        </div>

        <div class="p-3">
            <label for="password_confirmation">Confirme Contraseña: </label>
            <x-general.input type="password" id="password_confirmation" placeholder="Confirme contraseña" wire:model.defer="password_confirmation" />
            <x-jet-input-error for="password_confirmation" />
        </div>


        <div class="mt-4 flex justify-end">
            <x-general.button x-on:click="show=false" class="bg-gray-200 text-black hover:bg-gray-400 active:bg-gray-500 mx-2" wire:click="back_button">
                <i class="fas fa-arrow-left mr-2" aria-hidden="true"></i> {{ __(' Atrás') }}
            </x-general.button>
            @if ($user->id)
                <x-general.button class="bg-yellow-500 text-white hover:bg-yellow-600 active:bg-yellow-700 mx-2" wire:click="update"
                    wire:loading.attr="disabled" wire:target="update" wire:key="update-user">
                    <i class="fas fa-edit mr-2" aria-hidden="true"></i> {{ __(' Editar') }}
                </x-general.button>
            @else
                <x-general.button class="bg-blue-500 text-white hover:bg-blue-600 active:bg-blue-700 mx-2" wire:click="save"
                    wire:loading.attr="disabled" wire:target="save" wire:key="save-user">
                    <i class="fas fa-save mr-2" aria-hidden="true"></i> {{ __(' Guardar') }}
                </x-general.button>
            @endif

        </div>
    @if (!$modal)
        </div>
    @endif
    @if (in_array('create_person', $arr_actions))
        <x-jet-dialog-modal wire:model="modal_person">
            <x-slot name="title">
                CREAR USUARIO
            </x-slot>

            <x-slot name="content">
                @livewire('maintenance.person.mant-component', ["person" => $person, "modal" => true, "event_modal" => "toggle-modal-person", "role_id" => $role_id])
            </x-slot>
        </x-jet-dialog-modal>
    @endif
    @push("js")
    <script>
        $(".select2").select2();

        $(document).ready(function () {
            $('#branch_office_id').on('change', function (e) {
                var data = $('#branch_office_id').select2("val");
                @this.set('branch_office_id', data);
            });
            $('#person_id').on('change', function (e) {
                var data = $('#person_id').select2("val");
                @this.set('person_id', data);
            });
            $('#user_type_id').on('change', function (e) {
                var data = $('#user_type_id').select2("val");
                @this.set('user_type_id', data);
            });
        });
    </script>
    @endpush
</div>
