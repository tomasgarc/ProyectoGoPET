<x-app-layout>
    <!-- Importar tipografía premium de Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Share+Tech+Mono&display=swap" rel="stylesheet">

    <style>
        .font-premium {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
        .font-card {
            font-family: 'Share Tech Mono', monospace;
        }
        /* Animaciones y transiciones premium */
        .glow-input:focus {
            box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.15);
            border-color: #6366f1;
        }
        .highlight-active {
            border: 1px solid rgba(255, 255, 255, 0.4);
            background-color: rgba(255, 255, 255, 0.12);
            border-radius: 6px;
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
        <div class="flex justify-between items-center font-premium">
            <h2 class="font-extrabold text-2xl text-slate-800 leading-tight tracking-tight">
                {{ __('Confirmar y Pagar Reserva') }}
            </h2>
            <a href="javascript:history.back()" class="inline-flex items-center text-sm font-bold text-slate-500 hover:text-indigo-600 transition-all duration-200">
                <svg class="w-4 h-4 mr-1.5 transform hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                {{ __('Volver') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12 bg-gradient-to-b from-slate-50 via-indigo-50/10 to-slate-100 min-h-screen font-premium">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            
            @if ($errors->any())
                <div class="bg-rose-50 border border-rose-200 text-rose-800 px-5 py-4 rounded-2xl relative mb-8 flex flex-col shadow-sm" role="alert">
                    <div class="flex items-center mb-2">
                        <svg class="w-5 h-5 mr-2 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        <span class="font-extrabold text-sm">{{ __('Por favor, corrige los siguientes errores:') }}</span>
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
                    <div class="bg-white/80 backdrop-blur-md rounded-3xl p-6 border border-white/60 shadow-xl shadow-slate-100/50 relative overflow-hidden">
                        <div class="absolute top-0 right-0 w-24 h-24 bg-indigo-500/5 rounded-full blur-xl -mr-6 -mt-6"></div>
                        <h3 class="text-[10px] uppercase text-slate-400 font-extrabold tracking-widest mb-4">{{ __('Tu Cuidador Seleccionado') }}</h3>
                        
                        <div class="flex items-center space-x-4">
                            <img src="{{ $caretaker->avatar_url }}" alt="{{ $caretaker->name }}" class="w-16 h-16 rounded-2xl object-cover shadow-lg shadow-indigo-200 border border-indigo-105">
                            <div class="flex-grow">
                                <h4 class="text-lg font-extrabold text-slate-800 tracking-tight">{{ $caretaker->name }}</h4>
                                <p class="text-sm text-slate-500 font-semibold truncate">{{ $caretaker->email }}</p>
                                <div class="flex items-center mt-1.5 text-amber-500 text-xs">
                                    <span class="mr-1">★</span>
                                    <span class="font-extrabold text-slate-700">4.9</span>
                                    <span class="text-slate-400 ml-1.5 font-medium">(24 valoraciones)</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Resumen del Servicio -->
                    <div class="bg-white/80 backdrop-blur-md rounded-3xl p-6 border border-white/60 shadow-xl shadow-slate-100/50 space-y-5">
                        <h3 class="text-[10px] uppercase text-slate-400 font-extrabold tracking-widest border-b border-slate-100 pb-3 mb-2">{{ __('Resumen del Cuidado') }}</h3>
                        
                        <!-- Fechas -->
                        <div class="flex items-center space-x-3.5">
                            <div class="bg-indigo-50 text-indigo-600 p-2.5 rounded-xl text-lg">📅</div>
                            <div>
                                <p class="text-[9px] uppercase text-slate-400 font-extrabold tracking-wider leading-none mb-1">{{ __('Fechas del servicio') }}</p>
                                <p class="text-slate-800 text-sm font-extrabold">
                                    Del {{ \Carbon\Carbon::parse($careRequest->start_date)->format('d/m/Y') }} al {{ \Carbon\Carbon::parse($careRequest->end_date)->format('d/m/Y') }}
                                </p>
                            </div>
                        </div>

                        <!-- Perros -->
                        <div class="flex items-start space-x-3.5">
                            <div class="bg-indigo-50 text-indigo-600 p-2.5 rounded-xl text-lg mt-0.5">🐕</div>
                            <div>
                                <p class="text-[9px] uppercase text-slate-400 font-extrabold tracking-wider leading-none mb-1.5">{{ __('Mascotas a cuidar') }}</p>
                                <div class="flex flex-wrap gap-1.5">
                                    @foreach($careRequest->dogs as $dog)
                                        <span class="inline-flex items-center px-3 py-1 rounded-xl text-xs font-bold bg-indigo-50/80 text-indigo-700 border border-indigo-100/80 shadow-sm">
                                            {{ $dog->name }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <!-- Descripción -->
                        @if($careRequest->description)
                            <div class="bg-slate-50/60 rounded-2xl p-4 border border-slate-100 text-xs text-slate-600 italic leading-relaxed">
                                "{{ $careRequest->description }}"
                            </div>
                        @endif
                    </div>

                    <!-- Garantía de Pago de GoPET -->
                    <div class="bg-gradient-to-tr from-emerald-50 to-teal-50/60 border border-emerald-100/80 rounded-3xl p-6 shadow-sm flex items-start space-x-4">
                        <div class="bg-emerald-500 text-white rounded-2xl p-3 flex-shrink-0 shadow-md shadow-emerald-100">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                        </div>
                        <div>
                            <h4 class="font-extrabold text-sm text-emerald-900 tracking-tight uppercase">{{ __('Garantía Fideicomiso GoPET') }}</h4>
                            <p class="text-xs text-emerald-800/80 font-semibold mt-1 leading-relaxed">
                                {{ __('Retenemos tu pago de forma segura y solo liberamos el dinero al cuidador cuando el servicio finalice y des tu conformidad.') }}
                            </p>
                        </div>
                    </div>

                </div>

                <!-- Columna Derecha: Pasarela de Pago Premium -->
                <div class="lg:col-span-7">
                    
                    <div class="bg-white rounded-3xl border border-slate-100 shadow-xl shadow-slate-100/80 overflow-hidden">
                        
                        <!-- Header del Checkout -->
                        <div class="p-6 bg-slate-900 text-white flex justify-between items-center relative overflow-hidden">
                            <!-- Brillo decorativo -->
                            <div class="absolute right-0 bottom-0 w-32 h-32 bg-indigo-500/10 rounded-full blur-2xl"></div>
                            
                            <div class="relative z-10">
                                <h3 class="font-extrabold text-lg tracking-tight uppercase">{{ __('Pago Seguro') }}</h3>
                                <p class="text-slate-400 text-xs mt-0.5 font-medium">{{ __('Conexión encriptada SSL de 256 bits') }}</p>
                            </div>
                            <div class="flex items-center space-x-2.5 relative z-10">
                                <span class="text-xs font-bold text-indigo-300 bg-indigo-500/20 px-3 py-1 rounded-full border border-indigo-500/30 font-mono tracking-wide uppercase">Sandbox</span>
                            </div>
                        </div>

                        <!-- Formulario y Tarjeta -->
                        <div class="p-6 sm:p-8 space-y-8">
                            
                            <!-- Simulación de Tarjeta de Crédito Glassmorphic con Animación -->
                            <div id="visual_card" class="relative w-full max-w-[360px] h-[200px] mx-auto rounded-2xl text-white font-card shadow-2xl overflow-hidden transition-all duration-500 transform hover:scale-[1.03] bg-gradient-to-br from-slate-900 via-indigo-950 to-indigo-900 border border-white/10">
                                <!-- Efecto de brillo -->
                                <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_right,rgba(255,255,255,0.06),transparent_60%)]"></div>
                                
                                <div class="absolute inset-0 p-6 flex flex-col justify-between">
                                    <!-- Fila Superior: Chip y Marca -->
                                    <div class="flex justify-between items-start">
                                        <!-- Chip metálico realístico -->
                                        <div class="w-10 h-7.5 bg-gradient-to-r from-amber-300 via-yellow-400 to-amber-300 rounded-md relative shadow-inner border border-amber-200 overflow-hidden">
                                            <div class="absolute inset-y-0 left-2.5 border-l border-amber-600/30 w-1"></div>
                                            <div class="absolute inset-y-0 right-2.5 border-r border-amber-600/30 w-1"></div>
                                            <div class="absolute inset-x-0 top-2 border-t border-amber-600/30 h-1"></div>
                                            <div class="absolute inset-x-0 bottom-2 border-b border-amber-600/30 h-1"></div>
                                        </div>
                                        <!-- Marca dinámica de la tarjeta -->
                                        <div id="card_brand_logo" class="text-right text-sm font-black italic text-indigo-200 tracking-widest transition-all duration-300">
                                            GoPET Pay
                                        </div>
                                    </div>

                                    <!-- Fila Media: Número -->
                                    <div class="my-3 text-center">
                                        <div id="card_preview_number" class="text-lg sm:text-xl tracking-widest text-slate-100 font-semibold highlight-idle">
                                            •••• •••• •••• ••••
                                        </div>
                                    </div>

                                    <!-- Fila Inferior: Titular y Expiración -->
                                    <div class="flex justify-between items-end text-xs">
                                        <div class="max-w-[70%]">
                                            <span class="text-[8px] uppercase tracking-widest text-indigo-400 block mb-0.5 font-premium font-bold">{{ __('Titular') }}</span>
                                            <div id="card_preview_name" class="font-bold uppercase truncate tracking-wider text-slate-100 highlight-idle text-[11px] sm:text-xs">
                                                {{ __('Tu Nombre Aquí') }}
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <span class="text-[8px] uppercase tracking-widest text-indigo-400 block mb-0.5 font-premium font-bold">{{ __('Vence') }}</span>
                                            <div id="card_preview_expiry" class="font-bold text-slate-100 highlight-idle">
                                                MM/AA
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
                                    
                                    <!-- Nombre del Titular -->
                                    <div>
                                        <label for="card_name" class="block text-[10px] font-extrabold text-slate-500 uppercase tracking-widest mb-1.5">{{ __('Nombre en la Tarjeta') }}</label>
                                        <input type="text" id="card_name" name="card_name" value="{{ old('card_name') }}" required
                                            class="block w-full border-slate-200 focus:border-indigo-500 focus:ring-0 text-sm font-semibold rounded-xl py-2.5 px-3.5 placeholder-slate-400 transition glow-input"
                                            placeholder="JUAN PEREZ GONZALEZ" autocomplete="cc-name">
                                    </div>

                                    <!-- Número de Tarjeta -->
                                    <div>
                                        <label for="card_number" class="block text-[10px] font-extrabold text-slate-500 uppercase tracking-widest mb-1.5">{{ __('Número de Tarjeta') }}</label>
                                        <div class="relative rounded-xl">
                                            <input type="text" id="card_number" name="card_number" value="{{ old('card_number') }}" required maxlength="19"
                                                class="block w-full border-slate-200 focus:border-indigo-500 focus:ring-0 text-sm font-semibold rounded-xl py-2.5 px-3.5 placeholder-slate-400 transition glow-input pr-10"
                                                placeholder="4000 1234 5678 9010" autocomplete="cc-number">
                                            <div class="absolute inset-y-0 right-0 pr-3.5 flex items-center pointer-events-none text-slate-400">
                                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                                </svg>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Fila Expiración y CVV -->
                                    <div class="grid grid-cols-2 gap-4">
                                        
                                        <!-- Vencimiento -->
                                        <div>
                                            <label for="card_expiry" class="block text-[10px] font-extrabold text-slate-500 uppercase tracking-widest mb-1.5">{{ __('Vencimiento (MM/AA)') }}</label>
                                            <input type="text" id="card_expiry" name="card_expiry" value="{{ old('card_expiry') }}" required maxlength="5"
                                                class="block w-full border-slate-200 focus:border-indigo-500 focus:ring-0 text-sm font-semibold rounded-xl py-2.5 px-3.5 placeholder-slate-400 text-center transition glow-input"
                                                placeholder="MM/AA" autocomplete="cc-exp">
                                        </div>

                                        <!-- CVV -->
                                        <div>
                                            <label for="card_cvv" class="block text-[10px] font-extrabold text-slate-500 uppercase tracking-widest mb-1.5">{{ __('CVV / CVC') }}</label>
                                            <input type="password" id="card_cvv" name="card_cvv" required maxlength="4"
                                                class="block w-full border-slate-200 focus:border-indigo-500 focus:ring-0 text-sm font-semibold rounded-xl py-2.5 px-3.5 placeholder-slate-400 text-center transition glow-input"
                                                placeholder="•••" autocomplete="cc-csc">
                                        </div>

                                    </div>

                                </div>

                                <!-- Factura/Ticket de Gastos Estilo Premium -->
                                <div class="mt-8 pt-6 border-t border-slate-100 space-y-3 text-sm font-semibold text-slate-600 bg-slate-50/50 p-5 rounded-2xl border border-slate-100">
                                    <div class="flex justify-between">
                                        <span class="text-slate-500">{{ __('Servicio de Cuidado') }}</span>
                                        <span class="text-slate-800 font-mono">{{ number_format($amount, 2) }}€</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-slate-500 flex items-center">
                                            {{ __('Comisión de plataforma') }}
                                            <span class="ml-1 text-[10px] bg-slate-200 text-slate-700 px-1.5 py-0.5 rounded" title="10% de tasa administrativa ya deducida del precio base">10%</span>
                                        </span>
                                        <span class="text-slate-800 font-mono">{{ number_format($fee, 2) }}€</span>
                                    </div>
                                    <div class="flex justify-between text-base font-black text-slate-900 pt-3.5 border-t border-dashed border-slate-200">
                                        <span class="text-slate-800">{{ __('Total a pagar') }}</span>
                                        <span class="text-indigo-600 text-xl font-mono">{{ number_format($amount, 2) }}€</span>
                                    </div>
                                    <div class="text-[9px] text-slate-400 leading-normal font-semibold text-center pt-2">
                                        * El cuidador recibirá {{ number_format($netAmount, 2) }}€ una vez finalices el servicio.
                                    </div>
                                </div>

                                <!-- Botón de Pago con Efecto Glow -->
                                <button type="submit" class="w-full mt-6 inline-flex items-center justify-center px-6 py-4 bg-indigo-600 hover:bg-indigo-700 text-white font-extrabold text-sm uppercase tracking-wider rounded-2xl shadow-lg hover:shadow-indigo-500/25 transition-all duration-300 transform active:scale-[0.98]">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                    </svg>
                                    {{ __('Proceder al Pago Seguro') }}
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
            const cardNameInput = document.getElementById('card_name');
            const cardNumberInput = document.getElementById('card_number');
            const cardExpiryInput = document.getElementById('card_expiry');
            const cardCvvInput = document.getElementById('card_cvv');
            
            const cardPreviewName = document.getElementById('card_preview_name');
            const cardPreviewNumber = document.getElementById('card_preview_number');
            const cardPreviewExpiry = document.getElementById('card_preview_expiry');
            const cardBrandLogo = document.getElementById('card_brand_logo');
            const visualCard = document.getElementById('visual_card');

            // Detectar marca de tarjeta basándose en los dígitos
            function updateCardBrand(number) {
                const cleanNumber = number.replace(/\s/g, '');
                if (cleanNumber.startsWith('4')) {
                    cardBrandLogo.innerHTML = 'Visa';
                    cardBrandLogo.style.color = '#38bdf8'; // light blue
                } else if (cleanNumber.startsWith('5')) {
                    cardBrandLogo.innerHTML = 'Mastercard';
                    cardBrandLogo.style.color = '#fb923c'; // orange
                } else {
                    cardBrandLogo.innerHTML = 'GoPET Pay';
                    cardBrandLogo.style.color = '#c7d2fe'; // indigo-200
                }
            }

            // Nombre
            cardNameInput.addEventListener('input', function (e) {
                const val = e.target.value;
                cardPreviewName.textContent = val.trim() ? val.toUpperCase() : 'TU NOMBRE AQUÍ';
            });

            // Enfoques de Inputs -> Resaltar Campos en la Tarjeta
            cardNameInput.addEventListener('focus', () => {
                cardPreviewName.className = 'font-bold uppercase truncate tracking-wider text-slate-100 highlight-active';
            });
            cardNameInput.addEventListener('blur', () => {
                cardPreviewName.className = 'font-bold uppercase truncate tracking-wider text-slate-100 highlight-idle';
            });

            // Tarjeta Número
            cardNumberInput.addEventListener('input', function (e) {
                let val = e.target.value.replace(/\D/g, '');
                if (val.length > 16) {
                    val = val.substring(0, 16);
                }
                
                const groups = val.match(/.{1,4}/g);
                const formatted = groups ? groups.join(' ') : '';
                e.target.value = formatted;

                updateCardBrand(formatted);

                // Previsualizar
                let preview = formatted;
                while (preview.length < 19) {
                    const nextDotIndex = preview.length;
                    if (nextDotIndex === 4 || nextDotIndex === 9 || nextDotIndex === 14) {
                        preview += ' ';
                    } else {
                        preview += '•';
                    }
                }
                cardPreviewNumber.textContent = preview;
            });

            cardNumberInput.addEventListener('focus', () => {
                cardPreviewNumber.className = 'text-lg sm:text-xl tracking-widest text-slate-100 font-semibold highlight-active';
            });
            cardNumberInput.addEventListener('blur', () => {
                cardPreviewNumber.className = 'text-lg sm:text-xl tracking-widest text-slate-100 font-semibold highlight-idle';
            });

            // Tarjeta Expiración
            cardExpiryInput.addEventListener('input', function (e) {
                let val = e.target.value.replace(/\D/g, '');
                if (val.length > 4) {
                    val = val.substring(0, 4);
                }
                
                if (val.length > 2) {
                    val = val.substring(0, 2) + '/' + val.substring(2);
                }
                
                e.target.value = val;

                // Previsualizar
                let preview = val;
                if (preview.length === 0) {
                    cardPreviewExpiry.textContent = 'MM/AA';
                } else if (preview.length === 1) {
                    cardPreviewExpiry.textContent = preview + 'M/AA';
                } else if (preview.length === 2) {
                    cardPreviewExpiry.textContent = preview + '/AA';
                } else if (preview.length === 4) {
                    cardPreviewExpiry.textContent = preview + 'A';
                } else {
                    cardPreviewExpiry.textContent = preview;
                }
            });

            cardExpiryInput.addEventListener('focus', () => {
                cardPreviewExpiry.className = 'font-bold text-slate-100 highlight-active';
            });
            cardExpiryInput.addEventListener('blur', () => {
                cardPreviewExpiry.className = 'font-bold text-slate-100 highlight-idle';
            });

            // CVV hace rotar o inclinar la tarjeta ligeramente como efecto de diseño interactivo
            cardCvvInput.addEventListener('focus', () => {
                visualCard.style.transform = 'perspective(1000px) rotateY(15deg) scale(1.02)';
                visualCard.style.boxShadow = '0 25px 50px -12px rgba(99, 102, 241, 0.25)';
            });
            cardCvvInput.addEventListener('blur', () => {
                visualCard.style.transform = 'perspective(1000px) rotateY(0deg) scale(1)';
                visualCard.style.boxShadow = '';
            });
        });
    </script>
</x-app-layout>
