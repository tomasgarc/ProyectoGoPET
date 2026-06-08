<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-bold text-2xl text-brand-900 leading-tight">
                {{ __('Mis Perros') }}
            </h2>
            <a href="{{ route('dogs.create') }}" class="inline-flex items-center px-5 py-2.5 bg-brand-200 hover:bg-brand-500 text-brand-900 hover:text-white font-bold text-xs uppercase tracking-widest rounded-2xl shadow-md shadow-brand-150/40 hover:scale-[1.02] active:scale-[0.98] transition-all duration-150">
                + {{ __('Añadir Perro') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-6 bg-brand-50 border border-brand-200 text-brand-900 px-4 py-3 rounded-2xl relative flex items-center shadow-sm" role="alert">
                    <span class="mr-2">✨</span>
                    <span class="font-bold text-sm">{{ session('success') }}</span>
                </div>
            @endif

            <div class="bg-white border border-brand-100/50 rounded-3xl p-6 sm:p-8 shadow-sm">
                @if($dogs->isEmpty())
                    <div class="text-center py-12 space-y-4 max-w-md mx-auto">
                        <span class="text-6xl block">🐾</span>
                        <h3 class="font-black text-xl text-brand-900">Aún no tienes perros registrados</h3>
                        <p class="text-accent-600 font-medium text-sm leading-relaxed">
                            Registra a tus mascotas para que otros usuarios las conozcan cuando publiques peticiones de cuidado.
                        </p>
                        <div class="pt-2">
                            <a href="{{ route('dogs.create') }}" class="inline-flex items-center px-5 py-2.5 bg-brand-200 hover:bg-brand-500 text-brand-900 hover:text-white font-bold text-sm rounded-2xl shadow-md transition-all duration-150 hover:scale-[1.02]">
                                ¡Añade tu primer perro aquí!
                            </a>
                        </div>
                    </div>
                @else
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($dogs as $dog)
                            <div class="bg-accent-50/20 border border-brand-100/50 rounded-3xl overflow-hidden shadow-sm hover:shadow-xl hover:shadow-brand-100/10 hover:border-brand-200/50 transition-all duration-300 flex flex-col justify-between group">
                                <div class="w-full relative aspect-square overflow-hidden bg-accent-100/30">
                                    @if($dog->photo)
                                        <img src="{{ asset('storage/' . $dog->photo) }}" alt="{{ $dog->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                    @else
                                        <div class="w-full h-full flex flex-col items-center justify-center space-y-2">
                                            <span class="text-5xl opacity-40">🐕</span>
                                            <span class="text-xs text-accent-500 font-bold uppercase tracking-wider">Sin foto</span>
                                        </div>
                                    @endif
                                </div>
                                <div class="p-5 border-t border-brand-100/50 space-y-4">
                                    <div class="text-center">
                                        <h3 class="text-xl font-black text-brand-900 leading-tight">{{ $dog->name }}</h3>
                                        <p class="text-sm text-accent-600 font-semibold mt-1">{{ $dog->breed ?? 'Raza no especificada' }}</p>
                                    </div>
                                    <div class="flex justify-between items-center bg-white px-4 py-2 border border-brand-50 rounded-2xl text-xs font-bold text-brand-800">
                                        <span class="bg-brand-50 text-brand-700 px-2.5 py-0.5 rounded-full uppercase tracking-wider text-[10px]">{{ $dog->size ?? 'Mediano' }}</span>
                                        <span class="text-brand-600 px-2 py-0.5 bg-brand-50/50 rounded-full uppercase tracking-wider text-[10px]">{{ $dog->sex ?? 'Macho' }}</span>
                                        <span class="text-accent-600">{{ $dog->age }} {{ $dog->age == 1 ? 'año' : 'años' }}</span>
                                    </div>
                                    <div class="flex justify-center gap-3 pt-3 border-t border-brand-50/50">
                                        <a href="{{ route('dogs.edit', $dog) }}" class="inline-flex items-center justify-center px-4 py-2 bg-brand-50 border border-brand-100 hover:bg-brand-100 hover:text-brand-800 text-brand-750 font-bold text-xs uppercase tracking-wider rounded-xl transition duration-150">
                                            {{ __('Editar') }}
                                        </a>
                                        <form action="{{ route('dogs.destroy', $dog) }}" method="POST" onsubmit="return confirm('¿Estás seguro de eliminar este perro?')" class="inline-block">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="inline-flex items-center justify-center px-4 py-2 bg-rose-50 border border-rose-100 hover:bg-rose-100 hover:text-rose-800 text-rose-650 font-bold text-xs uppercase tracking-wider rounded-xl transition duration-150">
                                                {{ __('Eliminar') }}
                                            </button>
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
