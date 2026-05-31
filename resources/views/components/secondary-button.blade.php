<button {{ $attributes->merge(['type' => 'button', 'class' => 'inline-flex items-center justify-center px-5 py-2.5 bg-white border border-brand-200 rounded-2xl font-bold text-xs text-brand-700 uppercase tracking-widest shadow-sm hover:bg-brand-50/50 hover:text-brand-800 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:ring-offset-2 disabled:opacity-25 hover:scale-[1.02] active:scale-[0.98] transition-all duration-150']) }}>
    {{ $slot }}
</button>
