<div>
    @if (!$modal)
        <div class="bg-white rounded-md py-8 px-8 shadow-md">
    @endif
        <div class="grid grid-cols-1 md:grid-cols-3">
            <div class="col-span-3 p-2">
                <x-jet-label for="document_type_id" class="font-semibold text-gray-600">Tipo Doc. Caja: </x-jet-label>
                <x-general.select name="document_type_id" wire:model="document_type_id">
                    @foreach ($document_types as $item)
                        <option value="{{ $item->id }}"> {{ $item->name }} </option>
                    @endforeach
                </x-general.select>
                <x-jet-input-error for="document_type_id" />
            </div>
            <div class="col-span-3 p-2">
                <label for="date_time">Fecha: </label>
                <x-general.input type="date" disabled id="date_time" placeholder="Fecha" wire:model.defer="date_time" />
                <x-jet-input-error for="date_time" />
            </div>
            <div class="col-span-3 p-2">
                <label for="number">Número: </label>
                <x-general.input type="number" disabled id="number" placeholder="Numero" wire:model.defer="number" />
                <x-jet-input-error for="number" />
            </div>
            <div class="col-span-3 p-2">
                <x-jet-label for="currency_id" class="font-semibold text-gray-600">Moneda: </x-jet-label>
                <x-general.select name="currency_id" wire:model="currency_id">
                    @foreach ($currencies as $item)
                        <option value="{{ $item->id }}"> {{ $item->name }} </option>
                    @endforeach
                </x-general.select>
                <x-jet-input-error for="currency_id" />
            </div>
            <div class="col-span-3 p-2">
                <x-jet-label for="exchange_rate" class="font-semibold text-gray-600">T. Cambio: </x-jet-label>
                <x-general.input type="number" name="exchange_rate" wire:model.defer="exchange_rate"/>
                <x-jet-input-error for="exchange_rate" />
            </div>
            <div class="col-span-3 p-2">
                <x-jet-label for="tipo_documento" class="font-semibold text-gray-600">Tipo Documento: </x-jet-label>
                <x-general.input type="text" name="tipo_documento" wire:model.defer="tipo_documento"/>
                <x-jet-input-error for="tipo_documento" />
            </div>
            <div class="col-span-3 p-2">
                <x-jet-label for="numero_documento" class="font-semibold text-gray-600">Nro Documento: </x-jet-label>
                <x-general.input type="text" name="numero_documento" wire:model.defer="numero_documento"/>
                <x-jet-input-error for="numero_documento" />
            </div>
            <div class="col-span-3 p-2">
                <x-jet-label for="fecha_documento" class="font-semibold text-gray-600">Fecha Documento: </x-jet-label>
                <x-general.input type="date" name="fecha_documento" wire:model.defer="fecha_documento"/>
                <x-jet-input-error for="fecha_documento" />
            </div>
            <div class="col-span-12 sm:col-span-12 p-2">
                <div x-data="{ open: false }">
                    <div @click.away="open = false" class="py-2 sm:mr-2 col-span-1 pb-0">
                        <div class="flex justify-between">
                            <x-jet-label for="search_person" class="font-semibold text-gray-600">Cliente: </x-jet-label>
                        </div>
                        <x-general.input type="text" id="search_person"
                            x-on:click="open = true;"
                            x-on:keyup="open = true;"
                            onclick="this.select()"
                            placeholder="Cliente"
                            wire:model.debounce.400ms="search_person"
                            autocomplete="off"
                            class="select-all"
                            />
                    </div>
                    <div x-show="open" class="col-span-1 pr-2 pt-0 relative w-full">
                        <ul class="absolute bg-gray-100 shadow-md max-w-full right-2 left-0 z-50">
                            @foreach ($search_list_people as $person)
                                <li wire:click="select_active_person('{{ $person["id"] }}')" class="py-2 px-2 hover:bg-blue-100 cursor-pointer">{{ $person["name_complete"] . " - " . $person["document_number"]}}</li>
                            @endforeach
                        </ul>
                    </div>
                    <x-jet-input-error for="client_id" />
                </div>
            </div>
            <div class="p-2 col-span-6">
                <label for="comment">Comentario: </label>
                <x-general.textarea type="comment"  id="comment" placeholder="Comentario" wire:model.defer="comment" >
                </x-general.textarea>
                <x-jet-input-error for="comment" />
            </div>
            <div class="mt-6 border-2 border-gray-200 p-3 rounded-md col-span-3">
                <div class="w-full text-left text-base font-bold pl-5 mt-1">
                    TOTAL: {{ $currency["symbol"] }} {{ number_format($current_payment_amount, 2) }}
                </div>
                <div class="grid grid-cols-12">
                    <div class="col-span-12 xl:col-span-12 grid grid-cols-1 divide-y divide-gray-100">
                        <div class=" divide-y divide-gray-100">
                            @foreach ($payments as $key => $item)
                            <div class="grid justify-items-end w-full md:pr-5">
                                <div class="grid sm:flex items-center p-2">
                                    <x-jet-label for="barcode" class="text-xs min-w-max mt-1 mr-3">{{ $item["name"] }}: </x-jet-label>
                                    <div class="flex">
                                        <x-general.input type="number" wire:model.lazy="payments.{{ $key }}.amount" :full="false" :block="false" class="w-52 mt-0 text-right"/>
                                        <button class="ml-3" wire:click="quit_method_item('{{ $key }}')" wire:loading.attr="disabled" wire:target="quit_method_item('{{ $key }}')"
                                                wire:key="quit-payment-{{ $key }}"><i class="fas fa-times text-red-600 text-base"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        <div class="grid justify-items-end w-full pr-6 md:pr-11 p-2">
                            <div x-data="{ open_method: false }">
                                <div x-show="open_method" class="col-span-1 pr-2 pt-0 relative w-full">
                                    <ul class="absolute bg-gray-100 shadow-md max-w-full right-2 left-0 z-50 bottom-full">
                                        @foreach ($payment_methods as $key => $payment_method)
                                            <li wire:click="select_payment_method('{{ $key }}')"  class="py-2 px-2 hover:bg-blue-100 cursor-pointer text-center">{{ $payment_method["name"]}}</li>
                                        @endforeach
                                    </ul>
                                </div>
                                <div @click.away="open_method = false" class="py-2 sm:mr-2 col-span-1">
                                    <x-general.button class="bg-blue-300 text-white hover:bg-blue-400 active:bg-blue-500 mx-2"
                                    x-on:click="open_method = !open_method;"><i class="fas fa-plus mr-2" aria-hidden="true"></i> Método de pago </x-general.button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


        </div>
        <div class="mt-4 flex justify-end">
            <x-general.button x-on:click="show=false"  class="bg-gray-200 text-black hover:bg-gray-400 active:bg-gray-500 mx-2" wire:click="back_button">
                <i class="fas fa-arrow-left mr-2" aria-hidden="true"></i> {{ __(' Atrás') }}
            </x-general.button>
            @if ($checkout->id)
                <x-general.button class="bg-blue-500 text-white hover:bg-blue-600 active:bg-blue-700 mx-2" wire:click="save"
                    wire:loading.attr="disabled" wire:target="save" wire:key="save-checkout">
                    <i class="fas fa-save mr-2" aria-hidden="true"></i> {{ __(' Guardar') }}
                </x-general.button>
            @endif

        </div>
    @if (!$modal)
        </div>
    @endif
</div>
