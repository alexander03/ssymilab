<div>
    <x-jet-confirmation-modal wire:model="open">
        <x-slot name="title">
            LISTA DE ARCHIVOS
        </x-slot>

        <x-slot name="content">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-200">
                    <tr>
                        <th style="text-align: center;">#</th>
                        <th style="text-align: center;">Archivo</th>
                        <th style="text-align: center;">Eliminar</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                <?php 
                if(!is_null($schedule)){
                    $c=0;
                    foreach ($schedule->getMedia('installation') as $key => $value) {$c++;
                        echo "<tr>";
                        echo "<td>$c</td>";
                        echo "<td><a target='blank' href='".route('installation-file', ["id" => $schedule->id, "name" => $value->name])."'>".$value->name."</a></td>";
                        echo "<td style='text-align:center'><a title='Eliminar' href='javascript:void(0)' wire:click=\"delete_file('".$value->name."')\" class='text-red-600 hover:text-red-800'><i class='fas fa-trash'></i></a></td>";
                        echo "</tr>";
                    }
                }
                ?>
                </tbody>
            </table>
        </x-slot>

        <x-slot name="footer">
            <x-jet-secondary-button wire:click="toggle_modal_files" wire:loading.attr="disabled">
                <i class="fas fa-arrow-left mr-2" aria-hidden="true"></i> CANCELAR
            </x-jet-secondary-button>
        </x-slot>
    </x-jet-confirmation-modal>
</div>
