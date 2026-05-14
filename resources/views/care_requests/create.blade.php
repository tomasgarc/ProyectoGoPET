<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Crear Petición de Cuidado') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form action="{{ route('care-requests.store') }}" method="POST">
                        @csrf
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Selección de Perros -->
                            <div class="md:col-span-2">
                                <x-input-label :value="__('Selecciona los perros que necesitan cuidado')" />
                                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-2">
                                    @foreach($dogs as $dog)
                                        <div class="flex items-center p-3 border rounded-lg hover:bg-gray-50 cursor-pointer">
                                            <input type="checkbox" name="dogs[]" value="{{ $dog->id }}" id="dog_{{ $dog->id }}" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                            <label for="dog_{{ $dog->id }}" class="ml-2 text-sm text-gray-700 cursor-pointer">
                                                {{ $dog->name }} ({{ $dog->breed }})
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                                <x-input-error :messages="$errors->get('dogs')" class="mt-2" />
                            </div>

                            <!-- Fecha de Inicio -->
                            <div>
                                <x-input-label for="start_date" :value="__('Fecha de inicio')" />
                                <x-text-input id="start_date" class="block mt-1 w-full" type="date" name="start_date" :value="old('start_date')" required />
                                <x-input-error :messages="$errors->get('start_date')" class="mt-2" />
                            </div>

                            <!-- Fecha de Fin -->
                            <div>
                                <x-input-label for="end_date" :value="__('Fecha de finalización')" />
                                <x-text-input id="end_date" class="block mt-1 w-full" type="date" name="end_date" :value="old('end_date')" required />
                                <x-input-error :messages="$errors->get('end_date')" class="mt-2" />
                            </div>

                            <!-- Precio -->
                            <div>
                                <x-input-label for="price" :value="__('Precio ofrecido (€)')" />
                                <x-text-input id="price" class="block mt-1 w-full" type="number" step="0.01" name="price" :value="old('price')" required />
                                <x-input-error :messages="$errors->get('price')" class="mt-2" />
                            </div>

                            <!-- Notas/Información Adicional -->
                            <div class="md:col-span-2">
                                <x-input-label for="description" :value="__('Información adicional / Notas')" />
                                <textarea id="description" name="description" rows="4" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('description') }}</textarea>
                                <x-input-error :messages="$errors->get('description')" class="mt-2" />
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-6">
                            <a href="{{ route('care-requests.index') }}" class="text-sm text-gray-600 hover:underline mr-4">Cancelar</a>
                            <x-primary-button>
                                {{ __('Publicar Petición') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
