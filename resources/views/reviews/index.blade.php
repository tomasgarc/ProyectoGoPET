<x-app-layout>
    <!-- Importar tipografía premium de Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        .font-premium {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
        .tab-btn.active {
            color: #4f46e5;
            border-bottom-color: #4f46e5;
            font-weight: 800;
        }
    </style>

    <x-slot name="header">
        <div class="flex justify-between items-center font-premium">
            <h2 class="font-extrabold text-2xl text-slate-800 leading-tight tracking-tight">
                {{ __('Mis Reseñas y Valoraciones') }}
            </h2>
            <span class="text-xs font-bold text-indigo-600 bg-indigo-50 px-3 py-1 rounded-full border border-indigo-100 uppercase tracking-wider">
                ⭐ {{ auth()->user()->average_rating ?: '0.0' }} ({{ auth()->user()->reviews_count }} {{ auth()->user()->reviews_count === 1 ? __('reseña') : __('reseñas') }})
            </span>
        </div>
    </x-slot>

    <div class="py-12 bg-gradient-to-b from-slate-50 via-indigo-50/10 to-slate-100 min-h-screen font-premium">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            
            @if(session('success'))
                <div class="bg-emerald-50 border border-emerald-250 text-emerald-800 px-5 py-4 rounded-2xl relative flex items-center shadow-sm" role="alert">
                    <svg class="w-5 h-5 mr-2 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span class="block sm:inline font-bold text-sm">{{ session('success') }}</span>
                </div>
            @endif

            <!-- 1. Sección de Resumen General y Gráfico de Estrellas -->
            <div class="grid grid-cols-1 md:grid-cols-12 gap-8 items-stretch">
                
                <!-- Card del Promedio -->
                <div class="md:col-span-4 bg-white rounded-3xl p-6 border border-slate-100 shadow-xl shadow-slate-100/50 flex flex-col justify-center items-center text-center relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-24 h-24 bg-indigo-500/5 rounded-full blur-xl -mr-6 -mt-6"></div>
                    
                    <h3 class="text-xs uppercase text-slate-400 font-extrabold tracking-widest mb-4">{{ __('Tu Calificación') }}</h3>
                    
                    <div class="w-24 h-24 bg-indigo-50/80 text-indigo-650 rounded-full flex items-center justify-center text-4xl font-extrabold border border-indigo-100/50 shadow-inner mb-3">
                        {{ auth()->user()->average_rating ?: '0.0' }}
                    </div>
                    
                    <div class="flex items-center text-amber-500 text-xl mb-1.5">
                        @php
                            $avg = auth()->user()->average_rating ?: 0;
                        @endphp
                        @for($i = 1; $i <= 5; $i++)
                            <span>{{ $i <= round($avg) ? '★' : '☆' }}</span>
                        @endfor
                    </div>

                    <p class="text-xs font-bold text-slate-500">
                        {{ __('Basado en') }} {{ $totalReceivedCount }} {{ $totalReceivedCount === 1 ? __('valoración recibida') : __('valoraciones recibidas') }}
                    </p>
                </div>

                <!-- Gráfico de distribución de estrellas (Estilo Amazon) -->
                <div class="md:col-span-8 bg-white rounded-3xl p-6 border border-slate-100 shadow-xl shadow-slate-100/50 flex flex-col justify-between">
                    <h3 class="text-xs uppercase text-slate-400 font-extrabold tracking-widest border-b border-slate-100 pb-3 mb-4">{{ __('Distribución de Valoraciones') }}</h3>
                    
                    <div class="space-y-3">
                        @for($star = 5; $star >= 1; $star--)
                            @php
                                $count = $starsDistribution[$star];
                                $percent = $totalReceivedCount > 0 ? ($count / $totalReceivedCount) * 100 : 0;
                            @endphp
                            <div class="flex items-center space-x-3 text-xs font-bold text-slate-600">
                                <span class="w-12 text-right">{{ $star }} {{ $star === 1 ? __('estrella') : __('estrellas') }}</span>
                                <div class="flex-grow bg-slate-100 h-2.5 rounded-full overflow-hidden border border-slate-100">
                                    <div class="bg-amber-400 h-full rounded-full transition-all duration-500" style="width: {{ $percent }}%"></div>
                                </div>
                                <span class="w-8 text-slate-450">{{ $count }}</span>
                            </div>
                        @endfor
                    </div>
                </div>

            </div>

            @if($pendingToReview->isNotEmpty())
                <!-- Sección: Cuidado de Mascotas Recientes por Valorar -->
                <div class="bg-gradient-to-r from-indigo-900 to-indigo-950 text-white rounded-3xl p-6 sm:p-8 border border-indigo-950/80 shadow-2xl relative overflow-hidden">
                    <!-- Efecto de brillo de fondo -->
                    <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_right,rgba(255,255,255,0.08),transparent_55%)] pointer-events-none"></div>
                    <div class="absolute top-0 right-0 w-36 h-36 bg-indigo-500/10 rounded-full blur-3xl -mr-8 -mt-8 pointer-events-none"></div>

                    <div class="relative z-10 space-y-6">
                        <div class="border-b border-indigo-800 pb-4">
                            <h3 class="font-extrabold text-xl tracking-tight uppercase flex items-center">
                                <span class="mr-2.5">✨</span>{{ __('Cuidados recientes por valorar') }}
                            </h3>
                            <p class="text-indigo-300 text-xs mt-1 font-medium">{{ __('Valora a los usuarios con los que has trabajado recientemente para mejorar la comunidad GoPET.') }}</p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            @foreach($pendingToReview as $request)
                                @php
                                    $isOwner = auth()->id() === $request->user_id;
                                    $otherUser = $isOwner ? $request->acceptedBy : $request->user;
                                @endphp
                                @if($otherUser)
                                    <div class="bg-white/10 backdrop-blur-md rounded-2xl p-5 border border-white/10 hover:border-white/20 transition-all duration-300 flex flex-col justify-between" id="pending-card-{{ $request->id }}">
                                        <div>
                                            <div class="flex justify-between items-start">
                                                <div class="flex items-center space-x-3">
                                                    <img src="{{ $otherUser->avatar_url }}" alt="{{ $otherUser->name }}" class="w-10 h-10 rounded-full object-cover border border-white/20 shadow-sm">
                                                    <div>
                                                        <span class="text-[9px] uppercase tracking-widest text-indigo-300 font-black block">
                                                            {{ $isOwner ? __('Tu Cuidador:') : __('El Dueño:') }}
                                                        </span>
                                                        <h4 class="font-extrabold text-slate-100 tracking-tight text-sm leading-tight">{{ $otherUser->name }}</h4>
                                                    </div>
                                                </div>
                                                <span class="text-[9px] font-extrabold bg-indigo-500/30 text-indigo-200 border border-indigo-500/40 px-2 py-0.5 rounded uppercase tracking-wider">
                                                    {{ $isOwner ? __('Dueño') : __('Cuidador') }}
                                                </span>
                                            </div>

                                            <div class="mt-4 space-y-1.5 text-xs text-indigo-200 font-semibold leading-relaxed">
                                                <div class="flex items-center">
                                                    <span class="mr-2">📅</span>
                                                    <span>{{ \Carbon\Carbon::parse($request->start_date)->format('d/m/Y') }} {{ __('al') }} {{ \Carbon\Carbon::parse($request->end_date)->format('d/m/Y') }}</span>
                                                </div>
                                                <div class="flex items-center">
                                                    <span class="mr-2">🐾</span>
                                                    <div class="flex flex-wrap gap-1">
                                                        @foreach($request->dogs as $dog)
                                                            <span class="px-1.5 py-0.5 bg-indigo-500/20 text-indigo-150 rounded text-[9px] uppercase font-bold tracking-wider">
                                                                {{ $dog->name }}
                                                            </span>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="mt-5">
                                            <button type="button" onclick="toggleReviewForm({{ $request->id }})" class="w-full inline-flex items-center justify-center px-4 py-2 bg-indigo-600 hover:bg-indigo-550 text-white font-black text-xs uppercase tracking-wider rounded-xl transition duration-200 shadow-lg shadow-indigo-950/20">
                                                {{ __('Escribir Reseña') }}
                                            </button>
                                        </div>

                                        <!-- Formulario de Reseña Plegable -->
                                        <div id="review-form-container-{{ $request->id }}" class="hidden mt-5 pt-5 border-t border-indigo-800/60">
                                            <form action="{{ route('reviews.store', $request) }}" method="POST" class="space-y-4">
                                                @csrf
                                                
                                                <div>
                                                    <label class="block text-[10px] font-extrabold text-indigo-300 uppercase tracking-widest mb-1.5">{{ __('Calificación') }}</label>
                                                    <div class="flex items-center space-x-1.5 star-selector" data-request-id="{{ $request->id }}">
                                                        @for($i = 1; $i <= 5; $i++)
                                                            <button type="button" data-value="{{ $i }}" class="star-btn-{{ $request->id }} text-2xl text-indigo-800 hover:scale-110 transition duration-150 focus:outline-none">★</button>
                                                        @endfor
                                                    </div>
                                                    <input type="hidden" name="rating" id="rating-input-{{ $request->id }}" required>
                                                </div>

                                                <div>
                                                    <label for="comment-{{ $request->id }}" class="block text-[10px] font-extrabold text-indigo-300 uppercase tracking-widest mb-1.5">{{ __('Tu Opinión') }}</label>
                                                    <textarea id="comment-{{ $request->id }}" name="comment" rows="3" required minlength="5" maxlength="1000"
                                                        class="block w-full bg-indigo-950/40 border-indigo-800 text-slate-100 placeholder-indigo-600 focus:border-indigo-500 focus:ring-0 text-xs font-semibold rounded-xl py-2 px-3 transition"
                                                        placeholder="Cuéntanos qué tal fue el cuidado y tu trato con este usuario..."></textarea>
                                                </div>

                                                <div class="flex space-x-3">
                                                    <button type="submit" class="flex-grow inline-flex items-center justify-center px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs uppercase tracking-wider rounded-xl transition duration-200">
                                                        {{ __('Guardar Reseña') }}
                                                    </button>
                                                    <button type="button" onclick="toggleReviewForm({{ $request->id }})" class="px-4 py-2 bg-indigo-900/60 hover:bg-indigo-900/80 text-indigo-200 font-extrabold text-xs uppercase tracking-wider rounded-xl transition duration-200">
                                                        {{ __('Cancelar') }}
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            <!-- 2. Pestañas y Listado de Reseñas -->
            <div class="bg-white rounded-3xl border border-slate-100 shadow-xl shadow-slate-100/80 overflow-hidden">
                
                <!-- Botones de las Pestañas -->
                <div class="flex border-b border-slate-100 bg-slate-50/50">
                    <button class="tab-btn active px-6 py-4 text-xs uppercase tracking-widest text-slate-450 font-bold border-b-2 border-transparent hover:text-indigo-600 focus:outline-none transition-all duration-200" onclick="switchTab('received-caretaker', this)">
                        {{ __('Como Cuidador') }} ({{ $receivedAsCaretaker->count() }})
                    </button>
                    <button class="tab-btn px-6 py-4 text-xs uppercase tracking-widest text-slate-450 font-bold border-b-2 border-transparent hover:text-indigo-600 focus:outline-none transition-all duration-200" onclick="switchTab('received-owner', this)">
                        {{ __('Como Dueño') }} ({{ $receivedAsOwner->count() }})
                    </button>
                    <button class="tab-btn px-6 py-4 text-xs uppercase tracking-widest text-slate-450 font-bold border-b-2 border-transparent hover:text-indigo-600 focus:outline-none transition-all duration-200" onclick="switchTab('written', this)">
                        {{ __('Escritas por mí') }} ({{ $writtenReviews->count() }})
                    </button>
                </div>

                <div class="p-6 sm:p-8">
                    
                    <!-- Contenido Pestaña 1: Recibidas como Cuidador -->
                    <div id="tab-received-caretaker" class="tab-content block space-y-6">
                        @if($receivedAsCaretaker->isEmpty())
                            <div class="text-center py-12 text-slate-400">
                                <span class="text-5xl block mb-3">🤝</span>
                                <p class="text-sm font-bold">{{ __('No has recibido reseñas como cuidador todavía.') }}</p>
                                <p class="text-xs text-slate-450 mt-1">{{ __('Acepta peticiones de cuidado y completa el servicio para ganar estrellas.') }}</p>
                            </div>
                        @else
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                @foreach($receivedAsCaretaker as $review)
                                    <x-review-card :review="$review" :showReviewer="true" />
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <!-- Contenido Pestaña 2: Recibidas como Dueño -->
                    <div id="tab-received-owner" class="tab-content hidden space-y-6">
                        @if($receivedAsOwner->isEmpty())
                            <div class="text-center py-12 text-slate-400">
                                <span class="text-5xl block mb-3">🏡</span>
                                <p class="text-sm font-bold">{{ __('No has recibido reseñas como dueño todavía.') }}</p>
                                <p class="text-xs text-slate-450 mt-1">{{ __('Tus cuidadores podrán valorarte cuando finalicen los cuidados de tus mascotas.') }}</p>
                            </div>
                        @else
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                @foreach($receivedAsOwner as $review)
                                    <x-review-card :review="$review" :showReviewer="true" />
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <!-- Contenido Pestaña 3: Escritas por mí -->
                    <div id="tab-written" class="tab-content hidden space-y-6">
                        @if($writtenReviews->isEmpty())
                            <div class="text-center py-12 text-slate-400">
                                <span class="text-5xl block mb-3">✍️</span>
                                <p class="text-sm font-bold">{{ __('No has escrito ninguna reseña todavía.') }}</p>
                                <p class="text-xs text-slate-450 mt-1">{{ __('Puedes escribir reseñas sobre los usuarios con los que has trabajado en la sección de Peticiones Finalizadas.') }}</p>
                            </div>
                        @else
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                @foreach($writtenReviews as $review)
                                    <x-review-card :review="$review" :showReviewer="false" />
                                @endforeach
                            </div>
                        @endif
                    </div>

                </div>

            </div>

        </div>
    </div>

    <!-- Script en Vanilla JS para pestañas dinámicas fluidas y selector de estrellas -->
    <script>
        function switchTab(tabId, button) {
            // Ocultar todos los contenidos
            document.querySelectorAll('.tab-content').forEach(content => {
                content.classList.add('hidden');
                content.classList.remove('block');
            });
            
            // Quitar active de todos los botones
            document.querySelectorAll('.tab-btn').forEach(btn => {
                btn.classList.remove('active');
            });

            // Mostrar el seleccionado
            const activeContent = document.getElementById('tab-' + tabId);
            if (activeContent) {
                activeContent.classList.remove('hidden');
                activeContent.classList.add('block');
            }
            
            // Poner active al seleccionado
            button.classList.add('active');
        }

        function toggleReviewForm(requestId) {
            const container = document.getElementById('review-form-container-' + requestId);
            if (container.classList.contains('hidden')) {
                container.classList.remove('hidden');
                initializeStarsForRequest(requestId);
            } else {
                container.classList.add('hidden');
            }
        }

        function initializeStarsForRequest(requestId) {
            const stars = document.querySelectorAll('.star-btn-' + requestId);
            const ratingInput = document.getElementById('rating-input-' + requestId);

            if (!stars.length || !ratingInput) return;

            // Remove existing event listeners by replacing stars
            stars.forEach(star => {
                const newStar = star.cloneNode(true);
                star.parentNode.replaceChild(newStar, star);
            });

            const updatedStars = document.querySelectorAll('.star-btn-' + requestId);
            updatedStars.forEach(star => {
                star.addEventListener('click', function () {
                    const value = parseInt(this.getAttribute('data-value'));
                    ratingInput.value = value;
                    updateStarsForRequest(requestId, value);
                });

                star.addEventListener('mouseover', function () {
                    const value = parseInt(this.getAttribute('data-value'));
                    highlightStarsForRequest(requestId, value);
                });

                star.addEventListener('mouseout', function () {
                    const value = parseInt(ratingInput.value) || 0;
                    updateStarsForRequest(requestId, value);
                });
            });
        }

        function highlightStarsForRequest(requestId, val) {
            const stars = document.querySelectorAll('.star-btn-' + requestId);
            stars.forEach(star => {
                const starVal = parseInt(star.getAttribute('data-value'));
                if (starVal <= val) {
                    star.classList.remove('text-indigo-800');
                    star.classList.add('text-amber-400');
                } else {
                    star.classList.remove('text-amber-400');
                    star.classList.add('text-indigo-800');
                }
            });
        }

        function updateStarsForRequest(requestId, val) {
            const stars = document.querySelectorAll('.star-btn-' + requestId);
            stars.forEach(star => {
                const starVal = parseInt(star.getAttribute('data-value'));
                if (starVal <= val) {
                    star.classList.remove('text-indigo-800', 'text-amber-400');
                    star.classList.add('text-amber-500');
                } else {
                    star.classList.remove('text-amber-500', 'text-amber-400');
                    star.classList.add('text-indigo-800');
                }
            });
        }
    </script>
</x-app-layout>
