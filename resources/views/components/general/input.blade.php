@props(['disabled' => false, 'full' => true, 'block' => true, 'id' => ""])

<input {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge(['class' => "mt-1 placeholder-gray-400 text-xs md:text-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm disabled:opacity-60" . ($full ? " w-full" : "") . ($block ? " block" : ""), "id" => $id]) !!}>
