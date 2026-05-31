<x-app-layout>
    <style>
        .tab-btn.active {
            color: #3b6d5d;
            border-bottom-color: #3b6d5d;
            font-weight: 800;
        }
    </style>

    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-bold text-2xl text-brand-900 leading-tight tracking-tight">
                {{ __('Mis Reseñas y Valoraciones') }}
            </h2>
            <span class="text-xs font-bold text-brand-700 bg-brand-50 px-3 py-1.5 rounded-full border border-brand-100 uppercase tracking-wider">
                ⭐ {{ auth()->user()->average_rating ?: '0.0' }} ({{ auth()->user()->reviews_count }} {{ auth()->user()->reviews_count === 1 ? __('reseña') : __('reseñas') }})
            </span>
        </div>
    </x-slot>

    <div class="py-12 bg-gradient-to-b from-accent-50 via-brand-50/10 to-accent-100 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            
            @if(session('success'))
                <div class="bg-brand-50 border border-brand-200 text-brand-900 px-4 py-3 rounded-2xl relative flex items-center shadow-sm" role="alert">
                    <span class="mr-2">✨</span>
                    <span class="font-bold text-sm">{{ session('success') }}</span>
                </div>
            @endif

            <!-- 1. Sección de Resumen General y Gráfico de Estrellas -->
            <div class="grid grid-cols-1 md:grid-cols-12 gap-8 items-stretch">
                
                <!-- Card del Promedio -->
                <div class="md:col-span-4 bg-white rounded-3xl p-6 border border-brand-100/50 shadow-md flex flex-col justify-center items-center text-center relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-24 h-24 bg-brand-500/5 rounded-full blur-xl -mr-6 -mt-6"></div>
                    
                    <h3 class="text-xs uppercase text-accent-500 font-black tracking-widest mb-4">{{ __('Tu Calificación') }}</h3>
                    
                    <div class="w-24 h-24 bg-brand-50 text-brand-700 rounded-full flex items-center justify-center text-4xl font-black border border-brand-100 shadow-inner mb-3">
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

                    <p class="text-xs font-bold text-accent-600">
                        {{ __('Basado en') }} {{ $totalReceivedCount }} {{ $totalReceivedCount === 1 ? __('valoración recibida') : __('valoraciones recibidas') }}
                    </p>
                </div>

                <!-- Gráfico de distribución de estrellas -->
                <div class="md:col-span-8 bg-white rounded-3xl p-6 border border-brand-100/50 shadow-md flex flex-col justify-between">
                    <h3 class="text-xs uppercase text-accent-500 font-black tracking-widest border-b border-brand-50 pb-3 mb-4">{{ __('Distribución de Valoraciones') }}</h3>
                    
                    <div class="space-y-3">
                        @for($star = 5; $star >= 1; $star--)
                            @php
                                $count = $starsDistribution[$star];
                                $percent = $totalReceivedCount > 0 ? ($count / $totalReceivedCount) * 100 : 0;
                            @endphp
                            <div class="flex items-center space-x-3 text-xs font-bold text-accent-700">
                                <span class="w-16 text-right">{{ $star }} {{ $star === 1 ? __('estrella') : __('estrellas') }}</span>
                                <div class="flex-grow bg-accent-100 h-2.5 rounded-full overflow-hidden border border-accent-100/30">
                                    <div class="bg-amber-400 h-full rounded-full transition-all duration-500" style="width: {{ $percent }}%"></div>
                                </div>
                                <span class="w-8 text-accent-500">{{ $count }}</span>
                            </div>
                        @endfor
                    </div>
                </div>

            </div>

            @if($pendingToReview->isNotEmpty())
                <!-- Sección: Cuidado de Mascotas Recientes por Valorar -->
                <div class="bg-gradient-to-br from-brand-950 via-brand-900 to-brand-800 text-white rounded-3xl p-6 sm:p-8 shadow-xl relative overflow-hidden">
                    <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_right,rgba(255,255,255,0.06),transparent_55%)] pointer-events-none"></div>
                    <div class="absolute top-0 right-0 w-36 h-36 bg-brand-500/10 rounded-full blur-3xl -mr-8 -mt-8 pointer-events-none"></div>

                    <div class="relative z-10 space-y-6">
                        <div class="border-b border-white/10 pb-4">
                            <h3 class="font-black text-lg tracking-tight uppercase flex items-center">
                                <span class="mr-2.5">✨</span>{{ __('Cuidados recientes por valorar') }}
                            </h3>
                            <p class="text-brand-100 text-xs mt-1 font-semibold leading-relaxed">{{ __('Valora a los usuarios con los que has trabajado recientemente para mejorar la comunidad GoPET.') }}</p>
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
                                                        <span class="text-[9px] uppercase tracking-widest text-brand-200 font-bold block">
                                                            {{ $isOwner ? __('Tu Cuidador:') : __('El Dueño:') }}
                                                        </span>
                                                        <h4 class="font-bold text-slate-100 tracking-tight text-sm leading-tight">{{ $otherUser->name }}</h4>
                                                    </div>
                                                </div>
                                                <span class="text-[9px] font-bold bg-white/20 text-brand-100 border border-white/10 px-2 py-0.5 rounded-full uppercase tracking-wider">
                                                    {{ $isOwner ? __('Dueño') : __('Cuidador') }}
                                                </span>
                                            </div>

                                            <div class="mt-4 space-y-1.5 text-xs text-brand-100 font-semibold leading-relaxed">
                                                <div class="flex items-center">
                                                    <span class="mr-2">📅</span>
                                                    <span>{{ \Carbon\Carbon::parse($request->start_date)->format('d/m/Y') }} {{ __('al') }} {{ \Carbon\Carbon::parse($request->end_date)->format('d/m/Y') }}</span>
                                                </div>
                                                <div class="flex items-center">
                                                    <span class="mr-2">🐾</span>
                                                    <div class="flex flex-wrap gap-1">
                                                        @foreach($request->dogs as $dog)
                                                            <span class="px-2 py-0.5 bg-white/10 text-white rounded-full text-[9px] font-bold tracking-wider uppercase">
                                                                {{ $dog->name }}
                                                            </span>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="mt-5">
                                            <button type="button" onclick="toggleReviewForm({{ $request->id }})" class="w-full inline-flex items-center justify-center px-4 py-2.5 bg-white text-brand-850 font-bold text-xs uppercase tracking-wider rounded-xl transition duration-200 shadow-lg hover:bg-brand-50">
                                                {{ __('Escribir Reseña') }}
                                            </button>
                                        </div>

                                        <!-- Formulario de Reseña Plegable -->
                                        <div id="review-form-container-{{ $request->id }}" class="hidden mt-5 pt-5 border-t border-white/10">
                                            <form action="{{ route('reviews.store', $request) }}" method="POST" class="space-y-4">
                                                @csrf
                                                
                                                <div>
                                                    <label class="block text-[10px] font-black text-brand-200 uppercase tracking-widest mb-1.5">{{ __('Calificación') }}</label>
                                                    <div class="flex items-center space-x-1.5 star-selector" data-request-id="{{ $request->id }}">
                                                        @for($i = 1; $i <= 5; $i++)
                                                            <button type="button" data-value="{{ $i }}" class="star-btn-{{ $request->id }} text-2xl text-brand-200/50 hover:scale-110 transition duration-150 focus:outline-none">★</button>
                                                        @endfor
                                                    </div>
                                                    <input type="hidden" name="rating" id="rating-input-{{ $request->id }}" required>
                                                </div>

                                                <div>
                                                    <label for="comment-{{ $request->id }}" class="block text-[10px] font-black text-brand-200 uppercase tracking-widest mb-1.5">{{ __('Tu Opinión') }}</label>
                                                    <textarea id="comment-{{ $request->id }}" name="comment" rows="3" required minlength="5" maxlength="1000"
                                                        class="block w-full bg-black/20 border-brand-200/30 text-white placeholder-brand-200/40 focus:border-brand-300 focus:ring-0 text-xs font-semibold rounded-2xl py-2 px-3 transition"
                                                        placeholder="Cuéntanos qué tal fue el cuidado y tu trato con este usuario..."></textarea>
                                                </div>

                                                <div class="flex space-x-3">
                                                    <button type="submit" class="flex-grow inline-flex items-center justify-center px-4 py-2.5 bg-brand-500 hover:bg-brand-600 text-white font-bold text-xs uppercase tracking-wider rounded-xl transition duration-200 shadow-sm border border-brand-400">
                                                        {{ __('Guardar Reseña') }}
                                                    </button>
                                                    <button type="button" onclick="toggleReviewForm({{ $request->id }})" class="px-4 py-2.5 bg-white/10 hover:bg-white/20 text-brand-100 font-bold text-xs uppercase tracking-wider rounded-xl transition duration-200">
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
            <div class="bg-white rounded-3xl border border-brand-100/50 shadow-md overflow-hidden">
                
                <!-- Botones de las Pestañas -->
                <div class="flex flex-wrap border-b border-brand-50 bg-accent-50/50">
                    <button class="tab-btn active px-6 py-4 text-xs uppercase tracking-widest text-accent-500 font-bold border-b-2 border-transparent hover:text-brand-600 focus:outline-none transition-all duration-200" onclick="switchTab('received-caretaker', this)">
                        {{ __('Como Cuidador') }} ({{ $receivedAsCaretaker->count() }})
                    </button>
                    <button class="tab-btn px-6 py-4 text-xs uppercase tracking-widest text-accent-500 font-bold border-b-2 border-transparent hover:text-brand-600 focus:outline-none transition-all duration-200" onclick="switchTab('received-owner', this)">
                        {{ __('Como Dueño') }} ({{ $receivedAsOwner->count() }})
                    </button>
                    <button class="tab-btn px-6 py-4 text-xs uppercase tracking-widest text-accent-500 font-bold border-b-2 border-transparent hover:text-indigo-650 hover:text-brand-600 focus:outline-none transition-all duration-200" onclick="switchTab('written', this)">
                        {{ __('Escritas por mí') }} ({{ $writtenReviews->count() }})
                    </button>
                </div>

                <div class="p-6 sm:p-8">
                    
                    <!-- Contenido Pestaña 1: Recibidas como Cuidador -->
                    <div id="tab-received-caretaker" class="tab-content block space-y-6">
                        @if($receivedAsCaretaker->isEmpty())
                            <div class="text-center py-12 text-accent-400 space-y-3">
                                <span class="text-5xl block">🤝</span>
                                <p class="text-sm font-bold text-brand-900">{{ __('No has recibido reseñas como cuidador todavía.') }}</p>
                                <p class="text-xs text-accent-600 font-medium">{{ __('Acepta peticiones de cuidado y completa el servicio para ganar estrellas.') }}</p>
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
                            <div class="text-center py-12 text-accent-400 space-y-3">
                                <span class="text-5xl block">🏡</span>
                                <p class="text-sm font-bold text-brand-900">{{ __('No has recibido reseñas como dueño todavía.') }}</p>
                                <p class="text-xs text-accent-600 font-medium">{{ __('Tus cuidadores podrán valorarte cuando finalicen los cuidados de tus mascotas.') }}</p>
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
                            <div class="text-center py-12 text-accent-400 space-y-3">
                                <span class="text-5xl block">✍️</span>
                                <p class="text-sm font-bold text-brand-900">{{ __('No has escrito ninguna reseña todavía.') }}</p>
                                <p class="text-xs text-accent-600 font-medium">{{ __('Puedes escribir reseñas sobre los usuarios con los que has trabajado en la sección de Peticiones Finalizadas.') }}</p>
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
                    star.classList.remove('text-brand-200/50');
                    star.classList.add('text-amber-400');
                } else {
                    star.classList.remove('text-amber-400');
                    star.classList.add('text-brand-200/50');
                }
            });
        }

        function updateStarsForRequest(requestId, val) {
            const stars = document.querySelectorAll('.star-btn-' + requestId);
            stars.forEach(star => {
                const starVal = parseInt(star.getAttribute('data-value'));
                if (starVal <= val) {
                    star.classList.remove('text-brand-200/50', 'text-amber-400');
                    star.classList.add('text-amber-500');
                } else {
                    star.classList.remove('text-amber-500', 'text-amber-400');
                    star.classList.add('text-brand-200/50');
                }
            });
        }
    </script>
</x-app-layout>
