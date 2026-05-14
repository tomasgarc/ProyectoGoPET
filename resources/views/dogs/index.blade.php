<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Mis Perros') }}
            </h2>
            <a href="{{ route('dogs.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-700 hover:bg-indigo-800 text-white border border-transparent rounded-md font-semibold text-xs uppercase tracking-widest active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-sm" style="background-color: #4338ca; color: white;">
                + {{ __('Añadir Perro') }}
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
                    <div class="gap-6" style="display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 1.5rem;">
                        @foreach($dogs as $dog)
                            <div class="border shadow-sm hover:shadow-md transition bg-white group flex flex-col">
                                <div class="w-full" style="aspect-ratio: 1/1; overflow: hidden;">
                                    @if($dog->photo)
                                        <img src="{{ asset('storage/' . $dog->photo) }}" alt="{{ $dog->name }}" style="width: 100%; height: 100%; object-fit: cover;">
                                    @else
                                        <div style="width: 100%; height: 100%; background-color: #f3f4f6; display: flex; align-items: center; justify-content: center;">
                                            <span style="font-size: 3rem; opacity: 0.3;">🐾</span>
                                        </div>
                                    @endif
                                </div>
                                <div class="p-4 text-center border-t">
                                    <h3 class="text-lg font-bold text-gray-900">{{ $dog->name }}</h3>
                                    <p class="text-sm text-gray-600">{{ $dog->breed ?? 'Raza no especificada' }}</p>
                                    <div class="mt-2 flex justify-between items-center">
                                        <span class="text-[10px] bg-indigo-100 text-indigo-800 px-2 py-1 font-bold uppercase tracking-wider">{{ $dog->size ?? 'N/A' }}</span>
                                        <span class="text-xs text-gray-500 font-medium">{{ $dog->age }} años</span>
                                    </div>
                                    <div class="mt-4 flex justify-center gap-4 border-t pt-3">
                                        <a href="{{ route('dogs.edit', $dog) }}" class="text-xs font-bold text-indigo-600 hover:text-indigo-800 uppercase tracking-wider">Editar</a>
                                        <form action="{{ route('dogs.destroy', $dog) }}" method="POST" onsubmit="return confirm('¿Estás seguro de eliminar este perro?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-xs font-bold text-red-600 hover:text-red-800 uppercase tracking-wider">Eliminar</button>
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
