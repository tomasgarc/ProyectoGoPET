<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-brand-900 leading-tight">
            {{ __('Panel de Control') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            
            <!-- Welcome Banner -->
            <div class="bg-gradient-to-r from-brand-600 to-brand-700 text-white rounded-3xl p-8 shadow-xl shadow-brand-100/30 relative overflow-hidden">
                <!-- SVG logo path as background decoration -->
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

            <!-- Stats Grid -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Stat 1: Mis Perros -->
                <div class="bg-white border border-brand-100/50 p-6 rounded-3xl shadow-md flex items-center space-x-4">
                    <div class="w-12 h-12 bg-brand-50 rounded-2xl flex items-center justify-center text-2xl">🐕</div>
                    <div>
                        <span class="block text-2xl font-black text-brand-900">{{ Auth::user()->dogs->count() }}</span>
                        <span class="text-xs text-accent-600 font-bold uppercase tracking-wider">Perros Registrados</span>
                    </div>
                </div>
                <!-- Stat 2: Mis Peticiones -->
                <div class="bg-white border border-brand-100/50 p-6 rounded-3xl shadow-md flex items-center space-x-4">
                    <div class="w-12 h-12 bg-brand-50 rounded-2xl flex items-center justify-center text-2xl">📋</div>
                    <div>
                        <span class="block text-2xl font-black text-brand-900">{{ Auth::user()->careRequests->count() }}</span>
                        <span class="text-xs text-accent-600 font-bold uppercase tracking-wider">Mis Solicitudes</span>
                    </div>
                </div>
                <!-- Stat 3: Favoritos -->
                <div class="bg-white border border-brand-100/50 p-6 rounded-3xl shadow-md flex items-center space-x-4">
                    <div class="w-12 h-12 bg-brand-50 rounded-2xl flex items-center justify-center text-2xl">⭐</div>
                    <div>
                        <span class="block text-2xl font-black text-brand-900">{{ Auth::user()->favoriteCareRequests->count() }}</span>
                        <span class="text-xs text-accent-600 font-bold uppercase tracking-wider">Peticiones Favoritas</span>
                    </div>
                </div>
            </div>

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
