<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center w-full">
            <h2 class="font-bold text-2xl text-brand-900 leading-tight">
                {{ __('Detalle de Usuario') }}
            </h2>
            <a href="{{ route('admin.users.index') }}" class="inline-flex items-center text-sm font-bold text-accent-600 hover:text-brand-600 transition-all duration-200">
                ← {{ __('Volver a la Lista') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12 bg-gradient-to-b from-accent-50 via-brand-50/10 to-accent-100 min-h-screen">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Alerts -->
            @if (session('success'))
                <div class="bg-emerald-50 border border-emerald-250 text-emerald-950 px-5 py-4 rounded-3xl relative flex items-center shadow-sm" role="alert">
                    <span class="mr-2.5">✅</span>
                    <span class="font-bold text-sm">{{ session('success') }}</span>
                </div>
            @endif

            @if (session('error'))
                <div class="bg-rose-50 border border-rose-250 text-rose-950 px-5 py-4 rounded-3xl relative flex items-center shadow-sm" role="alert">
                    <span class="mr-2.5">❌</span>
                    <span class="font-bold text-sm">{{ session('error') }}</span>
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
                
                <!-- Columna Izquierda: Tarjeta del Usuario -->
                <div class="lg:col-span-4 space-y-6">
                    <div class="bg-white rounded-3xl p-6 border border-brand-100/50 shadow-xl relative overflow-hidden">
                        <div class="absolute top-0 right-0 w-24 h-24 bg-brand-500/5 rounded-full blur-xl -mr-6 -mt-6"></div>
                        
                        <div class="flex flex-col items-center text-center space-y-4">
                            <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="w-24 h-24 rounded-3xl object-cover shadow-md border-2 border-brand-200">
                            
                            <div>
                                <h3 class="text-lg font-black text-brand-900 tracking-tight">{{ $user->name }}</h3>
                                <p class="text-xs text-accent-500 font-bold mt-0.5">{{ $user->email }}</p>
                            </div>

                            <div class="flex flex-wrap gap-2 justify-center">
                                @if ($user->isAdmin())
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-black bg-indigo-50 text-indigo-700 border border-indigo-200 uppercase tracking-wider">
                                        Admin
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-black bg-brand-50 text-brand-700 border border-brand-100 uppercase tracking-wider">
                                        Usuario
                                    </span>
                                @endif

                                @if ($user->isBanned())
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-black bg-rose-50 text-rose-700 border border-rose-200 uppercase tracking-wider">
                                        🚫 Suspendido
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-black bg-emerald-50 text-emerald-700 border border-emerald-100 uppercase tracking-wider">
                                        ✅ Activo
                                    </span>
                                @endif
                            </div>

                            <div class="w-full pt-4 border-t border-brand-50/50 text-left space-y-3 text-xs text-accent-700 font-semibold">
                                <div class="flex justify-between">
                                    <span class="text-accent-500">Miembro desde:</span>
                                    <span>{{ $user->created_at->format('d/m/Y') }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-accent-500">Valoraciones:</span>
                                    @if ($user->reviews_count > 0)
                                        <span class="text-amber-500">{{ $user->average_rating }}★ ({{ $user->reviews_count }})</span>
                                    @else
                                        <span class="text-accent-400">Sin valorar</span>
                                    @endif
                                </div>
                            </div>

                            <!-- Botón de Chat Directo -->
                            @if ($user->id !== auth()->id())
                                <div class="w-full pt-4">
                                    <form action="{{ route('chats.start-direct', $user) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="w-full inline-flex items-center justify-center px-4 py-3 bg-brand-600 hover:bg-brand-700 text-white font-bold text-xs uppercase tracking-wider rounded-2xl shadow-md transition duration-150">
                                            💬 {{ __('Iniciar Chat Directo') }}
                                        </button>
                                    </form>
                                </div>
                            @endif

                            <!-- Botón de Baneo -->
                            @if (!$user->isAdmin())
                                <div class="w-full pt-4">
                                    <form action="{{ route('admin.users.toggle-ban', $user) }}" method="POST">
                                        @csrf
                                        @if ($user->isBanned())
                                            <button type="submit" class="w-full inline-flex items-center justify-center px-4 py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs uppercase tracking-wider rounded-2xl shadow-md transition duration-150">
                                                🔓 {{ __('Desbanear Usuario') }}
                                            </button>
                                        @else
                                            <button type="submit" onclick="return confirm('¿De verdad quieres banear a este usuario? Se le cerrará la sesión de forma inmediata y no podrá entrar de nuevo.')" class="w-full inline-flex items-center justify-center px-4 py-3 bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs uppercase tracking-wider rounded-2xl shadow-md transition duration-150">
                                                🚫 {{ __('Banear Usuario') }}
                                            </button>
                                        @endif
                                    </form>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Columna Derecha: Lista de Perros -->
                <div class="lg:col-span-8 space-y-6">
                    <div class="bg-white rounded-3xl p-6 border border-brand-100/50 shadow-xl">
                        <h3 class="font-black text-lg text-brand-900 border-b border-brand-50 pb-4 mb-6 flex items-center">
                            <span class="mr-2">🐕</span>
                            {{ __('Perros Registrados') }}
                            <span class="ml-2 text-xs bg-brand-50 text-brand-700 px-2 py-0.5 rounded-full font-black">{{ $user->dogs->count() }}</span>
                        </h3>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                            @forelse ($user->dogs as $dog)
                                <div class="border border-brand-100 hover:border-brand-300 rounded-2xl p-5 flex items-center space-x-4 hover:shadow-lg hover:scale-[1.01] transition-all duration-200 bg-white">
                                    @if ($dog->photo)
                                        <img src="{{ asset('storage/' . $dog->photo) }}" alt="{{ $dog->name }}" class="w-16 h-16 rounded-2xl object-cover border border-brand-200 flex-shrink-0 shadow-sm">
                                    @else
                                        <div class="w-16 h-16 bg-gradient-to-br from-brand-50 to-brand-100 border border-brand-200 text-brand-700 rounded-2xl flex items-center justify-center text-4xl flex-shrink-0 shadow-sm">
                                            🐕
                                        </div>
                                    @endif
                                    <div class="flex-grow min-w-0">
                                        <h4 class="font-black text-brand-900 truncate text-lg leading-none">{{ $dog->name }}</h4>
                                        <p class="text-xs text-accent-600 font-bold truncate mt-2 flex items-center">
                                            <span class="mr-1">🏷️</span> {{ $dog->breed }}
                                        </p>
                                        <div class="flex gap-2 mt-3">
                                            <span class="inline-flex px-2.5 py-1 rounded-lg bg-brand-50 border border-brand-200 text-[10px] font-black uppercase text-brand-700 tracking-wider">
                                                {{ $dog->size }}
                                            </span>
                                            <span class="inline-flex px-2.5 py-1 rounded-lg bg-indigo-50 border border-indigo-150 text-[10px] font-black text-indigo-700 tracking-wider">
                                                {{ $dog->age }} {{ $dog->age === 1 ? 'año' : 'años' }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="col-span-2 py-12 text-center text-accent-550 font-bold">
                                    <span class="text-4xl block mb-3">📭</span>
                                    {{ __('Este usuario no tiene perros registrados en su cuenta.') }}
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </div>
</x-app-layout>
