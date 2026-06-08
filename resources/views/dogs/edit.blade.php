<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-brand-900 leading-tight">
            {{ __('Editar Mascota: ') . $dog->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white border border-brand-100/50 rounded-3xl overflow-hidden shadow-sm">
                <div class="p-6 sm:p-8 text-gray-900">
                    <form action="{{ route('dogs.update', $dog) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PATCH')
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Nombre -->
                            <div>
                                <x-input-label for="name" :value="__('Nombre del perro')" class="text-xs font-black uppercase text-accent-600 tracking-wider mb-1.5" />
                                <x-text-input id="name" class="block w-full" type="text" name="name" :value="old('name', $dog->name)" required autofocus />
                                <x-input-error :messages="$errors->get('name')" class="mt-2" />
                            </div>

                            <!-- Raza -->
                            <div>
                                <x-input-label for="breed" :value="__('Raza')" class="text-xs font-black uppercase text-accent-600 tracking-wider mb-1.5" />
                                <x-text-input id="breed" class="block w-full" type="text" name="breed" :value="old('breed', $dog->breed)" />
                                <x-input-error :messages="$errors->get('breed')" class="mt-2" />
                            </div>

                            <!-- Edad -->
                            <div>
                                <x-input-label for="age" :value="__('Edad (años)')" class="text-xs font-black uppercase text-accent-600 tracking-wider mb-1.5" />
                                <x-text-input id="age" class="block w-full" type="number" name="age" :value="old('age', $dog->age)" />
                                <x-input-error :messages="$errors->get('age')" class="mt-2" />
                            </div>

                            <!-- Tamaño -->
                            <div>
                                <x-input-label for="size" :value="__('Tamaño')" class="text-xs font-black uppercase text-accent-600 tracking-wider mb-1.5" />
                                <select id="size" name="size" class="block w-full border-brand-200/80 focus:border-brand-500 focus:ring-brand-500/20 rounded-2xl shadow-sm text-accent-950 bg-white placeholder-accent-400 transition-all duration-150 py-2.5 px-3">
                                    <option value="pequeño" {{ strtolower($dog->size) == 'pequeño' ? 'selected' : '' }}>{{ __('Pequeño') }}</option>
                                    <option value="mediano" {{ strtolower($dog->size) == 'mediano' ? 'selected' : '' }}>{{ __('Mediano') }}</option>
                                    <option value="grande" {{ strtolower($dog->size) == 'grande' ? 'selected' : '' }}>{{ __('Grande') }}</option>
                                    <option value="gigante" {{ strtolower($dog->size) == 'gigante' ? 'selected' : '' }}>{{ __('Gigante') }}</option>
                                </select>
                                <x-input-error :messages="$errors->get('size')" class="mt-2" />
                            </div>

                            <!-- Sexo -->
                            <div>
                                <x-input-label for="sex" :value="__('Sexo')" class="text-xs font-black uppercase text-accent-600 tracking-wider mb-1.5" />
                                <select id="sex" name="sex" class="block w-full border-brand-200/80 focus:border-brand-500 focus:ring-brand-500/20 rounded-2xl shadow-sm text-accent-950 bg-white placeholder-accent-400 transition-all duration-150 py-2.5 px-3">
                                    <option value="macho" {{ strtolower($dog->sex) == 'macho' ? 'selected' : '' }}>{{ __('Macho') }}</option>
                                    <option value="hembra" {{ strtolower($dog->sex) == 'hembra' ? 'selected' : '' }}>{{ __('Hembra') }}</option>
                                </select>
                                <x-input-error :messages="$errors->get('sex')" class="mt-2" />
                            </div>

                            <!-- Foto -->
                            <div class="md:col-span-2">
                                <x-input-label for="photo" :value="__('Actualizar Foto')" class="text-xs font-black uppercase text-accent-600 tracking-wider mb-1.5" />
                                @if($dog->photo)
                                    <div class="mt-2 mb-4 flex items-center space-x-4 p-3 bg-accent-50/50 rounded-2xl border border-brand-50 max-w-sm">
                                        <img src="{{ asset('storage/' . $dog->photo) }}" alt="Foto actual" class="w-20 h-20 aspect-square object-cover rounded-xl border border-brand-100 shadow-sm">
                                        <div>
                                            <p class="text-xs font-bold text-accent-600 uppercase tracking-wider">Foto actual</p>
                                            <p class="text-xs font-medium text-accent-400 mt-0.5">Sube otra para cambiarla</p>
                                        </div>
                                    </div>
                                @endif
                                <input id="photo" name="photo" type="file" class="block w-full text-sm text-accent-600 file:mr-4 file:py-2.5 file:px-4 file:rounded-2xl file:border-2 file:border-brand-200/60 file:text-xs file:font-black file:bg-brand-50 file:text-brand-700 hover:file:bg-brand-100 transition-all duration-150" />
                                <x-input-error :messages="$errors->get('photo')" class="mt-2" />
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-8 pt-6 border-t border-brand-50">
                            <a href="{{ route('dogs.index') }}" class="text-sm font-bold text-accent-600 hover:text-brand-600 mr-6 transition-colors">
                                Cancelar
                            </a>
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
