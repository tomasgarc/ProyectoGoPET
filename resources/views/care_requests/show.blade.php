<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Detalles de la Petición') }}
            </h2>
            <a href="javascript:history.back()" class="text-sm text-gray-600 hover:underline">
                {{ __('&larr; Volver') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-between items-start mb-6">
                        <div>
                            <h3 class="text-lg font-bold text-gray-800">Estado: 
                                <span class="px-2 py-1 text-sm font-semibold 
                                    {{ $careRequest->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : ($careRequest->status === 'accepted' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800') }}">
                                    {{ ucfirst($careRequest->status) }}
                                </span>
                            </h3>
                            <p class="text-sm text-gray-500 mt-1">Publicada el {{ $careRequest->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                        <div class="text-right">
                            <span class="text-2xl font-bold text-indigo-600">{{ number_format($careRequest->price, 2) }}€</span>
                        </div>
                    </div>

                    <div class="mt-8 grid grid-cols-1 md:grid-cols-2 gap-8">
                        <!-- Perros -->
                        <div>
                            <h4 class="font-semibold text-gray-700 border-b pb-2 mb-4">Perros a cuidar</h4>
                            <div class="space-y-4">
                                @foreach($careRequest->dogs as $dog)
                                    <div class="flex items-center space-x-4 p-3 border bg-gray-50">
                                        @if($dog->photo)
                                            <img src="{{ asset('storage/' . $dog->photo) }}" alt="{{ $dog->name }}" class="w-16 h-16 aspect-square object-cover shadow-sm border border-gray-200">
                                        @else
                                            <div class="w-16 h-16 aspect-square bg-indigo-100 flex items-center justify-center text-indigo-600 font-bold border border-indigo-200">
                                                {{ substr($dog->name, 0, 1) }}
                                            </div>
                                        @endif
                                        <div>
                                            <p class="font-bold text-gray-800">{{ $dog->name }}</p>
                                            <p class="text-xs text-gray-500">{{ $dog->breed }} • {{ $dog->age }} años</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Detalles -->
                        <div>
                            <h4 class="font-semibold text-gray-700 border-b pb-2 mb-4">Detalles del servicio</h4>
                            <div class="space-y-4">
                                <div>
                                    <p class="text-xs uppercase text-gray-500 font-bold">Fechas</p>
                                    <p class="text-gray-800">
                                        Del <span class="font-semibold">{{ \Carbon\Carbon::parse($careRequest->start_date)->format('d/m/Y') }}</span> 
                                        al <span class="font-semibold">{{ \Carbon\Carbon::parse($careRequest->end_date)->format('d/m/Y') }}</span>
                                    </p>
                                </div>
                                
                                @if($careRequest->description)
                                    <div>
                                        <p class="text-xs uppercase text-gray-500 font-bold">Notas adicionales</p>
                                        <div class="mt-1 p-3 bg-gray-50 text-sm text-gray-700 whitespace-pre-wrap italic">
                                            "{{ $careRequest->description }}"
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Información del Dueño -->
                    @if($careRequest->user_id !== auth()->id())
                        <div class="mt-8 p-4 bg-indigo-50 border border-indigo-100 flex items-center justify-between">
                            <div class="flex items-center space-x-4">
                                <div class="w-12 h-12 bg-indigo-600 flex items-center justify-center text-white font-bold">
                                    {{ substr($careRequest->user->name, 0, 1) }}
                                </div>
                                <div>
                                    <p class="text-xs uppercase text-gray-500 font-bold tracking-wider">Publicado por</p>
                                    <p class="text-lg font-bold text-gray-900">{{ $careRequest->user->name }}</p>
                                </div>
                            </div>
                            <a href="mailto:{{ $careRequest->user->email }}" class="px-6 py-2 bg-indigo-600 text-white font-bold hover:bg-indigo-700 transition">
                                Contactar por Email
                            </a>
                        </div>
                    @endif

                    <div class="mt-10 pt-6 border-t flex justify-end space-x-4">
                        @if($careRequest->user_id === auth()->id())
                            <form action="{{ route('care-requests.destroy', $careRequest) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="px-4 py-2 text-sm text-red-600 font-semibold hover:bg-red-50 rounded-md transition" onclick="return confirm('¿Estás seguro de que quieres eliminar esta petición?')">
                                    Eliminar Petición
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
