<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Mis Perros') }}
            </h2>
            <a href="{{ route('dogs.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                + Añadir Perro
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                @if($dogs->isEmpty())
                    <div class="text-center py-8">
                        <p class="text-gray-500">Aún no has añadido ningún perro a tu perfil.</p>
                        <a href="{{ route('dogs.create') }}" class="text-indigo-600 hover:underline mt-2 inline-block">¡Empieza añadiendo uno aquí!</a>
                    </div>
                @else
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($dogs as $dog)
                            <div class="border rounded-lg overflow-hidden shadow-sm hover:shadow-md transition">
                                @if($dog->photo)
                                    <img src="{{ asset('storage/' . $dog->photo) }}" alt="{{ $dog->name }}" class="w-full h-48 object-cover">
                                @else
                                    <div class="w-full h-48 bg-gray-200 flex items-center justify-center">
                                        <span class="text-gray-400 text-5xl">🐾</span>
                                    </div>
                                @endif
                                <div class="p-4">
                                    <h3 class="text-lg font-bold text-gray-900">{{ $dog->name }}</h3>
                                    <p class="text-sm text-gray-600">{{ $dog->breed ?? 'Raza no especificada' }}</p>
                                    <div class="mt-2 flex justify-between items-center">
                                        <span class="text-xs bg-indigo-100 text-indigo-800 px-2 py-1 rounded">{{ $dog->size ?? 'Tamaño N/A' }}</span>
                                        <span class="text-xs text-gray-500">{{ $dog->age }} años</span>
                                    </div>
                                    <div class="mt-4 flex gap-2">
                                        <a href="{{ route('dogs.edit', $dog) }}" class="text-sm text-indigo-600 hover:underline">Editar</a>
                                        <form action="{{ route('dogs.destroy', $dog) }}" method="POST" onsubmit="return confirm('¿Estás seguro de eliminar este perro?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-sm text-red-600 hover:underline">Eliminar</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
