<div>
    <div class="col-12">
        <div class="card">
            @if (isset($actions_card))
                <div class="flex items-center justify-end px-3 py-3 bg-gray-50 text-right sm:px-6 shadow">
                    {{ $actions_card }}
                </div>
            @endif
            <div class="card-body">
                <h4 class="card-title"> {{ $title_card }} </h4>
                <div class="px-1 py-2 bg-white sm:p-6">
                    <div class="grid grid-cols-1 w-full">
                        {{ $content_card }}
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
