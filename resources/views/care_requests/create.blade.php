<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-brand-900 leading-tight">
            {{ __('Crear Petición de Cuidado') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white border border-brand-100/50 rounded-3xl overflow-hidden shadow-sm">
                <div class="p-6 sm:p-8 text-gray-900">
                    <form action="{{ route('care-requests.store') }}" method="POST">
                        @csrf
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Selección de Perros -->
                            <div class="md:col-span-2">
                                <x-input-label :value="__('Selecciona los perros que necesitan cuidado')" class="text-xs font-black uppercase text-accent-600 tracking-wider mb-1.5" />
                                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 mt-2">
                                    @foreach($dogs as $dog)
                                        <label for="dog_{{ $dog->id }}" class="flex items-center p-3.5 border border-brand-100 bg-accent-50/10 rounded-2xl hover:bg-brand-50/30 cursor-pointer transition duration-150">
                                            <input type="checkbox" name="dogs[]" value="{{ $dog->id }}" id="dog_{{ $dog->id }}" class="rounded-lg border-brand-200 text-brand-600 shadow-sm focus:ring-brand-500/20 focus:border-brand-500 w-5 h-5 cursor-pointer">
                                            <span class="ml-3 text-xs font-bold text-brand-900 cursor-pointer">
                                                🐾 {{ $dog->name }}
                                                <span class="block text-[10px] text-accent-500 font-medium leading-none mt-0.5">{{ $dog->breed }}</span>
                                            </span>
                                        </label>
                                    @endforeach
                                </div>
                                <x-input-error :messages="$errors->get('dogs')" class="mt-2" />
                            </div>

                            <!-- Fecha de Inicio -->
                            <div>
                                <x-input-label for="start_date" :value="__('Fecha de inicio')" class="text-xs font-black uppercase text-accent-600 tracking-wider mb-1.5" />
                                <x-text-input id="start_date" class="block w-full" type="date" name="start_date" :value="old('start_date')" required />
                                <x-input-error :messages="$errors->get('start_date')" class="mt-2" />
                            </div>

                            <!-- Fecha de Fin -->
                            <div>
                                <x-input-label for="end_date" :value="__('Fecha de finalización')" class="text-xs font-black uppercase text-accent-600 tracking-wider mb-1.5" />
                                <x-text-input id="end_date" class="block w-full" type="date" name="end_date" :value="old('end_date')" required />
                                <x-input-error :messages="$errors->get('end_date')" class="mt-2" />
                            </div>

                            <!-- Precio -->
                            <div>
                                <x-input-label for="price" :value="__('Precio ofrecido (€)')" class="text-xs font-black uppercase text-accent-600 tracking-wider mb-1.5" />
                                <x-text-input id="price" class="block w-full" type="number" step="0.01" name="price" :value="old('price')" required placeholder="Ej. 120" />
                                <x-input-error :messages="$errors->get('price')" class="mt-2" />
                            </div>

                            <!-- Notas/Información Adicional -->
                            <div class="md:col-span-2">
                                <x-input-label for="description" :value="__('Información adicional / Notas')" class="text-xs font-black uppercase text-accent-600 tracking-wider mb-1.5" />
                                <textarea id="description" name="description" rows="4" class="block w-full border-brand-200/80 focus:border-brand-500 focus:ring-brand-500/20 text-xs font-semibold rounded-2xl py-2.5 px-3 placeholder-accent-400 transition" placeholder="Detalla horarios, comportamiento o necesidades especiales...">{{ old('description') }}</textarea>
                                <x-input-error :messages="$errors->get('description')" class="mt-2" />
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-8 pt-6 border-t border-brand-50">
                            <a href="{{ route('care-requests.index') }}" class="text-sm font-bold text-accent-600 hover:text-brand-600 mr-6 transition-colors">
                                Cancelar
                            </a>
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
