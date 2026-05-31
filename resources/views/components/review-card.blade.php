@props(['review', 'showReviewer' => true])

@php
    $targetUser = $showReviewer ? $review->reviewer : $review->reviewee;
    $careRequest = $review->careRequest;
@endphp

<div class="bg-white/90 backdrop-blur-sm rounded-3xl p-5 border border-brand-100/50 shadow-md hover:shadow-lg hover:shadow-brand-100/10 transition-all duration-300 relative overflow-hidden flex flex-col justify-between">
    <!-- Efecto decorativo de fondo -->
    <div class="absolute -right-6 -bottom-6 w-20 h-20 bg-brand-50/40 rounded-full blur-lg pointer-events-none"></div>

    <div class="space-y-4">
        <!-- Cabecera: Perfil de Usuario y Fecha -->
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <img src="{{ $targetUser->avatar_url }}" alt="{{ $targetUser->name }}" class="w-10 h-10 rounded-full object-cover border border-brand-100 shadow-sm">
                <div>
                    <h4 class="text-xs font-bold uppercase tracking-wider text-brand-600">
                        {{ $showReviewer ? __('De: ') : __('Para: ') }}
                    </h4>
                    <span class="text-sm font-black text-accent-950 tracking-tight block leading-tight">{{ $targetUser->name }}</span>
                </div>
            </div>
            <span class="text-[9px] font-bold text-accent-600 uppercase tracking-widest">{{ $review->created_at->format('d/m/Y') }}</span>
        </div>

        <!-- Valoración de Estrellas -->
        <div class="flex items-center text-amber-500 text-base">
            @for($i = 1; $i <= 5; $i++)
                <span>{{ $i <= $review->rating ? '★' : '☆' }}</span>
            @endfor
            <span class="text-xs text-accent-600 font-bold ml-1.5">({{ $review->rating }}/5)</span>
        </div>

        <!-- Comentario -->
        <div class="bg-accent-50/60 p-3.5 border border-brand-50/50 rounded-2xl text-xs font-semibold text-accent-950/80 italic leading-relaxed">
            "{{ $review->comment }}"
        </div>
    </div>

    <!-- Contexto del Servicio Cuidado (Footer) -->
    @if($careRequest)
        <div class="mt-4 pt-3.5 border-t border-brand-50/60 flex flex-col space-y-2 text-[10px] text-accent-600 font-bold">
            <div class="flex justify-between items-center">
                <span>📅 {{ \Carbon\Carbon::parse($careRequest->start_date)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($careRequest->end_date)->format('d/m/Y') }}</span>
                <span class="text-brand-600 font-extrabold font-mono text-[11px]">{{ number_format($careRequest->price, 0) }}€</span>
            </div>
            <div class="flex items-center justify-between">
                <!-- Listado de Perros -->
                <div class="flex flex-wrap gap-1">
                    @foreach($careRequest->dogs as $dog)
                        <span class="px-2 py-0.5 bg-brand-50 text-brand-700 rounded-full font-extrabold text-[9px] uppercase tracking-wide">
                            🐾 {{ $dog->name }}
                        </span>
                    @endforeach
                </div>
                <!-- Enlace al servicio -->
                <a href="{{ route('care-requests.show', $careRequest) }}" class="text-brand-600 hover:text-brand-800 hover:underline transition-colors uppercase tracking-wider text-[8px] font-black">
                    {{ __('Ver Petición') }} →
                </a>
            </div>
        </div>
    @endif
</div>
