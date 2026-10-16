<div {{ $attributes->merge(['class' => 'row']) }}>
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">{{ $title }}</h4>
                <div class="mt-2 md:mt-0 md:col-span-2">
                    <div class="px-2 py-3 sm:p-6">
                        {{ $content }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
