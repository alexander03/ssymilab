<div>
    <x-jet-confirmation-modal wire:model="open">
        <x-slot name="title">
            ELIMINAR REGISTRO
        </x-slot>

        <x-slot name="content">
            ¿Desea eliminar el registro? Si el registro tiene relaciones con otros registros no podrá eliminarlo.
        </x-slot>

        <x-slot name="footer">
            <x-jet-secondary-button wire:click="toggle_modal" wire:loading.attr="disabled">
                <i class="fas fa-arrow-left mr-2" aria-hidden="true"></i> CANCELAR
            </x-jet-secondary-button>

            <x-jet-danger-button class="ml-2" wire:click="delete" wire:loading.attr="disabled">
                <i class="fas fa-check mr-2" aria-hidden="true"></i> ELIMINAR
            </x-jet-danger-button>
        </x-slot>
    </x-jet-confirmation-modal>
</div>
