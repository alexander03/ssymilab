<div>
    @if (!$modal)
        <div class="bg-white rounded-md py-8 px-8 shadow-md">
    @endif
        <div class="grid grid-cols-1 md:grid-cols-2">
            <div class="p-2">
                <label for="date_time">Fecha: </label>
                <x-general.input type="date" disabled id="date_time" placeholder="Fecha" wire:model.defer="date_time" />
                <x-jet-input-error for="date_time" />
            </div>
            <div class="p-2">
                <label for="number">Número: </label>
                <x-general.input type="number" disabled id="number" placeholder="Numero" wire:model.defer="number" />
                <x-jet-input-error for="number" />
            </div>
            @foreach ($init_payments as $key => $item)
                <div class="p-2">
                    <label for="init_payments.{{ $key }}.cash_amount">Monto inicial ({{ $item["currency_name"] }}): </label>
                    <x-general.input type="number" wire:model.defer="init_payments.{{ $key }}.cash_amount" />
                    <x-jet-input-error for="init_payments.{{ $key }}.cash_amount" />
                </div>
                @if ($item["currency_base"] != "Y")
                    <div class="p-2">
                        <label for="init_payments.{{ $key }}.exchange_rate"> Tipo de cambio ({{ $item["currency_name"] }}): </label>
                        <x-general.input type="number" wire:model.defer="init_payments.{{ $key }}.exchange_rate" />
                        <x-jet-input-error for="init_payments.{{ $key }}.exchange_rate" />
                    </div>
                @else
                    <div class="p-2">
                    </div>
                @endif
            @endforeach

        </div>
        <div class="mt-4 flex justify-end">
            <x-general.button x-on:click="show=false"  class="bg-gray-200 text-black hover:bg-gray-400 active:bg-gray-500 mx-2" wire:click="back_button">
                <i class="fas fa-arrow-left mr-2" aria-hidden="true"></i> {{ __(' Atrás') }}
            </x-general.button>
            @if ($checkout->id)
                <x-general.button class="bg-green-500 text-white hover:bg-green-600 active:bg-green-700 mx-2" wire:click="save"
                    wire:loading.attr="disabled" wire:target="save" wire:key="save-checkout">
                    <i class="fas fa-save mr-2" aria-hidden="true"></i> {{ __(' Aperturar') }}
                </x-general.button>
            @endif

        </div>
    @if (!$modal)
        </div>
    @endif
</div>
