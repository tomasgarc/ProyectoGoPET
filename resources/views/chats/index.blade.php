<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-brand-900 leading-tight">
            {{ __('Mensajes y Conversaciones') }}
        </h2>
    </x-slot>

    <div class="py-6 h-[calc(100vh-64px)] max-h-[850px]">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 h-full">
            <div class="bg-white overflow-hidden shadow-sm rounded-3xl border border-brand-100/50 h-full flex">
                
                <!-- Panel Izquierdo: Lista de Conversaciones -->
                <div class="w-full md:w-80 lg:w-96 border-r border-brand-100/50 flex flex-col flex-shrink-0 {{ $activeChat ? 'hidden md:flex' : 'flex' }}">
                    <div class="p-4 border-b border-brand-50 bg-accent-50/20 flex items-center justify-between">
                        <span class="font-black text-sm text-brand-900 uppercase tracking-wider">{{ __('Bandeja de Entrada') }}</span>
                        <span class="bg-brand-50 border border-brand-100 text-brand-700 font-bold text-xs py-0.5 px-2 rounded-full">
                            {{ $chats->count() }} {{ trans_choice('chat|chats', $chats->count()) }}
                        </span>
                    </div>

                    <!-- Lista de Chats -->
                    <div class="flex-grow overflow-y-auto divide-y divide-brand-50/50">
                        @forelse($chats as $chat)
                            @php
                                $partner = $chat->getPartner(auth()->id());
                                $latestMsg = $chat->latestMessage;
                                $unreadCount = $chat->unreadMessagesCount(auth()->id());
                                $isActive = $activeChat && $activeChat->id === $chat->id;
                            @endphp
                            <a href="{{ route('chats.index', ['chat' => $chat->id]) }}" 
                               class="block p-4 transition duration-150 hover:bg-accent-50/10 {{ $isActive ? 'bg-brand-50/40 border-l-4 border-brand-500' : '' }}">
                                <div class="flex items-start space-x-3">
                                    <!-- Avatar del Partner -->
                                    <img src="{{ $partner->avatar_url }}" alt="{{ $partner->name }}" class="w-10 h-10 rounded-full object-cover flex-shrink-0 border border-brand-100 shadow-sm">
                                    
                                    <!-- Información del Chat -->
                                    <div class="flex-grow min-w-0">
                                        <div class="flex items-center justify-between mb-1">
                                            <span class="font-bold text-sm text-brand-900 truncate block mr-2">{{ $partner->name }}</span>
                                            <span class="text-[10px] text-accent-500 font-semibold whitespace-nowrap">
                                                {{ $latestMsg ? $latestMsg->created_at->diffForHumans(null, true) : $chat->created_at->diffForHumans(null, true) }}
                                            </span>
                                        </div>
                                        
                                        <!-- Perros de la petición como contexto -->
                                        <div class="text-[10px] text-brand-600 font-black uppercase tracking-wider truncate mb-1">
                                            @if($chat->careRequest)
                                                🐾 
                                                @foreach($chat->careRequest->dogs as $dog)
                                                    {{ $dog->name }}{{ !$loop->last ? ', ' : '' }}
                                                @endforeach
                                                • {{ number_format($chat->careRequest->price, 0) }}€
                                            @else
                                                💬 {{ __('Chat Directo / Soporte') }}
                                            @endif
                                        </div>

                                        <!-- Último Mensaje o unread badge -->
                                        <div class="flex items-center justify-between">
                                            <p class="text-xs text-accent-600 truncate {{ $unreadCount > 0 ? 'font-black text-brand-900' : '' }}">
                                                @if($latestMsg)
                                                    {{ $latestMsg->sender_id === auth()->id() ? __('Tú: ') : '' }}{{ $latestMsg->content }}
                                                @else
                                                    <span class="italic text-accent-400">{{ __('No hay mensajes aún') }}</span>
                                                @endif
                                            </p>
                                            @if($unreadCount > 0)
                                                <span class="bg-brand-600 text-white font-black text-[9px] py-0.5 px-1.5 rounded-full ml-2 flex-shrink-0 leading-none">
                                                    {{ $unreadCount }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </a>
                        @empty
                            <div class="text-center py-16 text-accent-400 space-y-3">
                                <span class="text-5xl block">💬</span>
                                <p class="text-sm font-bold text-brand-900">{{ __('No tienes conversaciones activas.') }}</p>
                                <p class="text-xs text-accent-600 font-medium">{{ __('Contacta con dueños desde la sección explorar.') }}</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Panel Derecho: Conversación Activa -->
                <div class="flex-grow flex flex-col h-full bg-accent-50/10 {{ !$activeChat ? 'hidden md:flex items-center justify-center text-center p-8' : 'flex' }}">
                    
                    @if($activeChat)
                        @php
                            $partner = $activeChat->getPartner(auth()->id());
                            $careRequest = $activeChat->careRequest;
                        @endphp
                        
                        <!-- Header del Chat -->
                        <div class="p-4 border-b border-brand-50 bg-white flex flex-col lg:flex-row justify-between lg:items-center gap-4 flex-shrink-0 shadow-sm z-10">
                            <div class="flex items-center space-x-3">
                                <!-- Botón Atrás (Solo móvil) -->
                                <a href="{{ route('chats.index') }}" class="md:hidden p-1 text-accent-600 hover:text-brand-600 transition-colors mr-1">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                                    </svg>
                                </a>
                                <img src="{{ $partner->avatar_url }}" alt="{{ $partner->name }}" class="w-10 h-10 rounded-full object-cover flex-shrink-0 border border-brand-100 shadow-sm">
                                <div>
                                    <h4 class="font-black text-brand-900 text-sm leading-tight">{{ $partner->name }}</h4>
                                    <p class="text-[10px] text-brand-600 font-bold uppercase tracking-wider mt-0.5 flex items-center">
                                        <span class="w-1.5 h-1.5 rounded-full bg-brand-500 inline-block mr-1 animate-pulse"></span>
                                        {{ __('En conversación') }}
                                    </p>
                                </div>
                            </div>

                            <!-- Contexto / Detalles de la Petición y Botón de Aceptar -->
                            <div class="flex flex-wrap items-center gap-3">
                                @if($careRequest)
                                    <div class="bg-accent-50/50 border border-brand-100/50 px-3.5 py-1.5 rounded-2xl text-xs flex items-center space-x-2">
                                        <span class="font-bold text-accent-600">🐾 Petición:</span>
                                        <span class="text-brand-700 font-black">
                                            @foreach($careRequest->dogs as $dog)
                                                {{ $dog->name }}{{ !$loop->last ? ', ' : '' }}
                                            @endforeach
                                        </span>
                                        <span class="text-brand-200">|</span>
                                        <span class="text-brand-600 font-black">{{ number_format($careRequest->price, 0) }}€</span>
                                    </div>

                                    <a href="{{ route('care-requests.show', $careRequest) }}" class="inline-flex items-center px-5 py-2 bg-white hover:bg-brand-50/50 text-brand-750 border border-brand-200 rounded-xl text-xs font-bold transition shadow-sm">
                                        {{ __('Ver Petición') }}
                                    </a>

                                    <!-- ACCIÓN PREMIUM: Elegir como Cuidador desde el Chat -->
                                    @if($careRequest->user_id === auth()->id() && $careRequest->status === 'pending' && !$careRequest->isFinalized())
                                        <form action="{{ route('care-requests.accept', $careRequest) }}" method="POST" class="inline">
                                            @csrf
                                            <input type="hidden" name="accepted_by" value="{{ $partner->id }}">
                                            <button type="submit" class="inline-flex items-center px-5 py-2 bg-brand-200 hover:bg-brand-500 text-brand-900 hover:text-white rounded-xl text-xs font-bold transition shadow-sm uppercase tracking-wider hover:scale-[1.02] active:scale-[0.98]" onclick="return confirm('¿Confirmas que deseas seleccionar a {{ $partner->name }} como cuidador para tus perros?')">
                                                🤝 {{ __('Elegir Cuidador') }}
                                            </button>
                                        </form>
                                    @endif
                                @else
                                    <div class="bg-brand-50/50 border border-brand-100/50 px-3.5 py-1.5 rounded-2xl text-xs flex items-center space-x-2">
                                        <span class="font-bold text-brand-600">💬 Modo:</span>
                                        <span class="text-brand-700 font-black">Chat Directo</span>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Área de Mensajes -->
                        <div class="flex-grow overflow-y-auto p-4 space-y-4" id="chat-messages-container">
                            @forelse($activeChat->messages as $msg)
                                @php
                                    $isMine = $msg->sender_id === auth()->id();
                                @endphp
                                <div class="flex {{ $isMine ? 'justify-end' : 'justify-start' }}">
                                    <div class="flex items-end space-x-2 max-w-[75%]">
                                        @if(!$isMine)
                                            <img src="{{ $msg->sender->avatar_url }}" alt="{{ $msg->sender->name }}" class="w-7 h-7 rounded-full object-cover flex-shrink-0 border border-brand-100 shadow-sm">
                                        @endif
                                        <div class="flex flex-col {{ $isMine ? 'items-end' : 'items-start' }}">
                                            <div class="p-3 shadow-sm rounded-3xl text-sm leading-relaxed
                                                {{ $isMine ? 'bg-brand-600 text-white rounded-br-none' : 'bg-white text-accent-950 border border-brand-100/60 rounded-bl-none' }}">
                                                <p class="whitespace-pre-wrap font-medium">{{ $msg->content }}</p>
                                            </div>
                                            <span class="text-[9px] text-accent-400 font-semibold mt-1">
                                                {{ $msg->created_at->format('H:i') }}
                                                @if($isMine)
                                                    • {{ $msg->read_at ? __('Leído') : __('Entregado') }}
                                                @endif
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-16 text-accent-400 space-y-3 max-w-xs mx-auto">
                                    <span class="text-5xl block">👋</span>
                                    <p class="font-bold text-brand-900">{{ __('¡Saluda a tu compañero!') }}</p>
                                    <p class="text-xs text-accent-600 font-medium leading-relaxed">{{ __('Escribe un mensaje para presentarte, hablar de la zona de cuidado o concretar detalles del servicio.') }}</p>
                                </div>
                            @endforelse
                        </div>

                        <!-- Formulario de Mensaje -->
                        <div class="p-4 bg-white border-t border-brand-50 flex-shrink-0">
                            @if($careRequest && $careRequest->isFinalized())
                                <div class="bg-accent-50 text-accent-600 text-center py-2 px-4 rounded-xl text-xs font-bold italic">
                                    🔒 {{ __('Esta conversación está cerrada porque la petición ha finalizado.') }}
                                </div>
                            @else
                                <form action="{{ route('chats.messages.store', $activeChat) }}" method="POST" class="flex items-center gap-3">
                                    @csrf
                                    <div class="flex-grow">
                                        <input type="text" id="message-input" name="content" placeholder="{{ __('Escribe un mensaje...') }}" class="block w-full border-brand-200/80 focus:border-brand-500 focus:ring-brand-500/20 text-xs font-semibold py-2.5 px-4 rounded-2xl bg-white text-accent-950" required autocomplete="off" autofocus>
                                    </div>
                                    <button type="submit" class="inline-flex items-center justify-center p-2.5 bg-brand-600 hover:bg-brand-700 text-white rounded-2xl shadow-sm transition-all duration-150 hover:scale-[1.03] flex-shrink-0">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                                        </svg>
                                    </button>
                                </form>
                            @endif
                        </div>
                        
                        <!-- Scroll to bottom on load -->
                        <script>
                            document.addEventListener('DOMContentLoaded', function () {
                                const container = document.getElementById('chat-messages-container');
                                if (container) {
                                    container.scrollTop = container.scrollHeight;
                                }
                                
                                const input = document.getElementById('message-input');
                                if (input) {
                                    input.focus();
                                }
                            });
                        </script>
                    @else
                        <!-- Estado vacío para chat derecho -->
                        <div class="text-center p-8 space-y-4 max-w-sm mx-auto">
                            <span class="text-7xl block">💬</span>
                            <h3 class="text-xl font-black text-brand-900 uppercase tracking-wider leading-none">{{ __('Mensajes de GoPET') }}</h3>
                            <p class="text-accent-600 text-sm font-medium leading-relaxed">
                                {{ __('Selecciona una conversación de tu bandeja de entrada para empezar a hablar de las zonas de cuidado, acordar precios y concretar detalles.') }}
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
