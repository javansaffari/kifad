<button
    {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-4 py-2 bg-[#009595] border border-transparent rounded text-xs text-white uppercase tracking-widest hover:bg-[#007070] focus:bg-[#007070] active:bg-[#007070] focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-50 transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>
