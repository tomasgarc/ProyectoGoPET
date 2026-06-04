<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <!-- SEO Meta Tags -->
        <title>GoPET - Cuidado de Perros con Confianza</title>
        <meta name="description" content="GoPET conecta a dueños de perros con cuidadores locales de total confianza. Con pagos protegidos en depósito de garantía y valoraciones verificadas.">
        <meta name="keywords" content="cuidado de perros, paseador de perros, gopet, alojamiento de mascotas, cuidadores de confianza, residencia canina">
        <meta name="author" content="GoPET Intermodular">
        <meta name="robots" content="index, follow">

        <!-- OpenGraph Protocol -->
        <meta property="og:title" content="GoPET - Cuidado de Perros con Confianza">
        <meta property="og:description" content="Encuentra cuidadores locales verificados para tus mascotas con la tranquilidad del pago en custodia segura.">
        <meta property="og:type" content="website">
        <meta property="og:url" content="{{ url('/') }}">
        <meta property="og:image" content="{{ asset('favicon.png') }}">

        <!-- Sindicación RSS -->
        <link rel="alternate" type="application/rss+xml" title="Feed de Peticiones Activas - GoPET" href="{{ route('feeds.care-requests') }}">

        <!-- Favicons -->
        <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
        <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
        <link rel="alternate icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-accent-50 text-accent-950">
        
        <!-- Header / Navigation -->
        <header class="w-full max-w-7xl mx-auto px-6 py-6 flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <x-application-logo class="w-10 h-10 text-brand-600" />
                <span class="text-2xl font-black text-brand-900 tracking-tight">GoPET</span>
            </div>
            
            @if (Route::has('login'))
                <nav class="flex items-center space-x-4">
                    @auth
                        <a href="{{ url('/dashboard') }}" class="inline-flex items-center px-5 py-2.5 bg-brand-600 hover:bg-brand-700 text-white font-bold text-sm rounded-2xl transition-all duration-200 hover:scale-[1.02] shadow-md shadow-brand-100/50">
                            {{ __('Dashboard') }}
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="text-sm font-bold text-accent-600 hover:text-brand-600 transition-colors">
                            {{ __('Iniciar Sesión') }}
                        </a>

                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="inline-flex items-center px-5 py-2.5 bg-brand-600 hover:bg-brand-700 text-white font-bold text-sm rounded-2xl transition-all duration-200 hover:scale-[1.02] shadow-md shadow-brand-100/50">
                                {{ __('Registrarse') }}
                            </a>
                        @endif
                    @endauth
                </nav>
            @endif
        </header>

        <!-- Hero Section -->
        <section class="max-w-7xl mx-auto px-6 py-12 lg:py-24 grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
            <div class="lg:col-span-7 space-y-6 text-left">
                <span class="inline-block px-4 py-1.5 bg-brand-100 text-brand-700 text-xs font-black uppercase tracking-widest rounded-full">
                    🐾 Comunidad de amantes de los perros
                </span>
                <h1 class="text-4xl sm:text-5xl lg:text-6xl font-black text-brand-900 tracking-tight leading-tight">
                    El cuidado de tu perro, <br>
                    <span class="text-brand-500">en manos de confianza</span>
                </h1>
                <p class="text-base sm:text-lg text-accent-600 font-medium leading-relaxed max-w-xl">
                    GoPET conecta a dueños de perros con cuidadores locales de total confianza. Con pagos protegidos en depósito y opiniones verificadas de la comunidad.
                </p>
                <div class="flex flex-wrap gap-4 pt-2">
                    @auth
                        <a href="{{ route('care-requests.explore') }}" class="px-6 py-3.5 bg-brand-200 hover:bg-brand-500 text-brand-900 hover:text-white font-bold text-base rounded-2xl shadow-lg shadow-brand-200/50 transition-all duration-200 hover:scale-[1.02]">
                            Explorar peticiones
                        </a>
                        <a href="{{ route('dogs.index') }}" class="px-6 py-3.5 bg-white border-2 border-brand-200 text-brand-700 hover:bg-brand-50/50 font-bold text-base rounded-2xl transition-all duration-200 hover:scale-[1.02]">
                            Gestionar mis perros
                        </a>
                    @else
                        <a href="{{ route('register') }}" class="px-8 py-3.5 bg-brand-600 hover:bg-brand-700 text-white font-bold text-base rounded-2xl shadow-lg shadow-brand-200/50 transition-all duration-200 hover:scale-[1.02]">
                            Crear cuenta gratis
                        </a>
                        <a href="{{ route('login') }}" class="px-8 py-3.5 bg-white border-2 border-brand-200 text-brand-700 hover:bg-brand-50/50 font-bold text-base rounded-2xl transition-all duration-200 hover:scale-[1.02]">
                            Ofrecerme como cuidador
                        </a>
                    @endauth
                </div>

                <!-- Mini Stats -->
                <div class="grid grid-cols-3 gap-6 pt-8 border-t border-brand-100 max-w-lg">
                    <div>
                        <span class="block text-3xl font-black text-brand-900">100%</span>
                        <span class="text-xs text-accent-600 font-bold">Seguridad Garantizada</span>
                    </div>
                    <div>
                        <span class="block text-3xl font-black text-brand-900">4.9★</span>
                        <span class="text-xs text-accent-600 font-bold">Valoración Media</span>
                    </div>
                    <div>
                        <span class="block text-3xl font-black text-brand-900">Rápido</span>
                        <span class="text-xs text-accent-600 font-bold">Chats en Tiempo Real</span>
                    </div>
                </div>
            </div>
            
            <div class="lg:col-span-5 relative flex justify-center">
                <!-- Decorative backgrounds -->
                <div class="absolute -inset-4 bg-gradient-to-tr from-brand-200/30 to-brand-100/50 rounded-full blur-2xl -z-10"></div>
                <div class="w-full max-w-[400px] aspect-[4/5] bg-gradient-to-br from-brand-600 to-brand-500 rounded-[2.5rem] shadow-2xl flex flex-col items-center justify-between p-8 text-white relative overflow-hidden">
                    
                    <!-- Decorative dog paw icon in background -->
                    <div class="absolute -right-16 -top-16 opacity-10">
                        <x-application-logo class="w-64 h-64" />
                    </div>

                    <!-- Heart Badge -->
                    <div class="self-end bg-white/20 backdrop-blur-md rounded-2xl px-4 py-2 flex items-center space-x-2 text-sm font-bold shadow-sm">
                        <span>💖</span>
                        <span>Cuidado de calidad</span>
                    </div>
                    
                    <!-- App Preview Card Inside Hero -->
                    <div class="w-full bg-white text-accent-950 rounded-2xl p-5 shadow-xl space-y-4 relative z-10 hover:translate-y-[-5px] transition-transform duration-300">
                        <div class="flex justify-between items-center">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-brand-50 text-xl rounded-full flex items-center justify-center">🐶</div>
                                <div>
                                    <h4 class="font-black text-sm text-brand-900 leading-tight">Toby</h4>
                                    <p class="text-[11px] text-accent-600 font-semibold">Golden Retriever • 2 años</p>
                                </div>
                            </div>
                            <span class="px-2.5 py-1 bg-brand-50 text-brand-700 text-[10px] font-black uppercase rounded-full">Mediano</span>
                        </div>
                        <div class="border-t border-brand-50 pt-3 flex justify-between items-center text-xs">
                            <span class="text-accent-600 font-bold">📅 15 Jun - 20 Jun</span>
                            <span class="text-brand-600 font-black text-sm">90€</span>
                        </div>
                    </div>
                    
                    <div class="text-center space-y-1 relative z-10">
                        <p class="font-black text-xl tracking-tight">Encuentra el cuidador ideal</p>
                        <p class="text-xs text-brand-100 font-medium">Filtra por fechas, razas y presupuestos</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Features Grid -->
        <section class="bg-white border-t border-brand-50 py-16 lg:py-24">
            <div class="max-w-7xl mx-auto px-6">
                <div class="text-center max-w-2xl mx-auto space-y-4 mb-16">
                    <span class="text-xs font-black uppercase text-brand-600 tracking-widest bg-brand-50 px-3 py-1 rounded-full">¿Cómo funciona GoPET?</span>
                    <h2 class="text-3xl sm:text-4xl font-black text-brand-900 tracking-tight">Todo lo necesario para tu mascota</h2>
                    <p class="text-accent-600 font-medium leading-relaxed">Diseñamos una plataforma cercana y transparente para asegurar que tu perro se sienta como en casa.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <!-- Feature 1 -->
                    <div class="p-8 bg-accent-50/50 border border-brand-50/50 rounded-3xl hover:border-brand-200/50 hover:bg-white hover:shadow-xl hover:shadow-brand-100/10 transition-all duration-300 group">
                        <div class="w-12 h-12 rounded-2xl bg-brand-50 flex items-center justify-center text-2xl group-hover:scale-110 transition-transform duration-300">
                            🐾
                        </div>
                        <h3 class="text-lg font-black text-brand-900 mt-6 mb-2">Perfiles Detallados</h3>
                        <p class="text-sm text-accent-600 font-medium leading-relaxed">
                            Registra las fotos de tus perritos, su raza, edad, tamaño y cuidados especiales para que los cuidadores estén al tanto de todo.
                        </p>
                    </div>

                    <!-- Feature 2 -->
                    <div class="p-8 bg-accent-50/50 border border-brand-50/50 rounded-3xl hover:border-brand-200/50 hover:bg-white hover:shadow-xl hover:shadow-brand-100/10 transition-all duration-300 group">
                        <div class="w-12 h-12 rounded-2xl bg-brand-50 flex items-center justify-center text-2xl group-hover:scale-110 transition-transform duration-300">
                            🔍
                        </div>
                        <h3 class="text-lg font-black text-brand-900 mt-6 mb-2">Explorador Flexible</h3>
                        <p class="text-sm text-accent-600 font-medium leading-relaxed">
                            Explora peticiones de cuidado activas o publica tu propia solicitud detallando las fechas y el presupuesto disponible.
                        </p>
                    </div>

                    <!-- Feature 3 -->
                    <div class="p-8 bg-accent-50/50 border border-brand-50/50 rounded-3xl hover:border-brand-200/50 hover:bg-white hover:shadow-xl hover:shadow-brand-100/10 transition-all duration-300 group">
                        <div class="w-12 h-12 rounded-2xl bg-brand-50 flex items-center justify-center text-2xl group-hover:scale-110 transition-transform duration-300">
                            🔒
                        </div>
                        <h3 class="text-lg font-black text-brand-900 mt-6 mb-2">Depósito de Garantía</h3>
                        <p class="text-sm text-accent-600 font-medium leading-relaxed">
                            El dinero se retiene de forma segura y solo se libera al cuidador una vez que finaliza el cuidado y confirmas tu satisfacción.
                        </p>
                    </div>

                    <!-- Feature 4 -->
                    <div class="p-8 bg-accent-50/50 border border-brand-50/50 rounded-3xl hover:border-brand-200/50 hover:bg-white hover:shadow-xl hover:shadow-brand-100/10 transition-all duration-300 group">
                        <div class="w-12 h-12 rounded-2xl bg-brand-50 flex items-center justify-center text-2xl group-hover:scale-110 transition-transform duration-300">
                            💬
                        </div>
                        <h3 class="text-lg font-black text-brand-900 mt-6 mb-2">Chat en Tiempo Real</h3>
                        <p class="text-sm text-accent-600 font-medium leading-relaxed">
                            Habla directamente con los cuidadores, resuelve dudas y acuerda los detalles de entrega cómodamente desde la web.
                        </p>
                    </div>

                    <!-- Feature 5 -->
                    <div class="p-8 bg-accent-50/50 border border-brand-50/50 rounded-3xl hover:border-brand-200/50 hover:bg-white hover:shadow-xl hover:shadow-brand-100/10 transition-all duration-300 group">
                        <div class="w-12 h-12 rounded-2xl bg-brand-50 flex items-center justify-center text-2xl group-hover:scale-110 transition-transform duration-300">
                            ⭐
                        </div>
                        <h3 class="text-lg font-black text-brand-900 mt-6 mb-2">Valoraciones y Reseñas</h3>
                        <p class="text-sm text-accent-600 font-medium leading-relaxed">
                            Califica tu experiencia y escribe reseñas después del servicio para ayudar a mantener el alto estándar de la comunidad.
                        </p>
                    </div>

                    <!-- Feature 6 -->
                    <div class="p-8 bg-accent-50/50 border border-brand-50/50 rounded-3xl hover:border-brand-200/50 hover:bg-white hover:shadow-xl hover:shadow-brand-100/10 transition-all duration-300 group">
                        <div class="w-12 h-12 rounded-2xl bg-brand-50 flex items-center justify-center text-2xl group-hover:scale-110 transition-transform duration-300">
                            💼
                        </div>
                        <h3 class="text-lg font-black text-brand-900 mt-6 mb-2">Cartera Integrada</h3>
                        <p class="text-sm text-accent-600 font-medium leading-relaxed">
                            Visualiza tus fondos, gestiona tus cobros de cuidados finalizados y solicita transferencias de forma rápida y sencilla.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA Section -->
        <section class="max-w-7xl mx-auto px-6 py-16 lg:py-24 text-center">
            <div class="bg-gradient-to-br from-brand-600 to-brand-700 text-white rounded-[2.5rem] p-8 sm:p-12 lg:p-20 relative overflow-hidden shadow-2xl">
                <!-- Background Paw -->
                <div class="absolute -left-20 -bottom-20 opacity-5">
                    <x-application-logo class="w-96 h-96" />
                </div>
                
                <div class="max-w-xl mx-auto space-y-6 relative z-10">
                    <h2 class="text-3xl sm:text-4xl lg:text-5xl font-black tracking-tight leading-tight">
                        ¿Listo para darles el mejor cuidado?
                    </h2>
                    <p class="text-brand-100 font-medium text-sm sm:text-base leading-relaxed">
                        Únete hoy mismo a miles de dueños y cuidadores en GoPET. Registro rápido, sin cuotas mensuales y 100% amigable.
                    </p>
                    <div class="pt-4">
                        @auth
                            <a href="{{ route('care-requests.create') }}" class="px-8 py-4 bg-white text-brand-700 font-bold text-base rounded-2xl shadow-xl hover:bg-brand-50 hover:scale-[1.02] active:scale-[0.98] transition-all duration-200 inline-block">
                                Publicar Petición Ahora
                            </a>
                        @else
                            <a href="{{ route('register') }}" class="px-8 py-4 bg-white text-brand-700 font-bold text-base rounded-2xl shadow-xl hover:bg-brand-50 hover:scale-[1.02] active:scale-[0.98] transition-all duration-200 inline-block">
                                Crear Cuenta Gratis
                            </a>
                        @endauth
                    </div>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer class="border-t border-brand-100 bg-white py-12 text-center text-accent-600 text-sm font-medium">
            <div class="max-w-7xl mx-auto px-6 flex flex-col sm:flex-row justify-between items-center gap-4">
                <div class="flex items-center space-x-2">
                    <x-application-logo class="w-6 h-6 text-brand-600" />
                    <span class="font-black text-brand-900 tracking-tight">GoPET</span>
                </div>
                <p>© {{ date('Y') }} GoPET. Todos los derechos reservados. Rediseñado con 💚 para ti.</p>
            </div>
        </footer>

    </body>
</html>
