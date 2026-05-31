<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center justify-center px-5 py-2.5 bg-brand-600 border border-transparent rounded-2xl font-bold text-xs text-white uppercase tracking-widest hover:bg-brand-700 active:bg-brand-800 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:ring-offset-2 hover:scale-[1.02] active:scale-[0.98] shadow-md shadow-brand-200/20 transition-all duration-150']) }}>
    {{ $slot }}
</button>
