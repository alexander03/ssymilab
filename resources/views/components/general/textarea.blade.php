@props(['disabled' => false])

<textarea {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge(['class' => 'mt-1 block w-full text-xs md:text-sm border-gray-300 placeholder-gray-400 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm disabled:opacity-60']) !!}></textarea>
