@props(['justify' => 'justify-end'])
<div class="">
    <div class="col-12">
        <div class="card">
            @if (isset($head))
                <div class="flex items-center {{$justify}} px-3 py-3  text-right sm:px-6 shadow sm:rounded-tl-md sm:rounded-tr-md">
                    {{ $head }}
                </div>
            @endif
            <div class="card-body p-1 ">
                <div class="bg-white p-3 sm:p-4">
                    <div class="grid grid-cols-1 gap-1">
                        {{ $body }}
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
