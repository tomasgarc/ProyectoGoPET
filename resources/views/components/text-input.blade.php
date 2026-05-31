@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'border-brand-200/80 focus:border-brand-500 focus:ring-brand-500/20 rounded-2xl shadow-sm text-accent-950 bg-white placeholder-accent-400 transition-all duration-150']) }}>
