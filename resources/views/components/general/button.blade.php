<button
    {{ $attributes->merge(['type' => 'button', 'class' => 'items-center px-3 py-2 border border-transparent rounded-md font-semibold text-xs uppercase tracking-widest focus:outline-none focus:ring active:bg-gray-900 focus:border-gray-900 focus:ring-gray-300  disabled:opacity-25 transition']) }}>
    {{ $slot }}
</button>
