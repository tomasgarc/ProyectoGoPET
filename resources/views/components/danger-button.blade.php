<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center justify-center px-5 py-2.5 bg-rose-600 border border-transparent rounded-2xl font-bold text-xs text-white uppercase tracking-widest hover:bg-rose-700 active:bg-rose-800 focus:outline-none focus:ring-2 focus:ring-rose-500 focus:ring-offset-2 hover:scale-[1.02] active:scale-[0.98] shadow-md shadow-rose-200/20 transition-all duration-150']) }}>
    {{ $slot }}
</button>
