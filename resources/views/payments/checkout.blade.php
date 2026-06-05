<x-app-layout>
    <!-- Importar tipografía premium de Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Share+Tech+Mono&display=swap" rel="stylesheet">

    <style>
        .font-card {
            font-family: 'Share Tech Mono', monospace;
        }
        /* Animaciones y transiciones premium */
        .glow-input:focus {
            box-shadow: 0 0 0 4px rgba(79, 138, 119, 0.15);
            border-color: #4f8a77;
        }
        .highlight-active {
            border: 1px solid rgba(255, 255, 255, 0.4);
            background-color: rgba(255, 255, 255, 0.12);
            border-radius: 12px;
            padding: 2px 6px;
            transition: all 0.3s ease;
        }
        .highlight-idle {
            border: 1px solid transparent;
            background-color: transparent;
            padding: 2px 6px;
            transition: all 0.3s ease;
        }
    </style>

    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-bold text-2xl text-brand-900 leading-tight tracking-tight">
                {{ __('Confirmar y Pagar Reserva') }}
            </h2>
            <a href="javascript:history.back()" class="inline-flex items-center text-sm font-bold text-accent-600 hover:text-brand-600 transition-all duration-200">
                <svg class="w-4 h-4 mr-1.5 transform hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                {{ __('Volver') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12 bg-gradient-to-b from-accent-50 via-brand-50/10 to-accent-100 min-h-screen">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            
            @if (session('error'))
                <div class="bg-rose-50 border border-rose-200 text-rose-800 px-5 py-4 rounded-3xl relative mb-8 flex flex-col shadow-sm" role="alert">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        <span class="font-bold text-sm">{{ session('error') }}</span>
                    </div>
                </div>
            @endif

            @if ($errors->any())
                <div class="bg-rose-50 border border-rose-200 text-rose-800 px-5 py-4 rounded-3xl relative mb-8 flex flex-col shadow-sm" role="alert">
                    <div class="flex items-center mb-2">
                        <svg class="w-5 h-5 mr-2 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        <span class="font-bold text-sm">{{ __('Por favor, corrige los siguientes errores:') }}</span>
                    </div>
                    <ul class="list-disc pl-7 text-xs font-semibold space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
                
                <!-- Columna Izquierda: Detalles del Servicio y Cuidador -->
                <div class="lg:col-span-5 space-y-6">
                    
                    <!-- Tarjeta del Cuidador Premium -->
                    <div class="bg-white rounded-3xl p-6 border border-brand-100/50 shadow-xl shadow-brand-50/10 relative overflow-hidden">
                        <div class="absolute top-0 right-0 w-24 h-24 bg-brand-500/5 rounded-full blur-xl -mr-6 -mt-6"></div>
                        <h3 class="text-[10px] uppercase text-accent-500 font-black tracking-widest mb-4">{{ __('Tu Cuidador Seleccionado') }}</h3>
                        
                        <div class="flex items-center space-x-4">
                            <img src="{{ $caretaker->avatar_url }}" alt="{{ $caretaker->name }}" class="w-16 h-16 rounded-2xl object-cover shadow-md border-2 border-brand-200">
                            <div class="flex-grow">
                                <h4 class="text-base font-black text-brand-900 tracking-tight">{{ $caretaker->name }}</h4>
                                <p class="text-xs text-accent-550 font-semibold truncate">{{ $caretaker->email }}</p>
                                @if($caretaker->reviews_count > 0)
                                    <div class="flex items-center mt-1.5 text-amber-500 text-xs">
                                        <span class="mr-1">★</span>
                                        <span class="font-bold text-accent-700">{{ $caretaker->average_rating }}</span>
                                        <span class="text-accent-400 ml-1.5 font-medium">({{ $caretaker->reviews_count }} {{ $caretaker->reviews_count === 1 ? __('valoración') : __('valoraciones') }})</span>
                                    </div>
                                @else
                                    <div class="flex items-center mt-1.5 text-accent-400 text-[10px] font-bold">
                                        <span class="mr-1">☆</span>
                                        <span>{{ __('Sin valoraciones aún') }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Resumen del Servicio -->
                    <div class="bg-white rounded-3xl p-6 border border-brand-100/50 shadow-xl shadow-brand-50/10 space-y-5">
                        <h3 class="text-[10px] uppercase text-accent-500 font-black tracking-widest border-b border-brand-50 pb-3 mb-2">{{ __('Resumen del Cuidado') }}</h3>
                        
                        <!-- Fechas -->
                        <div class="flex items-center space-x-3.5">
                            <div class="bg-brand-50 text-brand-700 p-2.5 rounded-2xl text-lg">📅</div>
                            <div>
                                <p class="text-[9px] uppercase text-accent-500 font-black tracking-wider leading-none mb-1">{{ __('Fechas del servicio') }}</p>
                                <p class="text-brand-900 text-sm font-bold">
                                    Del {{ \Carbon\Carbon::parse($careRequest->start_date)->format('d/m/Y') }} al {{ \Carbon\Carbon::parse($careRequest->end_date)->format('d/m/Y') }}
                                </p>
                            </div>
                        </div>

                        <!-- Perros -->
                        <div class="flex items-start space-x-3.5">
                            <div class="bg-brand-50 text-brand-700 p-2.5 rounded-2xl text-lg mt-0.5">🐕</div>
                            <div>
                                <p class="text-[9px] uppercase text-accent-500 font-black tracking-wider leading-none mb-1.5">{{ __('Mascotas a cuidar') }}</p>
                                <div class="flex flex-wrap gap-1.5">
                                    @foreach($careRequest->dogs as $dog)
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-brand-50/80 text-brand-700 border border-brand-100 shadow-sm">
                                            {{ $dog->name }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <!-- Descripción -->
                        @if($careRequest->description)
                            <div class="bg-accent-50/20 rounded-2xl p-4 border border-brand-50/50 text-xs text-accent-600 italic leading-relaxed">
                                "{{ $careRequest->description }}"
                            </div>
                        @endif
                    </div>

                    <!-- Garantía de Pago de GoPET -->
                    <div class="bg-gradient-to-tr from-brand-50 to-brand-100/30 border border-brand-200/50 rounded-3xl p-6 shadow-sm flex items-start space-x-4">
                        <div class="bg-brand-500 text-white rounded-2xl p-3 flex-shrink-0 shadow-md shadow-brand-100">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                        </div>
                        <div>
                            <h4 class="font-black text-sm text-brand-900 tracking-tight uppercase">{{ __('Garantía Fideicomiso GoPET') }}</h4>
                            <p class="text-xs text-brand-800/80 font-bold mt-1 leading-relaxed">
                                {{ __('Retenemos tu pago de forma segura y solo liberamos el dinero al cuidador cuando el servicio finalice y des tu conformidad.') }}
                            </p>
                        </div>
                    </div>

                </div>

                <!-- Columna Derecha: Pasarela de Pago Premium -->
                <div class="lg:col-span-7">
                    
                    <div class="bg-white rounded-3xl border border-brand-100/50 shadow-xl overflow-hidden">
                        
                        <!-- Header del Checkout -->
                        <div class="p-6 bg-brand-900 text-white flex justify-between items-center relative overflow-hidden">
                            <!-- Brillo decorativo -->
                            <div class="absolute right-0 bottom-0 w-32 h-32 bg-white/5 rounded-full blur-2xl"></div>
                            
                            <div class="relative z-10">
                                <h3 class="font-black text-lg tracking-tight uppercase">{{ __('Pago Seguro') }}</h3>
                                <p class="text-brand-100/70 text-xs mt-0.5 font-medium">{{ __('Conexión encriptada SSL de 256 bits') }}</p>
                            </div>
                            <div class="flex items-center space-x-2.5 relative z-10">
                                <span class="text-xs font-bold text-brand-200 bg-white/10 px-3 py-1 rounded-full border border-white/20 font-mono tracking-wide uppercase">Sandbox</span>
                            </div>
                        </div>

                        <!-- Formulario y Tarjeta -->
                        <div class="p-6 sm:p-8 space-y-8">
                            
                            <!-- Simulación de Tarjeta de Crédito Glassmorphic con Animación -->
                            <div id="visual_card" class="relative w-full max-w-[360px] h-[200px] mx-auto rounded-3xl text-white font-card shadow-2xl overflow-hidden transition-all duration-500 transform hover:scale-[1.03] bg-gradient-to-br from-brand-950 via-brand-900 to-brand-800 border border-white/10">
                                <!-- Efecto de brillo -->
                                <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_right,rgba(255,255,255,0.06),transparent_60%)]"></div>
                                
                                <div class="absolute inset-0 p-6 flex flex-col justify-between">
                                    <!-- Fila Superior: Chip y Marca -->
                                    <div class="flex justify-between items-start">
                                        <!-- Chip metálico realístico -->
                                        <div class="w-10 h-7.5 bg-gradient-to-r from-amber-300 via-yellow-400 to-amber-300 rounded-md relative shadow-inner border border-amber-250 overflow-hidden">
                                            <div class="absolute inset-y-0 left-2.5 border-l border-amber-600/30 w-1"></div>
                                            <div class="absolute inset-y-0 right-2.5 border-r border-amber-600/30 w-1"></div>
                                            <div class="absolute inset-x-0 top-2 border-t border-amber-600/30 h-1"></div>
                                            <div class="absolute inset-x-0 bottom-2 border-b border-amber-600/30 h-1"></div>
                                        </div>
                                        <!-- Marca de la tarjeta -->
                                        <div id="card_brand_logo" class="text-right text-xs font-black italic text-brand-100 tracking-widest">
                                            GoPET Pay
                                        </div>
                                    </div>

                                    <!-- Fila Media: Estado Seguro -->
                                    <div class="my-3 text-center">
                                        <div class="text-sm tracking-wide text-brand-200 font-bold uppercase flex items-center justify-center space-x-2">
                                            <span>🔒 Pago 100% Seguro</span>
                                        </div>
                                        <div class="text-[10px] text-brand-350 mt-1 font-bold">
                                            Conexión Segura vía Stripe
                                        </div>
                                    </div>

                                    <!-- Fila Inferior: Titular y Expiración -->
                                    <div class="flex justify-between items-end text-xs">
                                        <div class="max-w-[70%]">
                                            <span class="text-[8px] uppercase tracking-widest text-brand-200 block mb-0.5 font-bold">{{ __('Plataforma de Pago') }}</span>
                                            <div class="font-bold uppercase truncate tracking-wider text-slate-100 text-[11px] sm:text-xs">
                                                Stripe Gateway
                                            </div>
                                        </div>
                                        <div class="text-right font-sans">
                                            <span class="text-[8px] uppercase tracking-widest text-brand-200 block mb-0.5 font-bold">{{ __('Certificado') }}</span>
                                            <div class="font-bold text-slate-100 text-[10px] uppercase font-card">
                                                PCI-DSS
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Formulario de Tarjeta -->
                            <form action="{{ route('payments.process', $careRequest) }}" method="POST" id="checkout-form" class="space-y-5">
                                @csrf
                                <input type="hidden" name="caretaker_id" value="{{ $caretaker->id }}">

                                <div class="grid grid-cols-1 gap-4 text-slate-700">
                                    <div class="bg-brand-50/60 border border-brand-100 rounded-2xl p-5 text-sm font-bold text-brand-900 space-y-4">
                                        <div class="flex items-center space-x-3 text-brand-800">
                                            <div class="bg-brand-500 text-white rounded-full p-1 flex-shrink-0">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3.5" d="M5 13l4 4L19 7" />
                                                </svg>
                                            </div>
                                            <span class="font-black text-xs uppercase tracking-wider text-brand-900">{{ __('Garantía de Pago Fideicomiso') }}</span>
                                        </div>
                                        <p class="text-xs text-accent-600 leading-relaxed font-semibold">
                                            {{ __('Al hacer clic en el botón de pago, serás redirigido a la pasarela oficial y segura de Stripe. Tus datos bancarios están totalmente protegidos y encriptados bajo la normativa PCI-DSS. GoPET retendrá de forma segura el importe y solo lo liberará al cuidador una vez que confirmes que el servicio de cuidado se ha completado correctamente.') }}
                                        </p>
                                    </div>
                                </div>

                                <!-- Factura/Ticket de Gastos Estilo Premium -->
                                <div class="mt-8 pt-6 border border-brand-100/50 space-y-3 text-sm font-bold text-accent-700 bg-accent-50/20 p-5 rounded-2xl">
                                    <div class="flex justify-between">
                                        <span class="text-accent-500">{{ __('Servicio de Cuidado') }}</span>
                                        <span class="text-accent-850 font-mono">{{ number_format($amount, 2) }}€</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-accent-500 flex items-center">
                                            {{ __('Comisión de plataforma') }}
                                            <span class="ml-1.5 text-[9px] bg-brand-50 text-brand-700 border border-brand-100 px-1.5 py-0.5 rounded font-black">10%</span>
                                        </span>
                                        <span class="text-accent-850 font-mono">{{ number_format($fee, 2) }}€</span>
                                    </div>
                                    <div class="flex justify-between text-base font-black text-brand-900 pt-3.5 border-t border-dashed border-brand-100">
                                        <span class="text-brand-900">{{ __('Total a pagar') }}</span>
                                        <span class="text-brand-600 text-xl font-mono">{{ number_format($amount, 2) }}€</span>
                                    </div>
                                    <div class="text-[9px] text-accent-400 leading-normal font-semibold text-center pt-2">
                                        * El cuidador recibirá {{ number_format($netAmount, 2) }}€ una vez finalices el servicio.
                                    </div>
                                </div>

                                <!-- Botón de Pago con Efecto Glow -->
                                <button type="submit" id="submit-btn" class="w-full mt-6 inline-flex items-center justify-center px-6 py-4 bg-brand-600 hover:bg-brand-700 text-white font-bold text-sm uppercase tracking-wider rounded-2xl shadow-lg hover:shadow-brand-500/25 transition-all duration-300 transform active:scale-[0.98] hover:scale-[1.01]">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                    </svg>
                                    {{ __('Proceder al Pago con Stripe') }}
                                </button>

                            </form>

                        </div>

                    </div>

                </div>

            </div>
        </div>
    </div>

    <!-- Script de interactividad en Vanilla JS -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const visualCard = document.getElementById('visual_card');
            const submitBtn = document.getElementById('submit-btn');
            const checkoutForm = document.getElementById('checkout-form');

            // Efecto sutil al pasar por encima del botón de pago
            submitBtn.addEventListener('mouseenter', () => {
                visualCard.style.transform = 'perspective(1000px) rotateY(5deg) scale(1.02)';
            });
            submitBtn.addEventListener('mouseleave', () => {
                visualCard.style.transform = 'perspective(1000px) rotateY(0deg) scale(1)';
            });

            // Rotar y deshabilitar al enviar
            checkoutForm.addEventListener('submit', () => {
                visualCard.style.transform = 'perspective(1000px) rotateY(15deg) scale(0.98)';
                submitBtn.disabled = true;
                submitBtn.innerHTML = `
                    <svg class="animate-spin h-5 w-5 mr-3 text-white" viewBox="0 0 24 24" fill="none">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Redirigiendo a Stripe...
                `;
            });
        });
    </script>
</x-app-layout>
