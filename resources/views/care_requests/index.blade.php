<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Mis Peticiones de Cuidado') }}
            </h2>
            <a href="{{ route('care-requests.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-700 hover:bg-indigo-800 text-white border border-transparent rounded-md font-semibold text-xs uppercase tracking-widest active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-sm" style="background-color: #4338ca; color: white;">
                {{ __('Nueva Petición') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if($careRequests->isEmpty())
                        <p class="text-center text-gray-500 py-4">{{ __('No tienes peticiones de cuidado publicadas.') }}</p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Perros</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fechas</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Precio</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($careRequests as $request)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex flex-wrap gap-1">
                                                    @foreach($request->dogs as $dog)
                                                        <span class="px-2 py-1 bg-indigo-100 text-indigo-700 rounded-full text-xs font-semibold">
                                                            {{ $dog->name }}
                                                        </span>
                                                    @endforeach
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900">{{ \Carbon\Carbon::parse($request->start_date)->format('d/m/Y') }}</div>
                                                <div class="text-sm text-gray-500">{{ \Carbon\Carbon::parse($request->end_date)->format('d/m/Y') }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-bold">
                                                {{ number_format($request->price, 2) }}€
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                    {{ $request->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : ($request->status === 'accepted' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800') }}">
                                                    {{ ucfirst($request->status) }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                <a href="{{ route('care-requests.show', $request) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">Ver</a>
                                                <form action="{{ route('care-requests.destroy', $request) }}" method="POST" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('¿Estás seguro?')">Eliminar</button>
                                                </form>
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
</x-app-layout>
