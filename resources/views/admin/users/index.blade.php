<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 w-full">
            <h2 class="font-bold text-2xl text-brand-900 leading-tight">
                {{ __('Administración de Usuarios') }}
            </h2>
            <a href="{{ route('dashboard') }}" class="inline-flex items-center text-sm font-bold text-accent-600 hover:text-brand-600 transition-all duration-200">
                ← {{ __('Volver al Panel') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12 bg-gradient-to-b from-accent-50 via-brand-50/10 to-accent-100 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

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

            <!-- Search and Info Header -->
            <div class="bg-white rounded-3xl border border-brand-100/50 p-6 shadow-xl shadow-brand-50/10 flex flex-col md:flex-row justify-between items-stretch md:items-center gap-4">
                <form action="{{ route('admin.users.index') }}" method="GET" class="flex-grow max-w-md flex gap-3">
                    <div class="relative flex-grow">
                        <input type="text" name="search" value="{{ $search }}" 
                            class="block w-full border-brand-200/85 focus:border-brand-500 focus:ring-0 text-sm font-semibold rounded-2xl py-2.5 pr-3.5 placeholder-accent-350 transition bg-white text-accent-950" style="padding-left: 2.75rem;"
                            placeholder="Buscar por nombre o email...">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-accent-400">
                            🔍
                        </div>
                    </div>
                    <button type="submit" class="px-5 py-2.5 bg-brand-600 hover:bg-brand-700 text-white font-bold text-sm rounded-2xl shadow-md transition duration-200">
                        {{ __('Buscar') }}
                    </button>
                    @if($search)
                        <a href="{{ route('admin.users.index') }}" class="px-4 py-2.5 bg-accent-100 hover:bg-accent-200 text-accent-700 font-bold text-sm rounded-2xl flex items-center transition duration-200">
                            {{ __('Limpiar') }}
                        </a>
                    @endif
                </form>
                
                <div class="text-xs font-semibold text-accent-600 flex items-center gap-3">
                    <span>Total de usuarios listados: <strong>{{ $users->total() }}</strong></span>
                </div>
            </div>

            <!-- Users List Table -->
            <div class="bg-white rounded-3xl border border-brand-100/50 shadow-xl overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-brand-900 text-white text-[10px] font-black uppercase tracking-wider">
                                <th class="p-5">{{ __('Usuario') }}</th>
                                <th class="p-5">{{ __('Rol') }}</th>
                                <th class="p-5">{{ __('Mascotas') }}</th>
                                <th class="p-5">{{ __('Estado') }}</th>
                                <th class="p-5 text-right">{{ __('Acciones') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-brand-50/50 text-sm text-accent-800">
                            @forelse ($users as $user)
                                <tr class="hover:bg-brand-50/10 transition-colors duration-150">
                                    <!-- User identity -->
                                    <td class="p-5">
                                        <div class="flex items-center space-x-4">
                                            <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="w-10 h-10 rounded-xl object-cover shadow-sm border border-brand-100">
                                            <div>
                                                <h4 class="font-bold text-brand-900 leading-tight">{{ $user->name }}</h4>
                                                <p class="text-xs text-accent-500 font-medium mt-0.5">{{ $user->email }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <!-- Role -->
                                    <td class="p-5">
                                        @if ($user->isAdmin())
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-black bg-indigo-50 text-indigo-700 border border-indigo-200 uppercase tracking-wider">
                                                Admin
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-black bg-brand-50 text-brand-700 border border-brand-100 uppercase tracking-wider">
                                                Usuario
                                            </span>
                                        @endif
                                    </td>
                                    <!-- Pet Count -->
                                    <td class="p-5 font-bold text-brand-900">
                                        <div class="flex items-center space-x-1.5">
                                            <span>🐕</span>
                                            <span>{{ $user->dogs->count() }}</span>
                                        </div>
                                    </td>
                                    <!-- Status -->
                                    <td class="p-5">
                                        @if ($user->isBanned())
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-black bg-rose-50 text-rose-700 border border-rose-200 uppercase tracking-wider">
                                                🚫 Suspendido
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-black bg-emerald-50 text-emerald-700 border border-emerald-100 uppercase tracking-wider">
                                                ✅ Activo
                                            </span>
                                        @endif
                                    </td>
                                    <!-- Actions -->
                                    <td class="p-5 text-right space-x-2">
                                        <a href="{{ route('admin.users.show', $user) }}" class="inline-flex items-center px-3.5 py-1.5 bg-accent-50 hover:bg-brand-50 hover:text-brand-700 border border-brand-100 text-accent-700 font-bold text-xs rounded-xl transition duration-150">
                                            🔍 {{ __('Ver Perros') }}
                                        </a>

                                        @if ($user->id !== auth()->id())
                                            <form action="{{ route('chats.start-direct', $user) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" class="inline-flex items-center px-3.5 py-1.5 bg-brand-600 hover:bg-brand-700 text-white font-bold text-xs rounded-xl shadow-sm transition duration-150">
                                                    💬 {{ __('Chat') }}
                                                </button>
                                            </form>
                                        @endif

                                        @if (!$user->isAdmin())
                                            <form action="{{ route('admin.users.toggle-ban', $user) }}" method="POST" class="inline">
                                                @csrf
                                                @if ($user->isBanned())
                                                    <button type="submit" class="inline-flex items-center px-3.5 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl shadow-sm transition duration-150">
                                                        🔓 {{ __('Desbanear') }}
                                                    </button>
                                                @else
                                                    <button type="submit" onclick="return confirm('¿Estás seguro de que quieres banear a este usuario? Se cerrará su sesión de inmediato.')" class="inline-flex items-center px-3.5 py-1.5 bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs rounded-xl shadow-sm transition duration-150">
                                                        🚫 {{ __('Banear') }}
                                                    </button>
                                                @endif
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="p-8 text-center text-accent-550 font-bold">
                                        {{ __('No se encontraron usuarios matching.') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination footer -->
                @if ($users->hasPages())
                    <div class="bg-brand-50/10 px-6 py-4 border-t border-brand-50/50 flex justify-center">
                        <div class="w-full">
                            {{ $users->appends(['search' => $search])->links() }}
                        </div>
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
