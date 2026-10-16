<div class="overflow-x-auto">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-200">
            <tr>
                @foreach ($thead as $item)
                    <th colspan="{{ $item['colspan'] }}" @php
                        if ($item['link']) {
                            echo "wire:click=\" order('" . $item['link'] . "')\"";
                        }
                    @endphp scope="col"
                        class="px-6 py-3 text-center text-xs font-bold text-gray-600 uppercase tracking-wider cursor-pointer">
                        {{ $item['value'] }}
                        @if ($item['link'])
                            @if ($sort == $item['link'])
                                @if ($direction == 'asc')
                                    <i class="fas fa-sort-alpha-up-alt ml-2"></i>
                                @else
                                    <i class="fas fa-sort-alpha-down-alt ml-2"></i>
                                @endif
                            @else
                                <i class="fas fa-sort ml-2"></i>
                            @endif
                        @endif
                    </th>
                @endforeach
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            {{ $slot }}
        </tbody>
    </table>
</div>
