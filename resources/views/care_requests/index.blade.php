<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <h2 class="font-bold text-2xl text-brand-900 leading-tight">
                {{ __('Mis Peticiones de Cuidado') }}
            </h2>
            <div class="flex items-center space-x-3">
                <a href="{{ route('care-requests.history') }}" class="inline-flex items-center px-4 py-2 bg-white hover:bg-brand-50/50 text-brand-700 border border-brand-200 rounded-2xl font-bold text-xs uppercase tracking-widest transition duration-150 shadow-sm">
                    <svg class="w-4 h-4 mr-2 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    {{ __('Historial') }}
                </a>
                <a href="{{ route('care-requests.create') }}" class="inline-flex items-center px-4 py-2 bg-brand-600 hover:bg-brand-700 text-white border border-transparent rounded-2xl font-bold text-xs uppercase tracking-widest transition duration-150 shadow-sm hover:scale-[1.02] active:scale-[0.98]">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    {{ __('Nueva Petición') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12" x-data="{ activeTab: 'my-requests' }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
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

            <!-- Tabs Navigation -->
            <div class="flex border border-brand-100/50 mb-6 bg-white p-1.5 rounded-2xl shadow-sm max-w-md">
                <button 
                    @click="activeTab = 'my-requests'"
                    :class="activeTab === 'my-requests' ? 'bg-brand-50 text-brand-700 shadow-inner-sm' : 'text-accent-600 hover:text-brand-600 hover:bg-brand-50/20'"
                    class="flex-1 inline-flex items-center justify-center py-2 px-4 font-bold text-sm transition-all duration-200 rounded-xl">
                    <span class="mr-2">📤</span>
                    {{ __('Creadas') }}
                    <span class="ml-2 bg-brand-100 text-brand-800 py-0.5 px-2 rounded-full text-xs font-black">
                        {{ $myRequests->count() }}
                    </span>
                </button>
                <button 
                    @click="activeTab = 'accepted-requests'"
                    :class="activeTab === 'accepted-requests' ? 'bg-brand-50 text-brand-700 shadow-inner-sm' : 'text-accent-600 hover:text-brand-600 hover:bg-brand-50/20'"
                    class="flex-1 inline-flex items-center justify-center py-2 px-4 font-bold text-sm transition-all duration-200 rounded-xl">
                    <span class="mr-2">🤝</span>
                    {{ __('Aceptadas') }}
                    <span class="ml-2 bg-brand-200 text-brand-900 py-0.5 px-2 rounded-full text-xs font-black">
                        {{ $acceptedRequests->count() }}
                    </span>
                </button>
            </div>

            <!-- Tab Content: My Requests -->
            <div x-show="activeTab === 'my-requests'" x-transition:enter="transition ease-out duration-350" class="space-y-6">
                <div class="bg-white overflow-hidden shadow-sm border border-brand-100/50 rounded-3xl">
                    <div class="p-6 text-gray-900">
                        @if($myRequests->isEmpty())
                            <div class="text-center py-12 max-w-sm mx-auto space-y-4">
                                <span class="text-5xl block">📋</span>
                                <h3 class="text-lg font-bold text-brand-900">{{ __('No has publicado ninguna petición activa.') }}</h3>
                                <p class="text-accent-600 text-sm font-medium leading-relaxed">{{ __('Publica una petición de cuidado para que otros cuidadores de la comunidad se ofrezcan.') }}</p>
                                <div class="pt-2">
                                    <a href="{{ route('care-requests.create') }}" class="inline-flex items-center px-5 py-2.5 bg-brand-600 hover:bg-brand-700 text-white font-bold text-sm rounded-2xl shadow-sm transition">
                                        {{ __('Crear Petición') }}
                                    </a>
                                </div>
                            </div>
                        @else
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-brand-50">
                                    <thead>
                                        <tr class="bg-accent-50/50">
                                            <th class="px-6 py-4 text-left text-xs font-bold text-accent-600 uppercase tracking-wider">Perros</th>
                                            <th class="px-6 py-4 text-left text-xs font-bold text-accent-600 uppercase tracking-wider">Fechas</th>
                                            <th class="px-6 py-4 text-left text-xs font-bold text-accent-600 uppercase tracking-wider">Presupuesto</th>
                                            <th class="px-6 py-4 text-left text-xs font-bold text-accent-600 uppercase tracking-wider">Estado</th>
                                            <th class="px-6 py-4 text-left text-xs font-bold text-accent-600 uppercase tracking-wider">Cuidador</th>
                                            <th class="px-6 py-4 text-left text-xs font-bold text-accent-600 uppercase tracking-wider">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-brand-50/50">
                                        @foreach($myRequests as $request)
                                            <tr class="hover:bg-accent-50/30 transition-colors">
                                                <td class="px-6 py-4">
                                                    <div class="flex flex-wrap gap-1.5">
                                                        @foreach($request->dogs as $dog)
                                                            <span class="inline-flex items-center px-2.5 py-0.5 bg-brand-50 border border-brand-100 text-brand-700 text-xs font-bold rounded-full uppercase">
                                                                🐾 {{ $dog->name }}
                                                            </span>
                                                        @endforeach
                                                    </div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="text-sm font-bold text-brand-900">{{ \Carbon\Carbon::parse($request->start_date)->format('d/m/Y') }}</div>
                                                    <div class="text-xs text-accent-600">hasta {{ \Carbon\Carbon::parse($request->end_date)->format('d/m/Y') }}</div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="text-sm font-black text-brand-600">{{ number_format($request->price, 0) }}€</div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <span class="px-2.5 py-1 inline-flex text-xs leading-5 font-bold rounded-full uppercase tracking-wider
                                                        {{ $request->status === 'pending' ? 'bg-amber-50 text-amber-700 border border-amber-200/50' : 'bg-brand-50 text-brand-800 border border-brand-100/50' }}">
                                                        {{ $request->getStatusLabel() }}
                                                    </span>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-accent-600">
                                                    @if($request->acceptedBy)
                                                        <div class="flex items-center space-x-2">
                                                            <img src="{{ $request->acceptedBy->avatar_url }}" alt="{{ $request->acceptedBy->name }}" class="w-6 h-6 rounded-full object-cover border border-brand-100 shadow-sm">
                                                            <span class="font-bold text-brand-900">{{ $request->acceptedBy->name }}</span>
                                                        </div>
                                                    @else
                                                        <span class="text-accent-400 italic text-xs">{{ __('Nadie aún') }}</span>
                                                    @endif
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm font-bold space-x-3">
                                                    <a href="{{ route('care-requests.show', $request) }}" class="text-brand-600 hover:text-brand-800 hover:underline">{{ __('Ver Detalles') }}</a>
                                                    @if($request->status === 'pending')
                                                        <form action="{{ route('care-requests.destroy', $request) }}" method="POST" class="inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="text-rose-600 hover:text-rose-800 hover:underline font-bold" onclick="return confirm('¿Estás seguro de que deseas eliminar esta petición?')">{{ __('Eliminar') }}</button>
                                                        </form>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Tab Content: Accepted Requests -->
            <div x-show="activeTab === 'accepted-requests'" x-transition:enter="transition ease-out duration-350" class="space-y-6" style="display: none;">
                <div class="bg-white overflow-hidden shadow-sm border border-brand-100/50 rounded-3xl">
                    <div class="p-6 text-gray-900">
                        @if($acceptedRequests->isEmpty())
                            <div class="text-center py-12 max-w-sm mx-auto space-y-4">
                                <span class="text-5xl block">🤝</span>
                                <h3 class="text-lg font-bold text-brand-900">{{ __('No has aceptado peticiones de cuidado.') }}</h3>
                                <p class="text-accent-600 text-sm font-medium leading-relaxed">{{ __('Ve a explorar peticiones de otros dueños y ofrécete como cuidador de confianza.') }}</p>
                                <div class="pt-2">
                                    <a href="{{ route('care-requests.explore') }}" class="inline-flex items-center px-5 py-2.5 bg-brand-200 hover:bg-brand-500 text-brand-900 hover:text-white font-bold text-sm rounded-2xl shadow-sm transition">
                                        {{ __('Explorar Peticiones') }}
                                    </a>
                                </div>
                            </div>
                        @else
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-brand-50">
                                    <thead>
                                        <tr class="bg-accent-50/50">
                                            <th class="px-6 py-4 text-left text-xs font-bold text-accent-600 uppercase tracking-wider">Dueño</th>
                                            <th class="px-6 py-4 text-left text-xs font-bold text-accent-600 uppercase tracking-wider">Perros a Cuidar</th>
                                            <th class="px-6 py-4 text-left text-xs font-bold text-accent-600 uppercase tracking-wider">Fechas</th>
                                            <th class="px-6 py-4 text-left text-xs font-bold text-accent-600 uppercase tracking-wider">Precio</th>
                                            <th class="px-6 py-4 text-left text-xs font-bold text-accent-600 uppercase tracking-wider">Estado</th>
                                            <th class="px-6 py-4 text-left text-xs font-bold text-accent-600 uppercase tracking-wider">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-brand-50/50">
                                        @foreach($acceptedRequests as $request)
                                            <tr class="hover:bg-accent-50/30 transition-colors">
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="flex items-center space-x-2">
                                                        <img src="{{ $request->user->avatar_url }}" alt="{{ $request->user->name }}" class="w-8 h-8 rounded-full object-cover border border-brand-100 shadow-sm">
                                                        <div>
                                                            <div class="text-sm font-bold text-brand-900 leading-tight">{{ $request->user->name }}</div>
                                                            <div class="text-xs text-accent-500 font-semibold">{{ $request->user->email }}</div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="px-6 py-4">
                                                    <div class="flex flex-wrap gap-1.5">
                                                        @foreach($request->dogs as $dog)
                                                            <span class="inline-flex items-center px-2.5 py-0.5 bg-brand-50 border border-brand-100 text-brand-700 text-xs font-bold rounded-full uppercase">
                                                                🐾 {{ $dog->name }}
                                                            </span>
                                                        @endforeach
                                                    </div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="text-sm font-bold text-brand-900 leading-tight">{{ \Carbon\Carbon::parse($request->start_date)->format('d/m/Y') }}</div>
                                                    <div class="text-xs text-accent-500 font-semibold">hasta {{ \Carbon\Carbon::parse($request->end_date)->format('d/m/Y') }}</div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="text-sm font-black text-brand-600">{{ number_format($request->price, 0) }}€</div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <span class="px-2.5 py-1 inline-flex text-xs leading-5 font-bold rounded-full bg-brand-50 text-brand-800 border border-brand-100/50 uppercase tracking-wider">
                                                        {{ $request->getStatusLabel() }}
                                                    </span>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm font-bold">
                                                    <a href="{{ route('care-requests.show', $request) }}" class="text-brand-600 hover:text-brand-800 hover:underline">{{ __('Ver Detalles') }}</a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
