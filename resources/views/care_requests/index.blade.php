<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Mis Peticiones de Cuidado') }}
            </h2>
            <div class="flex items-center space-x-3">
                <a href="{{ route('care-requests.history') }}" class="inline-flex items-center px-4 py-2 bg-white hover:bg-gray-50 text-gray-700 border border-gray-300 rounded-md font-semibold text-xs uppercase tracking-widest active:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition ease-in-out duration-150 shadow-sm">
                    <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    {{ __('Peticiones Finalizadas') }}
                </a>
                <a href="{{ route('care-requests.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-700 hover:bg-indigo-800 text-white border border-transparent rounded-md font-semibold text-xs uppercase tracking-widest active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-sm" style="background-color: #4338ca; color: white;">
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

            <!-- Tabs Navigation -->
            <div class="flex border-b border-gray-200 mb-6 bg-white p-2 rounded-lg shadow-sm">
                <button 
                    @click="activeTab = 'my-requests'"
                    :class="activeTab === 'my-requests' ? 'border-indigo-600 text-indigo-600 bg-indigo-50/50' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                    class="flex-1 sm:flex-none inline-flex items-center justify-center py-2.5 px-6 font-bold text-sm border-b-2 transition-all duration-200 rounded-md">
                    <span class="mr-2">📤</span>
                    {{ __('Peticiones Creadas') }}
                    <span class="ml-2 bg-indigo-100 text-indigo-700 py-0.5 px-2 rounded-full text-xs font-black">
                        {{ $myRequests->count() }}
                    </span>
                </button>
                <button 
                    @click="activeTab = 'accepted-requests'"
                    :class="activeTab === 'accepted-requests' ? 'border-indigo-600 text-indigo-600 bg-indigo-50/50' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                    class="flex-1 sm:flex-none inline-flex items-center justify-center py-2.5 px-6 font-bold text-sm border-b-2 transition-all duration-200 rounded-md">
                    <span class="mr-2">🤝</span>
                    {{ __('Peticiones Aceptadas') }}
                    <span class="ml-2 bg-emerald-100 text-emerald-700 py-0.5 px-2 rounded-full text-xs font-black">
                        {{ $acceptedRequests->count() }}
                    </span>
                </button>
            </div>

            <!-- Tab Content: My Requests -->
            <div x-show="activeTab === 'my-requests'" x-transition:enter="transition ease-out duration-300" class="space-y-6">
                <div class="bg-white overflow-hidden shadow-sm border border-gray-100 sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        @if($myRequests->isEmpty())
                            <div class="text-center py-12">
                                <span class="text-5xl">📋</span>
                                <h3 class="mt-4 text-lg font-bold text-gray-900">{{ __('No has publicado ninguna petición activa.') }}</h3>
                                <p class="text-gray-500 mt-2 max-w-md mx-auto">{{ __('Publica una petición para que otros usuarios puedan cuidar de tus perros.') }}</p>
                                <div class="mt-6">
                                    <a href="{{ route('care-requests.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-700 hover:bg-indigo-800 text-white font-semibold rounded-md shadow-md transition">
                                        {{ __('Crear Petición') }}
                                    </a>
                                </div>
                            </div>
                        @else
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-150">
                                    <thead>
                                        <tr class="bg-gray-50/50">
                                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Perros</th>
                                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Fechas</th>
                                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Presupuesto</th>
                                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Estado</th>
                                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Cuidador</th>
                                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-100">
                                        @foreach($myRequests as $request)
                                            <tr class="hover:bg-gray-50/30 transition-colors">
                                                <td class="px-6 py-4">
                                                    <div class="flex flex-wrap gap-1.5">
                                                        @foreach($request->dogs as $dog)
                                                            <span class="inline-flex items-center px-2.5 py-0.5 bg-indigo-50 border border-indigo-100 text-indigo-700 text-xs font-bold rounded-sm uppercase">
                                                                🐾 {{ $dog->name }}
                                                            </span>
                                                        @endforeach
                                                    </div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="text-sm font-semibold text-gray-900">{{ \Carbon\Carbon::parse($request->start_date)->format('d/m/Y') }}</div>
                                                    <div class="text-xs text-gray-500">hasta {{ \Carbon\Carbon::parse($request->end_date)->format('d/m/Y') }}</div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="text-sm font-black text-indigo-600">{{ number_format($request->price, 0) }}€</div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <span class="px-2.5 py-1 inline-flex text-xs leading-5 font-black rounded-sm uppercase tracking-wider
                                                        {{ $request->status === 'pending' ? 'bg-amber-100 text-amber-800 border border-amber-200' : 'bg-emerald-100 text-emerald-800 border border-emerald-200' }}">
                                                        {{ $request->getStatusLabel() }}
                                                    </span>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                                    @if($request->acceptedBy)
                                                        <div class="flex items-center space-x-2">
                                                            <img src="{{ $request->acceptedBy->avatar_url }}" alt="{{ $request->acceptedBy->name }}" class="w-6 h-6 rounded-full object-cover border border-gray-250 shadow-sm">
                                                            <span class="font-medium text-gray-900">{{ $request->acceptedBy->name }}</span>
                                                        </div>
                                                    @else
                                                        <span class="text-gray-400 italic text-xs">{{ __('Nadie aún') }}</span>
                                                    @endif
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm font-bold space-x-3">
                                                    <a href="{{ route('care-requests.show', $request) }}" class="text-indigo-600 hover:text-indigo-900 underline">{{ __('Ver Detalles') }}</a>
                                                    @if($request->status === 'pending')
                                                        <form action="{{ route('care-requests.destroy', $request) }}" method="POST" class="inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="text-rose-600 hover:text-rose-900 underline font-bold" onclick="return confirm('¿Estás seguro de que deseas eliminar esta petición?')">{{ __('Eliminar') }}</button>
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
            <div x-show="activeTab === 'accepted-requests'" x-transition:enter="transition ease-out duration-300" class="space-y-6" style="display: none;">
                <div class="bg-white overflow-hidden shadow-sm border border-gray-100 sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        @if($acceptedRequests->isEmpty())
                            <div class="text-center py-12">
                                <span class="text-5xl">🤝</span>
                                <h3 class="mt-4 text-lg font-bold text-gray-900">{{ __('No has aceptado ninguna petición de cuidado activa.') }}</h3>
                                <p class="text-gray-500 mt-2 max-w-md mx-auto">{{ __('Ve a la sección explorar peticiones para ofrecerte como cuidador a otros dueños.') }}</p>
                                <div class="mt-6">
                                    <a href="{{ route('care-requests.explore') }}" class="inline-flex items-center px-4 py-2 bg-indigo-700 hover:bg-indigo-800 text-white font-semibold rounded-md shadow-md transition">
                                        {{ __('Explorar Peticiones') }}
                                    </a>
                                </div>
                            </div>
                        @else
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-150">
                                    <thead>
                                        <tr class="bg-gray-50/50">
                                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Dueño</th>
                                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Perros a Cuidar</th>
                                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Fechas</th>
                                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Precio</th>
                                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Estado</th>
                                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-100">
                                        @foreach($acceptedRequests as $request)
                                            <tr class="hover:bg-gray-50/30 transition-colors">
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="flex items-center space-x-2">
                                                        <img src="{{ $request->user->avatar_url }}" alt="{{ $request->user->name }}" class="w-8 h-8 rounded-full object-cover border border-gray-250 shadow-sm">
                                                        <div>
                                                            <div class="text-sm font-semibold text-gray-900">{{ $request->user->name }}</div>
                                                            <div class="text-xs text-gray-500">{{ $request->user->email }}</div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="px-6 py-4">
                                                    <div class="flex flex-wrap gap-1.5">
                                                        @foreach($request->dogs as $dog)
                                                            <span class="inline-flex items-center px-2.5 py-0.5 bg-indigo-50 border border-indigo-100 text-indigo-700 text-xs font-bold rounded-sm uppercase">
                                                                🐾 {{ $dog->name }}
                                                            </span>
                                                        @endforeach
                                                    </div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="text-sm font-semibold text-gray-900">{{ \Carbon\Carbon::parse($request->start_date)->format('d/m/Y') }}</div>
                                                    <div class="text-xs text-gray-500">hasta {{ \Carbon\Carbon::parse($request->end_date)->format('d/m/Y') }}</div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="text-sm font-black text-indigo-600">{{ number_format($request->price, 0) }}€</div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <span class="px-2.5 py-1 inline-flex text-xs leading-5 font-black rounded-sm bg-emerald-100 text-emerald-800 border border-emerald-200 uppercase tracking-wider">
                                                        {{ $request->getStatusLabel() }}
                                                    </span>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm font-bold">
                                                    <a href="{{ route('care-requests.show', $request) }}" class="text-indigo-600 hover:text-indigo-900 underline">{{ __('Ver Detalles') }}</a>
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
