<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Mis Favoritos') }}
            </h2>
            <a href="{{ route('care-requests.explore') }}" class="inline-flex items-center text-sm font-semibold text-indigo-600 hover:text-indigo-800 transition-colors">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                {{ __('Volver a Explorar') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-lg relative mb-6 flex items-center shadow-sm" role="alert">
                    <svg class="w-5 h-5 mr-2 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span class="block sm:inline font-medium text-sm">{{ session('success') }}</span>
                </div>
            @endif

            <div class="mb-6">
                <p class="text-sm text-gray-500">
                    {{ __('Aquí se muestran las peticiones de cuidado activas que has marcado con un corazón para seguir de cerca o contactar más tarde.') }}
                </p>
            </div>

            @if($favorites->isEmpty())
                <div class="text-center py-16 bg-white border border-dashed border-gray-200 rounded-lg shadow-sm">
                    <span class="text-6xl mb-4 block">❤️</span>
                    <h3 class="text-lg font-bold text-gray-900 mt-2">{{ __('No tienes favoritos todavía') }}</h3>
                    <p class="text-gray-500 mt-1 max-w-md mx-auto">{{ __('Explora las peticiones de cuidado disponibles y haz clic en el corazón para guardarlas aquí.') }}</p>
                    <div class="mt-6">
                        <a href="{{ route('care-requests.explore') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-sm shadow-sm transition">
                            {{ __('Explorar Peticiones') }}
                        </a>
                    </div>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($favorites as $request)
                        <div class="bg-white overflow-hidden shadow-sm border border-gray-100 hover:shadow-md transition-shadow duration-300 flex flex-col">
                            <!-- Header de la Card -->
                            <div class="p-5 border-b border-gray-50 bg-gray-50/50">
                                <div class="flex justify-between items-start">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-10 h-10 bg-indigo-100 flex items-center justify-center text-indigo-700 font-bold border border-indigo-200">
                                            {{ substr($request->user->name, 0, 1) }}
                                        </div>
                                        <div>
                                            <p class="text-sm font-bold text-gray-900">{{ $request->user->name }}</p>
                                            <p class="text-xs text-gray-500">Publicado {{ $request->created_at->diffForHumans() }}</p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <span class="text-lg font-black text-indigo-600">{{ number_format($request->price, 0) }}€</span>
                                        <p class="text-[10px] text-gray-400 uppercase font-bold tracking-wider">Presupuesto</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Cuerpo de la Card -->
                            <div class="p-5 flex-grow">
                                <h4 class="text-xs font-bold text-gray-400 uppercase mb-3 tracking-widest">Perros a cuidar</h4>
                                <div class="flex flex-wrap gap-2 mb-4">
                                    @foreach($request->dogs as $dog)
                                        <div class="flex items-center bg-indigo-50 border border-indigo-100 pr-2 overflow-hidden">
                                            @if($dog->photo)
                                                <img src="{{ asset('storage/' . $dog->photo) }}" class="w-6 h-6 aspect-square object-cover mr-2">
                                            @else
                                                <span class="text-xs ml-2 mr-1">🐾</span>
                                            @endif
                                            <span class="text-[10px] font-bold text-indigo-700 uppercase">{{ $dog->name }}</span>
                                        </div>
                                    @endforeach
                                </div>

                                <div class="space-y-3">
                                    <div class="flex items-center text-sm text-gray-600">
                                        <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        <span>{{ \Carbon\Carbon::parse($request->start_date)->format('d M') }} - {{ \Carbon\Carbon::parse($request->end_date)->format('d M') }}</span>
                                    </div>
                                    
                                    @if($request->description)
                                        <p class="text-sm text-gray-600 line-clamp-2 italic">
                                            "{{ $request->description }}"
                                        </p>
                                    @endif
                                </div>
                            </div>

                            <!-- Footer / Acciones -->
                            <div class="p-5 bg-gray-50/30 border-t border-gray-50 flex items-center gap-3">
                                <a href="{{ route('care-requests.show', $request) }}" class="flex-grow text-center py-2 bg-white border border-indigo-200 text-indigo-700 font-bold text-sm hover:bg-indigo-600 hover:text-white hover:border-indigo-600 transition-all duration-200 shadow-sm" style="border-width: 2px; border-style: solid;">
                                    {{ __('Ver Detalles') }}
                                </a>
                                <form action="{{ route('care-requests.favorite', $request) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="p-2 bg-white border-2 border-gray-200 hover:border-rose-400 hover:bg-rose-50 text-gray-400 hover:text-rose-600 transition-colors shadow-sm focus:outline-none flex items-center justify-center" title="Quitar de favoritos">
                                        <svg class="w-5 h-5 text-rose-600 fill-current" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
