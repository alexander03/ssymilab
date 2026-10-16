@props(['submit'])

<div {{ $attributes->merge(['class' => 'row']) }}>
    <div class="col-12">
        <div class="card">
            <form wire:submit.prevent="{{ $submit }}">
                <div class="card-body">
                    <h4 class="card-title">{{ $title }}</h4>
                    <div class="px-2 py-3 bg-white sm:p-6">
                        <div class="">
                            {{ $form }}
                        </div>
                    </div>

                </div>
                @if (isset($actions))
                    <div class="flex items-center justify-end px-2 py-3 bg-gray-50 text-right sm:px-6 shadow sm:rounded-bl-md sm:rounded-br-md">
                        {{ $actions }}
                    </div>
                @endif
            </form>
        </div>
    </div>
</div>




