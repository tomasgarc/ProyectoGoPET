<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Editar Mascota: ') . $dog->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form action="{{ route('dogs.update', $dog) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PATCH')
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Nombre -->
                            <div>
                                <x-input-label for="name" :value="__('Nombre del perro')" />
                                <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name', $dog->name)" required autofocus />
                                <x-input-error :messages="$errors->get('name')" class="mt-2" />
                            </div>

                            <!-- Raza -->
                            <div>
                                <x-input-label for="breed" :value="__('Raza')" />
                                <x-text-input id="breed" class="block mt-1 w-full" type="text" name="breed" :value="old('breed', $dog->breed)" />
                                <x-input-error :messages="$errors->get('breed')" class="mt-2" />
                            </div>

                            <!-- Edad -->
                            <div>
                                <x-input-label for="age" :value="__('Edad (años)')" />
                                <x-text-input id="age" class="block mt-1 w-full" type="number" name="age" :value="old('age', $dog->age)" />
                                <x-input-error :messages="$errors->get('age')" class="mt-2" />
                            </div>

                            <!-- Tamaño -->
                            <div>
                                <x-input-label for="size" :value="__('Tamaño')" />
                                <select id="size" name="size" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                    <option value="Pequeño" {{ $dog->size == 'Pequeño' ? 'selected' : '' }}>{{ __('Pequeño') }}</option>
                                    <option value="Mediano" {{ $dog->size == 'Mediano' ? 'selected' : '' }}>{{ __('Mediano') }}</option>
                                    <option value="Grande" {{ $dog->size == 'Grande' ? 'selected' : '' }}>{{ __('Grande') }}</option>
                                    <option value="Gigante" {{ $dog->size == 'Gigante' ? 'selected' : '' }}>{{ __('Gigante') }}</option>
                                </select>
                                <x-input-error :messages="$errors->get('size')" class="mt-2" />
                            </div>

                            <!-- Foto -->
                            <div class="md:col-span-2">
                                <x-input-label for="photo" :value="__('Actualizar Foto')" />
                                @if($dog->photo)
                                    <div class="mt-2 mb-4">
                                        <img src="{{ asset('storage/' . $dog->photo) }}" alt="Foto actual" class="w-32 h-32 object-cover rounded-md border">
                                    </div>
                                @endif
                                <input id="photo" name="photo" type="file" class="block mt-1 w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100" />
                                <x-input-error :messages="$errors->get('photo')" class="mt-2" />
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-6">
                            <a href="{{ route('dogs.index') }}" class="text-sm text-gray-600 hover:underline mr-4">Cancelar</a>
                            <x-primary-button>
                                {{ __('Actualizar Mascota') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
