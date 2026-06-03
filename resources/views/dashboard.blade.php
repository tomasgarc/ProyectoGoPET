<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 w-full">
            <h2 class="font-bold text-2xl text-brand-900 leading-tight">
                {{ __('Panel de Control') }}
            </h2>
            
            <!-- Python ETL Trigger Button -->
            <form action="{{ route('dashboard.update-analytics') }}" method="POST">
                @csrf
                <button type="submit" class="inline-flex items-center px-4 py-2.5 bg-gradient-to-r from-brand-600 to-indigo-600 hover:from-brand-700 hover:to-indigo-700 text-white font-bold text-sm rounded-2xl transition-all duration-200 hover:scale-[1.02] shadow-md shadow-brand-100/50">
                    <span class="mr-2">🐍</span>
                    {{ __('Actualizar Estadísticas (Python)') }}
                </button>
            </form>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            
            <!-- Status Alerts -->
            @if (session('success'))
                <div class="bg-emerald-50 border-2 border-emerald-200 text-emerald-900 rounded-2xl p-4 shadow-sm flex items-center space-x-3">
                    <span class="text-xl">✅</span>
                    <p class="text-sm font-bold">{{ session('success') }}</p>
                </div>
            @endif

            @if (session('error'))
                <div class="bg-rose-50 border-2 border-rose-200 text-rose-900 rounded-2xl p-4 shadow-sm flex items-center space-x-3">
                    <span class="text-xl">❌</span>
                    <p class="text-sm font-bold">{{ session('error') }}</p>
                </div>
            @endif

            <!-- Welcome Banner -->
            <div class="bg-gradient-to-r from-brand-600 to-brand-700 text-white rounded-3xl p-8 shadow-xl shadow-brand-100/30 relative overflow-hidden">
                <div class="absolute -right-16 -top-16 opacity-10">
                    <x-application-logo class="w-64 h-64" />
                </div>
                <div class="relative z-10 space-y-3">
                    <h1 class="text-3xl sm:text-4xl font-black">¡Hola, {{ Auth::user()->name }}! 🐾</h1>
                    <p class="text-brand-100 font-medium text-sm sm:text-base max-w-xl">
                        Te damos la bienvenida a tu panel de control de GoPET. Aquí puedes gestionar a tus mascotas, ver tus solicitudes de cuidado y responder mensajes de la comunidad.
                    </p>
                </div>
            </div>

            <!-- Stats Grid (User Context) -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Stat 1: Mis Perros -->
                <div class="bg-white border border-brand-100/50 p-6 rounded-3xl shadow-md flex items-center space-x-4 hover:shadow-lg transition-shadow duration-300">
                    <div class="w-12 h-12 bg-brand-50 rounded-2xl flex items-center justify-center text-2xl">🐕</div>
                    <div>
                        <span class="block text-2xl font-black text-brand-900">{{ Auth::user()->dogs->count() }}</span>
                        <span class="text-xs text-accent-600 font-bold uppercase tracking-wider">Perros Registrados</span>
                    </div>
                </div>
                <!-- Stat 2: Mis Peticiones -->
                <div class="bg-white border border-brand-100/50 p-6 rounded-3xl shadow-md flex items-center space-x-4 hover:shadow-lg transition-shadow duration-300">
                    <div class="w-12 h-12 bg-brand-50 rounded-2xl flex items-center justify-center text-2xl">📋</div>
                    <div>
                        <span class="block text-2xl font-black text-brand-900">{{ Auth::user()->careRequests->count() }}</span>
                        <span class="text-xs text-accent-600 font-bold uppercase tracking-wider">Mis Solicitudes</span>
                    </div>
                </div>
                <!-- Stat 3: Favoritos -->
                <div class="bg-white border border-brand-100/50 p-6 rounded-3xl shadow-md flex items-center space-x-4 hover:shadow-lg transition-shadow duration-300">
                    <div class="w-12 h-12 bg-brand-50 rounded-2xl flex items-center justify-center text-2xl">⭐</div>
                    <div>
                        <span class="block text-2xl font-black text-brand-900">{{ Auth::user()->favoriteCareRequests->count() }}</span>
                        <span class="text-xs text-accent-600 font-bold uppercase tracking-wider">Peticiones Favoritas</span>
                    </div>
                </div>
            </div>

            <!-- Python Global Analysis & Metrics (ETL Result) -->
            @if ($stats)
                <div class="bg-white border border-brand-100/50 rounded-3xl p-8 shadow-md space-y-8">
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 border-b border-brand-50 pb-6">
                        <div>
                            <h3 class="font-black text-xl text-brand-900">Métricas Globales de la Plataforma</h3>
                            <p class="text-xs text-accent-600 font-semibold mt-1">
                                Datos agregados y analizados mediante el pipeline de Python ETL sobre GoPET.
                            </p>
                        </div>
                        @if (isset($stats['is_fallback']) && $stats['is_fallback'])
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-black bg-amber-50 text-amber-700 border border-amber-200">
                                ⚠️ Modo de compatibilidad PHP
                            </span>
                        @else
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-black bg-indigo-50 text-indigo-700 border border-indigo-200">
                                🐍 Procesado con Python
                            </span>
                        @endif
                    </div>

                    <!-- Platform Stats Cards -->
                    <div class="grid grid-cols-2 lg:grid-cols-4 gap-6">
                        <!-- Stat card: Vol. Transaccionado -->
                        <div class="p-5 bg-accent-50/30 border border-brand-50/50 rounded-2xl">
                            <span class="text-xs text-accent-600 font-bold uppercase tracking-wider">Vol. Transaccionado</span>
                            <span class="block text-3xl font-black text-brand-900 mt-2">{{ number_format($stats['total_volume'], 2) }}€</span>
                        </div>
                        <!-- Stat card: Comisiones Platform -->
                        <div class="p-5 bg-accent-50/30 border border-brand-50/50 rounded-2xl">
                            <span class="text-xs text-accent-600 font-bold uppercase tracking-wider">Comisiones Plataforma</span>
                            <span class="block text-3xl font-black text-brand-900 mt-2 text-indigo-600">{{ number_format($stats['platform_fees'], 2) }}€</span>
                        </div>
                        <!-- Stat card: Mascotas Totales -->
                        <div class="p-5 bg-accent-50/30 border border-brand-50/50 rounded-2xl">
                            <span class="text-xs text-accent-600 font-bold uppercase tracking-wider">Perros Totales</span>
                            <span class="block text-3xl font-black text-brand-900 mt-2">{{ $stats['total_dogs'] }}</span>
                        </div>
                        <!-- Stat card: Rating Medio -->
                        <div class="p-5 bg-accent-50/30 border border-brand-50/50 rounded-2xl">
                            <span class="text-xs text-accent-600 font-bold uppercase tracking-wider">Valoración Media</span>
                            <span class="block text-3xl font-black text-brand-900 mt-2 text-amber-500">{{ $stats['average_rating'] }}★</span>
                        </div>
                    </div>

                    <!-- Charts Section -->
                    @if (isset($stats['charts_generated']) && $stats['charts_generated'])
                        <div class="space-y-6">
                            <h4 class="font-black text-lg text-brand-900 border-b border-brand-50/50 pb-2">Visualización Gráfica</h4>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                                <!-- Dog Sizes Chart -->
                                <div class="bg-accent-50/20 border border-brand-50/50 p-4 rounded-2xl flex flex-col items-center shadow-inner hover:scale-[1.01] transition-transform duration-200">
                                    <img src="{{ asset('images/analytics/dog_sizes.png') }}?v={{ time() }}" alt="Tamaños de Perro" class="max-w-full h-auto rounded-xl">
                                    <span class="text-xs text-accent-600 font-bold mt-2">Distribución de tamaños</span>
                                </div>
                                <!-- Request Status Chart -->
                                <div class="bg-accent-50/20 border border-brand-50/50 p-4 rounded-2xl flex flex-col items-center shadow-inner hover:scale-[1.01] transition-transform duration-200">
                                    <img src="{{ asset('images/analytics/request_statuses.png') }}?v={{ time() }}" alt="Estados de Peticiones" class="max-w-full h-auto rounded-xl">
                                    <span class="text-xs text-accent-600 font-bold mt-2">Solicitudes de cuidado por estado</span>
                                </div>
                                <!-- Financial Chart -->
                                <div class="bg-accent-50/20 border border-brand-50/50 p-4 rounded-2xl flex flex-col items-center shadow-inner hover:scale-[1.01] transition-transform duration-200">
                                    <img src="{{ asset('images/analytics/revenue_stats.png') }}?v={{ time() }}" alt="Estadísticas de Pagos" class="max-w-full h-auto rounded-xl">
                                    <span class="text-xs text-accent-600 font-bold mt-2">Flujos de pagos reales en GoPET</span>
                                </div>
                            </div>
                        </div>
                    @else
                        <!-- Text fallback for charts -->
                        <div class="bg-accent-50/40 border border-brand-100 p-6 rounded-2xl space-y-4">
                            <div class="flex items-center space-x-3">
                                <span class="text-xl">💡</span>
                                <h4 class="font-black text-sm text-brand-900">Gráficos de Python no generados físicamente</h4>
                            </div>
                            <p class="text-xs text-accent-600 leading-relaxed max-w-2xl">
                                Los gráficos visuales no se han generado en el servidor (puede deberse a que no están instaladas las librerías `matplotlib` y `pandas` de Python). Sin embargo, a continuación se desglosan los datos de interés calculados por el pipeline:
                            </p>
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 pt-2">
                                <div class="bg-white p-4 border border-brand-50/50 rounded-xl space-y-2">
                                    <span class="text-xs text-accent-600 font-bold">Tamaño de Perros:</span>
                                    <ul class="text-xs text-brand-900 font-medium space-y-1">
                                        @if(isset($stats['dog_sizes']) && is_array($stats['dog_sizes']))
                                            @foreach($stats['dog_sizes'] as $size => $count)
                                                <li>• {{ ucfirst($size) }}: <strong>{{ $count }}</strong></li>
                                            @endforeach
                                        @else
                                            <li>No hay datos</li>
                                        @endif
                                    </ul>
                                </div>
                                <div class="bg-white p-4 border border-brand-50/50 rounded-xl space-y-2">
                                    <span class="text-xs text-accent-600 font-bold">Estados de Petición:</span>
                                    <ul class="text-xs text-brand-900 font-medium space-y-1">
                                        @if(isset($stats['request_statuses']) && is_array($stats['request_statuses']))
                                            @foreach($stats['request_statuses'] as $status => $count)
                                                <li>• {{ $status == 'pending' ? 'Pendiente' : ($status == 'accepted' ? 'En Curso' : 'Finalizada') }}: <strong>{{ $count }}</strong></li>
                                            @endforeach
                                        @else
                                            <li>No hay datos</li>
                                        @endif
                                    </ul>
                                </div>
                                <div class="bg-white p-4 border border-brand-50/50 rounded-xl space-y-2">
                                    <span class="text-xs text-accent-600 font-bold">Estado de Fondos:</span>
                                    <ul class="text-xs text-brand-900 font-medium space-y-1">
                                        <li>• En Custodia: <strong>{{ number_format($stats['escrow_amount'] ?? 0, 2) }}€</strong></li>
                                        <li>• Liberado: <strong>{{ number_format($stats['released_amount'] ?? 0, 2) }}€</strong></li>
                                        <li>• Devuelto: <strong>{{ number_format($stats['refunded_amount'] ?? 0, 2) }}€</strong></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            @endif

            <!-- Quick Actions -->
            <div class="bg-white border border-brand-100/50 rounded-3xl p-8 shadow-md">
                <h3 class="font-black text-xl text-brand-900 mb-6">Acciones Rápidas</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    <!-- Action: Añadir Mascota -->
                    <a href="{{ route('dogs.create') }}" class="p-6 bg-accent-50/50 border border-brand-50/50 hover:border-brand-200 hover:bg-white hover:shadow-xl hover:shadow-brand-100/10 transition-all duration-200 rounded-2xl flex flex-col items-center text-center group">
                        <span class="text-3xl group-hover:scale-110 transition-transform duration-200">➕</span>
                        <span class="font-black text-sm text-brand-900 mt-4">Añadir Perro</span>
                        <span class="text-xs text-accent-600 font-semibold mt-1">Registra otra mascota</span>
                    </a>
                    <!-- Action: Crear Petición -->
                    <a href="{{ route('care-requests.create') }}" class="p-6 bg-accent-50/50 border border-brand-50/50 hover:border-brand-200 hover:bg-white hover:shadow-xl hover:shadow-brand-100/10 transition-all duration-200 rounded-2xl flex flex-col items-center text-center group">
                        <span class="text-3xl group-hover:scale-110 transition-transform duration-200">📤</span>
                        <span class="font-black text-sm text-brand-900 mt-4">Nueva Petición</span>
                        <span class="text-xs text-accent-600 font-semibold mt-1">Solicita un cuidador</span>
                    </a>
                    <!-- Action: Explorar -->
                    <a href="{{ route('care-requests.explore') }}" class="p-6 bg-accent-50/50 border border-brand-50/50 hover:border-brand-200 hover:bg-white hover:shadow-xl hover:shadow-brand-100/10 transition-all duration-200 rounded-2xl flex flex-col items-center text-center group">
                        <span class="text-3xl group-hover:scale-110 transition-transform duration-200">🔍</span>
                        <span class="font-black text-sm text-brand-900 mt-4">Explorar Peticiones</span>
                        <span class="text-xs text-accent-600 font-semibold mt-1">Buscar perros que cuidar</span>
                    </a>
                    <!-- Action: Cartera -->
                    <a href="{{ route('payments.wallet') }}" class="p-6 bg-accent-50/50 border border-brand-50/50 hover:border-brand-200 hover:bg-white hover:shadow-xl hover:shadow-brand-100/10 transition-all duration-200 rounded-2xl flex flex-col items-center text-center group">
                        <span class="text-3xl group-hover:scale-110 transition-transform duration-200">💰</span>
                        <span class="font-black text-sm text-brand-900 mt-4">Mi Cartera</span>
                        <span class="text-xs text-accent-600 font-semibold mt-1">Ver saldo y transacciones</span>
                    </a>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
