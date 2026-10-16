<div>
    @if (!$modal)
        <div class="bg-white rounded-md py-8 px-8 shadow-md">
    @endif
        <div class="grid grid-cols-1 sm:grid-cols-3">
            <div class="p-3 col-span-1 sm:col-span-2">
                <label for="product_name">Producto: </label>
                <x-general.input type="text" id="product_name" :disabled="true" placeholder="Producto" wire:model.defer="product_name" />
                <x-jet-input-error for="product_name" />
            </div>
            <div class="p-3 col-span-1">
                <label for="period">Periodo (Meses): </label>
                <x-general.input type="number" min="0" id="period"  placeholder="Periodo" wire:model.defer="period" />
                <x-jet-input-error for="period" />
            </div>
        </div>
        <div class="grid grid-cols-1">
            <div class="p-3 col-span-1">
                <label for="job_name">Actividad: </label>
                <x-general.input type="text" id="job_name" placeholder="Actividad" wire:model.defer="job_name" wire:keydown.enter="add_job"/>
                <x-jet-input-error for="job_name" />
            </div>
            <div class="p-3 col-span-1">
                <table class="min-w-full divide-y divide-gray-200 shadow">
                    <thead class="bg-gray-200">
                        <tr>
                            <th colspan="3" class="py-1 text-center text-xs font-bold text-gray-600 uppercase tracking-wider">Actividades</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @if (count($jobs_list) == 0)
                            <tr>
                                <td colspan="3" class="px-3 py-2 text-center text-xs text-gray-500"> No ha agregado ningún registro</td>
                            </tr>
                        @endif
                        @foreach ($jobs_list as $key => $item)
                            <tr>
                                <td class="px-3 py-2 text-center text-sm text-gray-500">{{ $item["job_name"] }}</td>
                                <td class="px-3 py-2 text-center text-xs text-gray-500">
                                    <x-general.select name="job_type_{{$key}}" id="job_type_{{$key}}" wire:model.defer="jobs_list.{{$key}}.job_type">
                                    <option value="R">REVISAR ESTADO</option>
                                    <option value="E">EJECUTAR INSTRUCCION</option>
                                    <option value="C">CAMBIAR</option>
                                    </x-general.select>
                                </td>
                                <td class="px-3 py-2 text-center text-sm text-gray-500"><button
                                    wire:click="quit_job('{{ $key }}')"
                                    wire:key="quit-job-{{$key}}"><i class="fas fa-times text-red-600"></i></button></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

        </div>
        <div class="mt-4 flex justify-end">
            <x-general.button x-on:click="show=false"  class="bg-gray-200 text-black hover:bg-gray-400 active:bg-gray-500 mx-2" wire:click="back_button">
                <i class="fas fa-arrow-left mr-2" aria-hidden="true"></i> {{ __(' Atrás') }}
            </x-general.button>
            @if ($maintenance->id)
                <x-general.button class="bg-yellow-500 text-white hover:bg-yellow-600 active:bg-yellow-700 mx-2" wire:click="save"
                    wire:loading.attr="disabled" wire:target="update" wire:key="update-brand">
                    <i class="fas fa-edit mr-2" aria-hidden="true"></i> {{ __(' Editar') }}
                </x-general.button>
            @else
                <x-general.button class="bg-blue-500 text-white hover:bg-blue-600 active:bg-blue-700 mx-2" wire:click="save"
                    wire:loading.attr="disabled" wire:target="save" wire:key="save-brand">
                    <i class="fas fa-save mr-2" aria-hidden="true"></i> {{ __(' Guardar') }}
                </x-general.button>
            @endif
        @if (!$modal)
            </div>
        @endif

    </div>
</div>
