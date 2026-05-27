<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Detalles de la Petición') }}
            </h2>
            <a href="javascript:history.back()" class="inline-flex items-center text-sm font-semibold text-gray-600 hover:text-indigo-600 transition-colors">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                {{ __('Volver') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-lg relative mb-6 flex items-center shadow-sm" role="alert">
                    <svg class="w-5 h-5 mr-2 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span class="block sm:inline font-medium text-sm">{{ session('success') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3 rounded-lg relative mb-6 flex items-center shadow-sm" role="alert">
                    <svg class="w-5 h-5 mr-2 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <span class="block sm:inline font-medium text-sm">{{ session('error') }}</span>
                </div>
            @endif

            <!-- Banner de Finalizada si corresponde -->
            @if($careRequest->isFinalized())
                <div class="bg-gray-100 border-l-4 border-gray-500 text-gray-700 p-4 mb-6 shadow-sm flex items-start">
                    <svg class="w-6 h-6 mr-3 text-gray-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <div>
                        <p class="font-bold">{{ __('Esta petición ha finalizado') }}</p>
                        <p class="text-sm text-gray-600">{{ __('El plazo del cuidado ha expirado o se ha marcado como finalizada de forma automática.') }}</p>
                    </div>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm rounded-lg border border-gray-100">
                <div class="p-6 sm:p-8 text-gray-900">
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8 pb-6 border-b border-gray-100">
                        <div>
                            <div class="flex items-center space-x-3">
                                <h3 class="text-xl font-black text-gray-900">{{ __('Petición de Cuidado') }}</h3>
                                <span class="px-2.5 py-1 text-xs font-black rounded-sm uppercase tracking-wider
                                    {{ $careRequest->getResolvedStatus() === 'pending' ? 'bg-amber-100 text-amber-800 border border-amber-200' : ($careRequest->getResolvedStatus() === 'accepted' ? 'bg-emerald-100 text-emerald-800 border border-emerald-200' : 'bg-gray-100 text-gray-800 border border-gray-200') }}">
                                    {{ $careRequest->getStatusLabel() }}
                                </span>
                                @if($careRequest->user_id !== auth()->id())
                                    <form action="{{ route('care-requests.favorite', $careRequest) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="p-1.5 bg-white border border-gray-200 hover:border-rose-400 hover:bg-rose-50 text-gray-400 hover:text-rose-600 transition-colors shadow-sm focus:outline-none flex items-center justify-center">
                                            @if($careRequest->isFavoritedBy(auth()->id()))
                                                <svg class="w-4.5 h-4.5 text-rose-600 fill-current" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                                </svg>
                                            @else
                                                <svg class="w-4.5 h-4.5 text-gray-400 hover:text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                                </svg>
                                            @endif
                                        </button>
                                    </form>
                                @endif
                            </div>
                            <p class="text-sm text-gray-500 mt-1.5">Publicada el {{ $careRequest->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                        <div class="text-left sm:text-right bg-indigo-50 border border-indigo-100 px-4 py-2 rounded-sm">
                            <p class="text-[10px] text-indigo-500 uppercase font-black tracking-widest">{{ __('Presupuesto total') }}</p>
                            <span class="text-3xl font-black text-indigo-700">{{ number_format($careRequest->price, 0) }}€</span>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                        <!-- Perros -->
                        <div>
                            <h4 class="font-bold text-gray-800 border-b border-gray-100 pb-2.5 mb-4 flex items-center">
                                <span class="mr-2">🐕</span>{{ __('Perros a cuidar') }}
                            </h4>
                            <div class="space-y-4">
                                @foreach($careRequest->dogs as $dog)
                                    <div class="flex items-center space-x-4 p-4 border border-gray-100 bg-gray-50/50 hover:bg-gray-50 transition duration-200">
                                        @if($dog->photo)
                                            <img src="{{ asset('storage/' . $dog->photo) }}" alt="{{ $dog->name }}" class="w-16 h-16 object-cover shadow-sm border border-gray-200">
                                        @else
                                            <div class="w-16 h-16 bg-indigo-100 text-indigo-700 font-black flex items-center justify-center text-xl border border-indigo-200">
                                                {{ substr($dog->name, 0, 1) }}
                                            </div>
                                        @endif
                                        <div>
                                            <p class="font-black text-gray-900 text-base uppercase">{{ $dog->name }}</p>
                                            <p class="text-sm text-gray-500 font-semibold">{{ $dog->breed }} • {{ $dog->age }} años</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Detalles -->
                        <div>
                            <h4 class="font-bold text-gray-800 border-b border-gray-100 pb-2.5 mb-4 flex items-center">
                                <span class="mr-2">📅</span>{{ __('Detalles del servicio') }}
                            </h4>
                            <div class="space-y-4">
                                <div>
                                    <p class="text-xs uppercase text-gray-400 font-black tracking-widest">{{ __('Fechas de Cuidado') }}</p>
                                    <p class="text-gray-800 text-base mt-0.5">
                                        Del <span class="font-bold text-indigo-700">{{ \Carbon\Carbon::parse($careRequest->start_date)->format('d/m/Y') }}</span> 
                                        al <span class="font-bold text-indigo-700">{{ \Carbon\Carbon::parse($careRequest->end_date)->format('d/m/Y') }}</span>
                                    </p>
                                </div>
                                
                                @if($careRequest->description)
                                    <div>
                                        <p class="text-xs uppercase text-gray-400 font-black tracking-widest">{{ __('Notas adicionales') }}</p>
                                        <div class="mt-1 p-4 bg-gray-50 text-sm text-gray-700 border border-gray-100 italic">
                                            "{{ $careRequest->description }}"
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Información de Cuidador (Aceptada) -->
                    @if($careRequest->acceptedBy)
                        <div class="mt-8 p-5 bg-emerald-50/50 border border-emerald-100 rounded-lg">
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                                <div class="flex items-center space-x-4">
                                    <img src="{{ $careRequest->acceptedBy->avatar_url }}" alt="{{ $careRequest->acceptedBy->name }}" class="w-12 h-12 rounded-full object-cover border border-emerald-250 shadow-sm">
                                    <div>
                                        <p class="text-xs uppercase text-emerald-600 font-black tracking-widest">{{ __('Petición Aceptada por') }}</p>
                                        <p class="text-lg font-black text-gray-900">{{ $careRequest->acceptedBy->name }}</p>
                                    </div>
                                </div>
                                <a href="mailto:{{ $careRequest->acceptedBy->email }}" class="inline-flex items-center justify-center px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-sm shadow-sm transition">
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
                        <div class="mt-6 p-6 rounded-lg border {{ $careRequest->payment->status === 'escrow' ? 'bg-amber-50 border-amber-200 text-amber-900 shadow-sm' : ($careRequest->payment->status === 'released' ? 'bg-emerald-50 border-emerald-200 text-emerald-900 shadow-sm' : 'bg-rose-50 border-rose-200 text-rose-900 shadow-sm') }}">
                            <div class="flex items-start space-x-3.5">
                                <div class="text-3xl mt-0.5">
                                    {{ $careRequest->payment->status === 'escrow' ? '🔒' : ($careRequest->payment->status === 'released' ? '✅' : '⚠️') }}
                                </div>
                                <div class="flex-grow">
                                    <h4 class="font-extrabold text-sm uppercase tracking-wider text-slate-800">
                                        @if($careRequest->payment->status === 'escrow')
                                            {{ __('Pago en Depósito de Garantía (Fideicomiso)') }}
                                        @elseif($careRequest->payment->status === 'released')
                                            {{ __('Pago Liberado') }}
                                        @elseif($careRequest->payment->status === 'refunded')
                                            {{ __('Pago Reembolsado') }}
                                        @endif
                                    </h4>

                                    <p class="text-xs mt-2 font-semibold leading-relaxed opacity-90 text-slate-700">
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
                                        <div class="mt-5 flex flex-wrap gap-4">
                                            <!-- Liberar Pago -->
                                            <form action="{{ route('payments.release', $careRequest) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-sm transition uppercase tracking-wider rounded-lg" onclick="return confirm('¿Confirmas que el cuidado de tu mascota ha finalizado correctamente y deseas liberar el pago al cuidador?')">
                                                    {{ __('Confirmar Finalización y Liberar Pago') }}
                                                </button>
                                            </form>

                                            <!-- Cancelar y Reembolsar -->
                                            <form action="{{ route('payments.refund', $careRequest) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" class="px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs shadow-sm transition uppercase tracking-wider rounded-lg" onclick="return confirm('¿Estás seguro de que quieres cancelar esta reserva? El importe se reembolsará automáticamente y la petición volverá a estar activa en el feed.')">
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
                        <div class="mt-8 p-5 bg-indigo-50 border border-indigo-100 rounded-lg">
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                                <div class="flex items-center space-x-4">
                                    <img src="{{ $careRequest->user->avatar_url }}" alt="{{ $careRequest->user->name }}" class="w-12 h-12 rounded-full object-cover border border-indigo-250 shadow-sm">
                                    <div>
                                        <p class="text-xs uppercase text-indigo-500 font-black tracking-widest">{{ __('Publicado por') }}</p>
                                        <p class="text-lg font-black text-gray-900">{{ $careRequest->user->name }}</p>
                                    </div>
                                </div>
                                @if(!$careRequest->isFinalized())
                                    <div class="flex flex-col sm:flex-row gap-3">
                                        <form action="{{ route('chats.start', $careRequest) }}" method="POST" class="w-full sm:w-auto">
                                            @csrf
                                            <button type="submit" class="w-full inline-flex items-center justify-center px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-black text-sm uppercase tracking-wider shadow-sm transition">
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                                </svg>
                                                {{ __('Enviar Mensaje / Abrir Chat') }}
                                            </button>
                                        </form>

                                        <form action="{{ route('care-requests.favorite', $careRequest) }}" method="POST" class="inline flex-shrink-0">
                                            @csrf
                                            <button type="submit" class="p-2.5 bg-white border-2 border-rose-600 hover:bg-rose-50 text-rose-600 transition shadow-sm flex items-center justify-center" title="{{ $careRequest->isFavoritedBy(auth()->id()) ? __('Quitar de Favoritos') : __('Marcar como Favorito') }}">
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
                                    <span class="text-sm font-semibold text-gray-400 italic">{{ __('Petición finalizada') }}</span>
                                @endif
                            </div>
                        </div>
                    @endif

                    <!-- Formulario de Aceptar Petición (Solo para el Creador, si está Normal/Pending y Activa) -->
                    @if($careRequest->user_id === auth()->id() && $careRequest->status === 'pending' && !$careRequest->isFinalized())
                        <div class="mt-8 p-6 bg-amber-50/50 border border-amber-200 rounded-lg">
                            <h4 class="font-black text-amber-800 text-base uppercase mb-2 flex items-center">
                                <span class="mr-2">🤝</span>{{ __('Elegir Cuidador y Pagar') }}
                            </h4>
                            <p class="text-xs text-amber-700/80 font-bold mb-4">
                                {{ __('Selecciona al cuidador con el que hayas chateado. Al continuar, serás redirigido a la pasarela de pago para confirmar la reserva con un depósito en garantía (estilo Airbnb). El dinero no se liberará hasta que confirmes la finalización del servicio.') }}
                            </p>
                            <form action="{{ route('care-requests.accept', $careRequest) }}" method="POST" class="flex flex-col sm:flex-row items-end gap-4">
                                @csrf
                                <div class="flex-grow w-full">
                                    <label for="accepted_by" class="block text-xs font-black text-gray-700 uppercase tracking-wider mb-1.5">{{ __('Selecciona el cuidador') }}</label>
                                    <select id="accepted_by" name="accepted_by" class="block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm font-semibold py-2" required {{ $users->isEmpty() ? 'disabled' : '' }}>
                                        @if($users->isEmpty())
                                            <option value="" disabled selected>{{ __('Ningún usuario te ha contactado por chat todavía') }}</option>
                                        @else
                                            <option value="" disabled selected>{{ __('-- Elegir cuidador --') }}</option>
                                            @foreach($users as $user)
                                                <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                                <button type="submit" class="w-full sm:w-auto px-5 py-2.5 bg-indigo-700 hover:bg-indigo-800 text-white font-bold text-sm shadow-md transition uppercase tracking-wider flex-shrink-0 disabled:opacity-50 disabled:cursor-not-allowed" {{ $users->isEmpty() ? 'disabled' : '' }} style="background-color: #4338ca; color: white;">
                                    {{ __('Proceder al Pago') }}
                                </button>
                            </form>
                        </div>
                    @endif

                    <!-- Acciones del pie -->
                    <div class="mt-10 pt-6 border-t border-gray-100 flex justify-between items-center">
                        <div>
                            @if($careRequest->user_id === auth()->id() && $careRequest->status === 'pending')
                                <form action="{{ route('care-requests.destroy', $careRequest) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="inline-flex items-center text-sm text-red-600 font-bold hover:text-red-800 hover:underline transition" onclick="return confirm('¿Estás seguro de que quieres eliminar esta petición?')">
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
</x-app-layout>
