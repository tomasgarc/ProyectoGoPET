<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-brand-900 leading-tight">
            {{ __('Peticiones de Cuidado Disponibles') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="bg-brand-50 border border-brand-200 text-brand-900 px-4 py-3 rounded-2xl relative mb-6 flex items-center shadow-sm" role="alert">
                    <span class="mr-2">✨</span>
                    <span class="font-bold text-sm">{{ session('success') }}</span>
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($careRequests as $request)
                    <div class="bg-white overflow-hidden shadow-sm border border-brand-100/50 rounded-3xl hover:shadow-xl hover:shadow-brand-100/10 hover:border-brand-200/50 transition-all duration-300 flex flex-col justify-between group">
                        
                        <!-- Header de la Card -->
                        <div class="p-5 border-b border-brand-50 bg-accent-50/20">
                            <div class="flex justify-between items-start">
                                <div class="flex items-center space-x-3">
                                    <img src="{{ $request->user->avatar_url }}" alt="{{ $request->user->name }}" class="w-10 h-10 rounded-full object-cover border border-brand-100 shadow-sm">
                                    <div>
                                        <p class="text-sm font-bold text-brand-900 leading-tight">{{ $request->user->name }}</p>
                                        <p class="text-[10px] text-accent-500 font-semibold mt-0.5">{{ $request->created_at->diffForHumans() }}</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <span class="text-xl font-black text-brand-600 block leading-none">{{ number_format($request->price, 0) }}€</span>
                                    <p class="text-[9px] text-accent-500 uppercase font-black tracking-widest mt-1">Presupuesto</p>
                                </div>
                            </div>
                        </div>

                        <!-- Cuerpo de la Card -->
                        <div class="p-5 flex-grow space-y-4">
                            <div>
                                <h4 class="text-[10px] font-black text-accent-500 uppercase tracking-widest mb-2">Perros a cuidar</h4>
                                <div class="flex flex-wrap gap-2">
                                    @foreach($request->dogs as $dog)
                                        <div class="flex items-center bg-brand-50/50 border border-brand-100/50 pr-2.5 overflow-hidden rounded-full py-0.5 pl-0.5">
                                            @if($dog->photo)
                                                <img src="{{ asset('storage/' . $dog->photo) }}" class="w-5 h-5 aspect-square rounded-full object-cover mr-1.5 shadow-sm">
                                            @else
                                                <span class="text-[10px] ml-2 mr-1">🐾</span>
                                            @endif
                                            <span class="text-[10px] font-bold text-brand-700 uppercase tracking-wide">{{ $dog->name }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <div class="space-y-3 pt-2 border-t border-brand-50/50">
                                <div class="flex items-center text-sm text-accent-700 font-semibold">
                                    <svg class="w-4 h-4 mr-2 text-brand-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    <span>{{ \Carbon\Carbon::parse($request->start_date)->format('d M') }} - {{ \Carbon\Carbon::parse($request->end_date)->format('d M') }}</span>
                                </div>
                                
                                @if($request->description)
                                    <p class="text-xs text-accent-600 font-medium line-clamp-2 italic leading-relaxed bg-accent-50/30 p-2.5 rounded-xl border border-brand-50/30">
                                        "{{ $request->description }}"
                                    </p>
                                @endif
                            </div>
                        </div>

                        <!-- Footer / Acciones -->
                        <div class="p-5 bg-accent-50/10 border-t border-brand-50 flex items-center gap-3">
                            <a href="{{ route('care-requests.show', $request) }}" class="flex-grow text-center py-2.5 bg-white border border-brand-200 text-brand-700 font-bold text-xs uppercase tracking-widest rounded-2xl hover:bg-brand-600 hover:text-white hover:border-brand-600 transition-all duration-200 hover:scale-[1.02] shadow-sm">
                                {{ __('Ver Detalles') }}
                            </a>
                            <form action="{{ route('care-requests.favorite', $request) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="p-2.5 bg-white border border-brand-200 hover:border-rose-400 hover:bg-rose-50/50 text-accent-400 hover:text-rose-600 transition-all duration-200 shadow-sm focus:outline-none flex items-center justify-center rounded-2xl">
                                    @if($request->isFavoritedBy(auth()->id()))
                                        <svg class="w-5 h-5 text-rose-600 fill-current" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                        </svg>
                                    @else
                                        <svg class="w-5 h-5 text-accent-400 hover:text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                        </svg>
                                    @endif
                                </button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full py-16 text-center bg-white shadow-sm border-2 border-dashed border-brand-200 rounded-3xl space-y-4 max-w-md mx-auto">
                        <span class="text-5xl block">🐕</span>
                        <p class="text-brand-900 font-black text-lg">No hay peticiones de cuidado disponibles</p>
                        <p class="text-sm text-accent-600 font-medium">¡Vuelve a revisar más tarde en busca de nuevas ofertas!</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>
