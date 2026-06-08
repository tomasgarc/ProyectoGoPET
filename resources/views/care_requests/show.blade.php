<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-bold text-2xl text-brand-900 leading-tight">
                {{ __('Detalles de la Petición') }}
            </h2>
            <a href="javascript:history.back()" class="inline-flex items-center text-sm font-bold text-accent-600 hover:text-brand-600 transition-colors">
                <svg class="w-4 h-4 mr-1 text-brand-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                {{ __('Volver') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="bg-brand-50 border border-brand-200 text-brand-900 px-4 py-3 rounded-2xl relative mb-6 flex items-center shadow-sm" role="alert">
                    <span class="mr-2">✨</span>
                    <span class="font-bold text-sm">{{ session('success') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3 rounded-2xl relative mb-6 flex items-center shadow-sm" role="alert">
                    <span class="mr-2">⚠️</span>
                    <span class="font-bold text-sm">{{ session('error') }}</span>
                </div>
            @endif

            <!-- Banner de Finalizada si corresponde -->
            @if($careRequest->isFinalized())
                <div class="bg-brand-50/50 border-l-4 border-brand-500 text-brand-900 p-4 mb-6 rounded-r-2xl shadow-sm flex items-start">
                    <svg class="w-6 h-6 mr-3 text-brand-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <div>
                        <p class="font-black text-sm uppercase tracking-wider">{{ __('Esta petición ha finalizado') }}</p>
                        <p class="text-xs text-accent-700 font-medium mt-0.5">{{ __('El plazo del cuidado ha expirado o se ha completado de forma definitiva.') }}</p>
                    </div>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm rounded-3xl border border-brand-100/50">
                <div class="p-6 sm:p-8 text-gray-900">
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8 pb-6 border-b border-brand-50">
                        <div>
                            <div class="flex flex-wrap items-center gap-3">
                                <h3 class="text-xl font-black text-brand-900">{{ __('Petición de Cuidado') }}</h3>
                                <span class="px-2.5 py-0.5 text-xs font-bold rounded-full uppercase tracking-wider
                                    {{ $careRequest->getResolvedStatus() === 'pending' ? 'bg-amber-50 text-amber-700 border border-amber-200/50' : ($careRequest->getResolvedStatus() === 'accepted' ? 'bg-brand-50 text-brand-800 border border-brand-100/50' : 'bg-accent-100/50 text-accent-700 border border-brand-100/20') }}">
                                    {{ $careRequest->getStatusLabel() }}
                                </span>
                                @if($careRequest->user_id !== auth()->id())
                                    <form action="{{ route('care-requests.favorite', $careRequest) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="p-1.5 bg-white border border-brand-200 hover:border-rose-400 hover:bg-rose-50/50 text-accent-400 hover:text-rose-600 transition-colors shadow-sm focus:outline-none flex items-center justify-center rounded-xl">
                                            @if($careRequest->isFavoritedBy(auth()->id()))
                                                <svg class="w-4 h-4 text-rose-600 fill-current" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                                </svg>
                                            @else
                                                <svg class="w-4 h-4 text-accent-400 hover:text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                                </svg>
                                            @endif
                                        </button>
                                    </form>
                                @endif
                            </div>
                            <p class="text-xs text-accent-500 font-semibold mt-1.5">Publicada el {{ $careRequest->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                        <div class="text-left sm:text-right bg-brand-50/50 border border-brand-100 px-5 py-2.5 rounded-2xl">
                            <p class="text-[9px] text-brand-700 uppercase font-black tracking-widest">{{ __('Presupuesto total') }}</p>
                            <span class="text-3xl font-black text-brand-900">{{ number_format($careRequest->price, 0) }}€</span>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                        <!-- Perros -->
                        <div>
                            <h4 class="font-black text-sm uppercase text-brand-900 border-b border-brand-50 pb-2.5 mb-4 flex items-center">
                                <span class="mr-2">🐕</span>{{ __('Perros a cuidar') }}
                            </h4>
                            <div class="space-y-4">
                                @foreach($careRequest->dogs as $dog)
                                    <div class="flex items-center space-x-4 p-4 border border-brand-50 bg-accent-50/10 rounded-2xl hover:bg-accent-50/30 transition duration-200">
                                        @if($dog->photo)
                                            <img src="{{ asset('storage/' . $dog->photo) }}" alt="{{ $dog->name }}" class="w-14 h-14 rounded-xl object-cover shadow-sm border border-brand-100">
                                        @else
                                            <div class="w-14 h-14 bg-brand-50 text-brand-700 font-black rounded-xl flex items-center justify-center text-xl border border-brand-100">
                                                {{ substr($dog->name, 0, 1) }}
                                            </div>
                                        @endif
                                        <div>
                                            <p class="font-black text-brand-900 text-sm uppercase">{{ $dog->name }}</p>
                                            <p class="text-xs text-accent-600 font-semibold mt-0.5">{{ $dog->breed }} • {{ $dog->age }} {{ $dog->age == 1 ? 'año' : 'años' }} • {{ ucfirst($dog->sex ?? 'macho') }}</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Detalles -->
                        <div>
                            <h4 class="font-black text-sm uppercase text-brand-900 border-b border-brand-50 pb-2.5 mb-4 flex items-center">
                                <span class="mr-2">📅</span>{{ __('Detalles del servicio') }}
                            </h4>
                            <div class="space-y-4">
                                <div>
                                    <p class="text-[9px] uppercase text-accent-500 font-black tracking-widest">{{ __('Fechas de Cuidado') }}</p>
                                    <p class="text-accent-950 text-sm font-semibold mt-0.5">
                                        Del <span class="font-black text-brand-700">{{ \Carbon\Carbon::parse($careRequest->start_date)->format('d/m/Y') }}</span> 
                                        al <span class="font-black text-brand-700">{{ \Carbon\Carbon::parse($careRequest->end_date)->format('d/m/Y') }}</span>
                                    </p>
                                </div>

                                <div>
                                    <p class="text-[9px] uppercase text-accent-500 font-black tracking-widest">{{ __('Ubicación') }}</p>
                                    <p class="text-accent-950 text-sm font-semibold mt-0.5">
                                        📍 <span class="font-black text-brand-700">{{ $careRequest->location }}</span>
                                    </p>
                                </div>
                                
                                @if($careRequest->description)
                                    <div>
                                        <p class="text-[9px] uppercase text-accent-500 font-black tracking-widest">{{ __('Notas adicionales') }}</p>
                                        <div class="mt-1 p-4 bg-accent-50/20 text-xs font-medium text-accent-900 border border-brand-50/50 rounded-2xl italic leading-relaxed">
                                            "{{ $careRequest->description }}"
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Información de Cuidador (Aceptada) -->
                    @if($careRequest->acceptedBy)
                        <div class="mt-8 p-5 bg-brand-50/30 border border-brand-100 rounded-3xl">
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                                <div class="flex items-center space-x-4">
                                    <img src="{{ $careRequest->acceptedBy->avatar_url }}" alt="{{ $careRequest->acceptedBy->name }}" class="w-12 h-12 rounded-full object-cover border-2 border-brand-200 shadow-sm">
                                    <div>
                                        <p class="text-[9px] uppercase text-brand-700 font-black tracking-widest">{{ __('Petición Aceptada por') }}</p>
                                        <p class="text-base font-black text-brand-900">{{ $careRequest->acceptedBy->name }}</p>
                                        @if($careRequest->acceptedBy->reviews_count > 0)
                                            <div class="flex items-center mt-0.5 text-amber-500 text-xs font-bold">
                                                <span class="mr-0.5">★</span>
                                                <span class="text-accent-700">{{ $careRequest->acceptedBy->average_rating }}</span>
                                                <span class="text-accent-400 font-medium ml-1">({{ $careRequest->acceptedBy->reviews_count }} {{ __('reseñas') }})</span>
                                            </div>
                                        @else
                                            <p class="text-[10px] text-accent-400 font-bold mt-0.5">{{ __('Sin valoraciones aún') }}</p>
                                        @endif
                                    </div>
                                </div>
                                <a href="mailto:{{ $careRequest->acceptedBy->email }}" class="inline-flex items-center justify-center px-4 py-2.5 bg-brand-600 hover:bg-brand-700 text-white font-bold text-xs uppercase tracking-wider rounded-xl shadow-sm transition">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                    </svg>
                                    {{ __('Contactar Cuidador') }}
                                </a>
                            </div>
                        </div>
                    @endif

                    <!-- Estado de Pago e Interacciones de Garantía (Fideicomiso) -->
                    @if($careRequest->payment)
                        <div class="mt-6 p-6 rounded-3xl border {{ $careRequest->payment->status === 'escrow' ? 'bg-amber-50/50 border-amber-200 text-amber-900 shadow-sm' : ($careRequest->payment->status === 'released' ? 'bg-brand-50/50 border-brand-200 text-brand-900 shadow-sm' : 'bg-rose-50/50 border-rose-200 text-rose-900 shadow-sm') }}">
                            <div class="flex items-start space-x-3.5">
                                <div class="text-3xl mt-0.5">
                                    {{ $careRequest->payment->status === 'escrow' ? '🔒' : ($careRequest->payment->status === 'released' ? '✅' : '⚠️') }}
                                </div>
                                <div class="flex-grow space-y-2">
                                    <h4 class="font-black text-sm uppercase tracking-wider text-accent-950">
                                        @if($careRequest->payment->status === 'escrow')
                                            {{ __('Pago en Depósito de Garantía (Fideicomiso)') }}
                                        @elseif($careRequest->payment->status === 'released')
                                            {{ __('Pago Liberado') }}
                                        @elseif($careRequest->payment->status === 'refunded')
                                            {{ __('Pago Reembolsado') }}
                                        @endif
                                    </h4>

                                    <p class="text-xs font-medium leading-relaxed text-accent-700">
                                        @if($careRequest->payment->status === 'escrow')
                                            @if($careRequest->user_id === auth()->id())
                                                {{ __('Hemos retenido los ') }}<strong>{{ number_format($careRequest->price, 0) }}€</strong>{{ __(' de tu pago de forma segura en GoPET. Una vez finalizado el cuidado, marca el servicio como completado para liberar los fondos al cuidador. Si hay algún problema, puedes cancelar y solicitar un reembolso.') }}
                                            @else
                                                {{ __('El dueño de la mascota ya ha realizado el pago de ') }}<strong>{{ number_format($careRequest->price, 0) }}€</strong>{{ __(' (tu ganancia neta será de ') }}<strong>{{ number_format($careRequest->payment->net_amount, 2) }}€</strong>{{ __(' tras la comisión de servicio). El dinero está custodiado por GoPET y se transferirá a tu saldo disponible cuando el dueño marque el servicio como completado.') }}
                                            @endif
                                        @elseif($careRequest->payment->status === 'released')
                                            @if($careRequest->user_id === auth()->id())
                                                {{ __('Los ') }}<strong>{{ number_format($careRequest->price, 0) }}€</strong>{{ __(' han sido transferidos al saldo disponible del cuidador ') }}<strong>{{ $careRequest->acceptedBy->name }}</strong>.
                                            @else
                                                {{ __('¡Felicidades! Los fondos de ') }}<strong>{{ number_format($careRequest->payment->net_amount, 2) }}€</strong>{{ __(' han sido liberados y están disponibles en tu cartera.') }}
                                            @endif
                                        @elseif($careRequest->payment->status === 'refunded')
                                            {{ __('Esta reserva ha sido cancelada y el importe de ') }}<strong>{{ number_format($careRequest->price, 0) }}€</strong>{{ __(' ha sido devuelto a la cuenta del dueño de la mascota.') }}
                                        @endif
                                    </p>

                                    <!-- Acciones para el Dueño en estado Escrow -->
                                    @if($careRequest->payment->status === 'escrow' && $careRequest->user_id === auth()->id())
                                        <div class="mt-4 flex flex-wrap gap-3 pt-2">
                                            <!-- Liberar Pago -->
                                            <form action="{{ route('payments.release', $careRequest) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" class="px-4 py-2.5 bg-brand-600 hover:bg-brand-700 text-white font-bold text-xs uppercase tracking-wider rounded-xl shadow-sm transition hover:scale-[1.02] active:scale-[0.98]" onclick="return confirm('¿Confirmas que el cuidado de tu mascota ha finalizado correctamente y deseas liberar el pago al cuidador?')">
                                                    {{ __('Confirmar y Liberar Pago') }}
                                                </button>
                                            </form>

                                            <!-- Cancelar y Reembolsar -->
                                            <form action="{{ route('payments.refund', $careRequest) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" class="px-4 py-2.5 bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs uppercase tracking-wider rounded-xl shadow-sm transition hover:scale-[1.02] active:scale-[0.98]" onclick="return confirm('¿Estás seguro de que quieres cancelar esta reserva? El importe se reembolsará automáticamente y la petición volverá a estar activa en el feed.')">
                                                    {{ __('Cancelar y Solicitar Reembolso') }}
                                                </button>
                                            </form>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif


                    <!-- Información del Dueño (si no es el usuario logueado) -->
                    @if($careRequest->user_id !== auth()->id())
                        <div class="mt-8 p-5 bg-accent-50/40 border border-brand-100/50 rounded-3xl">
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                                <div class="flex items-center space-x-4">
                                    <img src="{{ $careRequest->user->avatar_url }}" alt="{{ $careRequest->user->name }}" class="w-12 h-12 rounded-full object-cover border border-brand-100 shadow-sm">
                                    <div>
                                        <p class="text-[9px] uppercase text-brand-700 font-black tracking-widest">{{ __('Publicado por') }}</p>
                                        <p class="text-base font-black text-brand-900 leading-tight">{{ $careRequest->user->name }}</p>
                                        @if($careRequest->user->reviews_count > 0)
                                            <div class="flex items-center mt-0.5 text-amber-500 text-xs font-bold">
                                                <span class="mr-0.5">★</span>
                                                <span class="text-accent-700">{{ $careRequest->user->average_rating }}</span>
                                                <span class="text-accent-400 font-medium ml-1">({{ $careRequest->user->reviews_count }} {{ __('reseñas') }})</span>
                                            </div>
                                        @else
                                            <p class="text-[10px] text-accent-400 font-bold mt-0.5">{{ __('Sin valoraciones aún') }}</p>
                                        @endif
                                    </div>
                                </div>
                                @if(!$careRequest->isFinalized())
                                    <div class="flex gap-2">
                                        <form action="{{ route('chats.start', $careRequest) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="inline-flex items-center justify-center px-5 py-2.5 bg-brand-600 hover:bg-brand-700 text-white font-bold text-xs uppercase tracking-wider rounded-xl shadow-sm transition hover:scale-[1.02] active:scale-[0.98]">
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                                </svg>
                                                {{ __('Enviar Mensaje') }}
                                            </button>
                                        </form>

                                        <form action="{{ route('care-requests.favorite', $careRequest) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="p-2.5 bg-white border border-rose-600 hover:bg-rose-50/50 text-rose-600 transition shadow-sm flex items-center justify-center rounded-xl" title="{{ $careRequest->isFavoritedBy(auth()->id()) ? __('Quitar de Favoritos') : __('Marcar como Favorito') }}">
                                                @if($careRequest->isFavoritedBy(auth()->id()))
                                                    <svg class="w-5 h-5 text-rose-600 fill-current" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                                    </svg>
                                                @else
                                                    <svg class="w-5 h-5 text-rose-600 hover:fill-current" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                                    </svg>
                                                @endif
                                            </button>
                                        </form>
                                    </div>
                                @else
                                    <span class="text-xs font-bold text-accent-400 italic">{{ __('Petición finalizada') }}</span>
                                @endif
                            </div>
                        </div>
                    @endif

                    <!-- Formulario de Aceptar Petición (Solo para el Creador, si está Normal/Pending y Activa) -->
                    @if($careRequest->user_id === auth()->id() && $careRequest->status === 'pending' && !$careRequest->isFinalized())
                        <div class="mt-8 p-6 bg-brand-50/30 border border-brand-100/50 rounded-3xl">
                            <h4 class="font-black text-brand-900 text-sm uppercase mb-2 flex items-center">
                                <span class="mr-2">🤝</span>{{ __('Elegir Cuidador y Confirmar') }}
                            </h4>
                            <p class="text-xs text-accent-700 font-semibold mb-4 leading-relaxed">
                                {{ __('Selecciona al cuidador de confianza con el que hayas chateado. Al continuar, serás redirigido a la pasarela de pago simulada para depositar los fondos en garantía de forma segura.') }}
                            </p>
                            <form action="{{ route('care-requests.accept', $careRequest) }}" method="POST" class="flex flex-col sm:flex-row items-end gap-4">
                                @csrf
                                <div class="flex-grow w-full">
                                    <label for="accepted_by" class="block text-[10px] font-black text-accent-600 uppercase tracking-wider mb-1.5">{{ __('Selecciona el cuidador') }}</label>
                                    <select id="accepted_by" name="accepted_by" class="block w-full border-brand-200/80 focus:border-brand-500 focus:ring-brand-500/20 text-xs font-semibold py-2.5 px-3 rounded-2xl bg-white" required {{ $users->isEmpty() ? 'disabled' : '' }}>
                                        @if($users->isEmpty())
                                            <option value="" disabled selected>{{ __('Ningún usuario te ha contactado por chat todavía') }}</option>
                                        @else
                                            <option value="" disabled selected>{{ __('-- Elegir cuidador --') }}</option>
                                            @foreach($users as $user)
                                                <option value="{{ $user->id }}">
                                                    {{ $user->name }} ({{ $user->email }}) 
                                                    @if($user->reviews_count > 0)
                                                        — ★ {{ $user->average_rating }} ({{ $user->reviews_count }} {{ $user->reviews_count === 1 ? __('reseña') : __('reseñas') }})
                                                    @else
                                                        — {{ __('Sin valoraciones aún') }}
                                                    @endif
                                                </option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                                <button type="submit" class="w-full sm:w-auto px-5 py-2.5 bg-brand-200 hover:bg-brand-500 text-brand-900 hover:text-white font-bold text-xs uppercase tracking-wider rounded-xl shadow-sm transition disabled:opacity-50 disabled:cursor-not-allowed hover:scale-[1.02] active:scale-[0.98] flex-shrink-0" {{ $users->isEmpty() ? 'disabled' : '' }}>
                                    {{ __('Proceder al Pago') }}
                                </button>
                            </form>
                        </div>
                    @endif

                    <!-- Sección de Reseñas / Valoraciones (Solo si está Finalizada) -->
                    @if($careRequest->isFinalized())
                        @php
                            $user = auth()->user();
                            $isParticipant = ($user->id === $careRequest->user_id || $user->id === $careRequest->accepted_by);
                        @endphp

                        @if($isParticipant)
                            <div class="mt-8 pt-8 border-t border-brand-50">
                                <h4 class="font-black text-brand-900 text-sm uppercase mb-6 flex items-center">
                                    <span class="mr-2">⭐</span>{{ __('Valoraciones de la Petición') }}
                                </h4>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <!-- Tu Valoración (Enviada o para enviar) -->
                                    <div class="bg-accent-50/20 border border-brand-50/50 p-5 rounded-3xl">
                                        @if($careRequest->canBeReviewedBy($user))
                                            <h5 class="font-black text-xs text-brand-700 uppercase tracking-wider mb-3.5">{{ __('Dejar tu Reseña') }}</h5>
                                            
                                            <form action="{{ route('reviews.store', $careRequest) }}" method="POST" class="space-y-4">
                                                @csrf
                                                
                                                <div>
                                                    <label class="block text-[10px] font-black text-accent-500 uppercase tracking-widest mb-1.5">{{ __('¿Cómo calificarías la experiencia?') }}</label>
                                                    <div class="flex items-center space-x-1.5" id="star-selector">
                                                        @for($i = 1; $i <= 5; $i++)
                                                            <button type="button" data-value="{{ $i }}" class="star-btn text-3xl text-accent-300 hover:scale-110 transition duration-150 focus:outline-none">★</button>
                                                        @endfor
                                                    </div>
                                                    <input type="hidden" name="rating" id="rating-input" required>
                                                </div>

                                                <div>
                                                    <label for="comment" class="block text-[10px] font-black text-accent-500 uppercase tracking-widest mb-1.5">{{ __('Tu Comentario') }}</label>
                                                    <textarea id="comment" name="comment" rows="3" required minlength="5" maxlength="1000"
                                                        class="block w-full border-brand-200 focus:border-brand-500 focus:ring-brand-500/20 text-xs font-semibold rounded-2xl py-2.5 px-3 placeholder-accent-400 transition"
                                                        placeholder="Describe cómo fue trabajar con este usuario..."></textarea>
                                                </div>

                                                <button type="submit" class="w-full inline-flex items-center justify-center px-4 py-2.5 bg-brand-600 hover:bg-brand-700 text-white font-bold text-xs uppercase tracking-wider rounded-xl shadow-sm transition hover:scale-[1.02] active:scale-[0.98]">
                                                    {{ __('Enviar Reseña') }}
                                                </button>
                                            </form>
                                        @else
                                            @php
                                                $myReview = $careRequest->getReviewBy($user);
                                            @endphp

                                            @if($myReview)
                                                <h5 class="font-black text-xs text-brand-600 uppercase tracking-wider mb-3.5">{{ __('Tu Reseña Enviada') }}</h5>
                                                <div class="space-y-2">
                                                    <div class="flex items-center text-amber-500 text-lg">
                                                        @for($i = 1; $i <= 5; $i++)
                                                            <span>{{ $i <= $myReview->rating ? '★' : '☆' }}</span>
                                                        @endfor
                                                        <span class="text-xs text-accent-600 font-bold ml-2">({{ $myReview->rating }} {{ __('estrellas') }})</span>
                                                    </div>
                                                    <p class="text-xs text-accent-900 font-medium italic bg-white p-3 border border-brand-50 rounded-2xl">
                                                        "{{ $myReview->comment }}"
                                                    </p>
                                                    <p class="text-[9px] text-accent-400 font-bold">{{ __('Fecha:') }} {{ $myReview->created_at->format('d/m/Y H:i') }}</p>
                                                </div>
                                            @else
                                                <h5 class="font-black text-xs text-accent-400 uppercase tracking-wider mb-2">{{ __('Dejar tu Reseña') }}</h5>
                                                <p class="text-xs text-accent-400 font-bold italic">{{ __('No es posible valorar esta petición.') }}</p>
                                            @endif
                                        @endif
                                    </div>

                                    <!-- Valoración Recibida -->
                                    <div class="bg-accent-50/20 border border-brand-50/50 p-5 rounded-3xl">
                                        @php
                                            $otherUser = $user->id === $careRequest->user_id ? $careRequest->acceptedBy : $careRequest->user;
                                            $receivedReview = $otherUser ? $careRequest->getReviewBy($otherUser) : null;
                                        @endphp

                                        @if($receivedReview)
                                            <h5 class="font-black text-xs text-brand-600 uppercase tracking-wider mb-3.5">{{ __('Reseña Recibida de :name', ['name' => $otherUser->name]) }}</h5>
                                            <div class="space-y-2">
                                                <div class="flex items-center text-amber-500 text-lg">
                                                    @for($i = 1; $i <= 5; $i++)
                                                        <span>{{ $i <= $receivedReview->rating ? '★' : '☆' }}</span>
                                                    @endfor
                                                    <span class="text-xs text-accent-600 font-bold ml-2">({{ $receivedReview->rating }} {{ __('estrellas') }})</span>
                                                </div>
                                                <p class="text-xs text-accent-900 font-medium italic bg-white p-3 border border-brand-50 rounded-2xl">
                                                    "{{ $receivedReview->comment }}"
                                                </p>
                                                <p class="text-[9px] text-accent-400 font-bold">{{ __('Fecha:') }} {{ $receivedReview->created_at->format('d/m/Y H:i') }}</p>
                                            </div>
                                        @else
                                            <h5 class="font-black text-xs text-accent-400 uppercase tracking-wider mb-3.5">{{ __('Reseña del otro participante') }}</h5>
                                            <div class="text-center py-6">
                                                <span class="text-3xl block opacity-60">⏳</span>
                                                <p class="text-xs text-accent-500 font-bold mt-2 leading-relaxed">
                                                    {{ __('Esperando que :name envíe su valoración sobre ti.', ['name' => $otherUser ? $otherUser->name : 'el otro usuario']) }}
                                                </p>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endif

                    <!-- Acciones del pie -->
                    <div class="mt-10 pt-6 border-t border-brand-50 flex justify-between items-center">
                        <div>
                            @if($careRequest->user_id === auth()->id() && $careRequest->status === 'pending')
                                <form action="{{ route('care-requests.destroy', $careRequest) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="inline-flex items-center text-xs text-rose-600 font-black hover:text-rose-800 hover:underline transition uppercase tracking-wider" onclick="return confirm('¿Estás seguro de que quieres eliminar esta petición?')">
                                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                        {{ __('Eliminar Petición') }}
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Script para selector de estrellas de reseñas interactivo -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const stars = document.querySelectorAll('.star-btn');
            const ratingInput = document.getElementById('rating-input');

            if (stars.length && ratingInput) {
                stars.forEach(star => {
                    star.addEventListener('click', function () {
                        const value = parseInt(this.getAttribute('data-value'));
                        ratingInput.value = value;
                        updateStars(value);
                    });

                    star.addEventListener('mouseover', function () {
                        const value = parseInt(this.getAttribute('data-value'));
                        highlightStars(value);
                    });

                    star.addEventListener('mouseout', function () {
                        const value = parseInt(ratingInput.value) || 0;
                        updateStars(value);
                    });
                });
            }

            function highlightStars(val) {
                stars.forEach(star => {
                    const starVal = parseInt(star.getAttribute('data-value'));
                    if (starVal <= val) {
                        star.classList.remove('text-accent-300');
                        star.classList.add('text-amber-400');
                    } else {
                        star.classList.remove('text-amber-400');
                        star.classList.add('text-accent-300');
                    }
                });
            }

            function updateStars(val) {
                stars.forEach(star => {
                    const starVal = parseInt(star.getAttribute('data-value'));
                    if (starVal <= val) {
                        star.classList.remove('text-accent-300', 'text-amber-400');
                        star.classList.add('text-amber-500');
                    } else {
                        star.classList.remove('text-amber-500', 'text-amber-400');
                        star.classList.add('text-accent-300');
                    }
                });
            }
        });
    </script>
</x-app-layout>
