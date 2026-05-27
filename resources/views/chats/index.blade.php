<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Mensajes y Conversaciones') }}
        </h2>
    </x-slot>

    <div class="py-6 h-[calc(100vh-64px)] max-h-[850px]">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 h-full">
            <div class="bg-white overflow-hidden shadow-sm rounded-lg border border-gray-100 h-full flex">
                
                <!-- Panel Izquierdo: Lista de Conversaciones -->
                <div class="w-full md:w-80 lg:w-96 border-r border-gray-150 flex flex-col flex-shrink-0 {{ $activeChat ? 'hidden md:flex' : 'flex' }}">
                    <div class="p-4 border-b border-gray-100 bg-gray-50/50 flex items-center justify-between">
                        <span class="font-black text-lg text-gray-800 uppercase tracking-wider">{{ __('Bandeja de Entrada') }}</span>
                        <span class="bg-indigo-100 text-indigo-700 font-bold text-xs py-0.5 px-2 rounded-full">
                            {{ $chats->count() }} {{ trans_choice('chat|chats', $chats->count()) }}
                        </span>
                    </div>

                    <!-- Lista de Chats -->
                    <div class="flex-grow overflow-y-auto divide-y divide-gray-50">
                        @forelse($chats as $chat)
                            @php
                                $partner = $chat->getPartner(auth()->id());
                                $latestMsg = $chat->latestMessage;
                                $unreadCount = $chat->unreadMessagesCount(auth()->id());
                                $isActive = $activeChat && $activeChat->id === $chat->id;
                            @endphp
                            <a href="{{ route('chats.index', ['chat' => $chat->id]) }}" 
                               class="block p-4 transition duration-150 hover:bg-gray-50/80 {{ $isActive ? 'bg-indigo-50/40 border-l-4 border-indigo-600' : '' }}">
                                <div class="flex items-start space-x-3">
                                    <!-- Avatar del Partner -->
                                    <div class="w-10 h-10 rounded-full flex-shrink-0 flex items-center justify-center font-bold text-sm
                                        {{ $chat->creator_id === auth()->id() ? 'bg-emerald-100 text-emerald-800' : 'bg-indigo-100 text-indigo-700' }}">
                                        {{ substr($partner->name, 0, 1) }}
                                    </div>
                                    
                                    <!-- Información del Chat -->
                                    <div class="flex-grow min-w-0">
                                        <div class="flex items-center justify-between mb-1">
                                            <span class="font-bold text-sm text-gray-900 truncate block mr-2">{{ $partner->name }}</span>
                                            <span class="text-[10px] text-gray-400 font-semibold whitespace-nowrap">
                                                {{ $latestMsg ? $latestMsg->created_at->diffForHumans(null, true) : $chat->created_at->diffForHumans(null, true) }}
                                            </span>
                                        </div>
                                        
                                        <!-- Perros de la petición como contexto -->
                                        <div class="text-[10px] text-indigo-600 font-black uppercase tracking-wider truncate mb-1">
                                            🐾 
                                            @foreach($chat->careRequest->dogs as $dog)
                                                {{ $dog->name }}{{ !$loop->last ? ', ' : '' }}
                                            @endforeach
                                            • {{ number_format($chat->careRequest->price, 0) }}€
                                        </div>

                                        <!-- Último Mensaje o unread badge -->
                                        <div class="flex items-center justify-between">
                                            <p class="text-xs text-gray-500 truncate {{ $unreadCount > 0 ? 'font-bold text-gray-900' : '' }}">
                                                @if($latestMsg)
                                                    {{ $latestMsg->sender_id === auth()->id() ? __('Tú: ') : '' }}{{ $latestMsg->content }}
                                                @else
                                                    <span class="italic text-gray-400">{{ __('No hay mensajes aún') }}</span>
                                                @endif
                                            </p>
                                            @if($unreadCount > 0)
                                                <span class="bg-indigo-600 text-white font-black text-[10px] py-0.5 px-1.5 rounded-full ml-2 flex-shrink-0">
                                                    {{ $unreadCount }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </a>
                        @empty
                            <div class="text-center py-12 text-gray-400">
                                <span class="text-4xl block mb-2">💬</span>
                                <p class="text-sm font-semibold">{{ __('No tienes conversaciones activas.') }}</p>
                                <p class="text-xs text-gray-400 mt-1">{{ __('Contacta con dueños desde la sección explorar.') }}</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Panel Derecho: Conversación Activa -->
                <div class="flex-grow flex flex-col h-full bg-gray-50/30 {{ !$activeChat ? 'hidden md:flex items-center justify-center text-center p-8' : 'flex' }}">
                    
                    @if($activeChat)
                        @php
                            $partner = $activeChat->getPartner(auth()->id());
                            $careRequest = $activeChat->careRequest;
                        @endphp
                        
                        <!-- Header del Chat -->
                        <div class="p-4 border-b border-gray-150 bg-white flex flex-col sm:flex-row justify-between sm:items-center gap-4 flex-shrink-0 shadow-sm z-10">
                            <div class="flex items-center space-x-3">
                                <!-- Botón Atrás (Solo móvil) -->
                                <a href="{{ route('chats.index') }}" class="md:hidden p-1 text-gray-500 hover:text-indigo-600 transition-colors mr-1">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                                    </svg>
                                </a>
                                <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-sm bg-indigo-600 text-white shadow-sm flex-shrink-0">
                                    {{ substr($partner->name, 0, 1) }}
                                </div>
                                <div>
                                    <h4 class="font-bold text-gray-900 text-sm leading-tight">{{ $partner->name }}</h4>
                                    <p class="text-[10px] text-emerald-600 font-bold uppercase tracking-wider mt-0.5 flex items-center">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 inline-block mr-1"></span>
                                        {{ __('En conversación') }}
                                    </p>
                                </div>
                            </div>

                            <!-- Contexto / Detalles de la Petición y Botón de Aceptar -->
                            <div class="flex flex-wrap items-center gap-3">
                                <div class="bg-gray-50 border border-gray-150 px-3 py-1.5 rounded text-xs flex items-center space-x-2">
                                    <span class="font-bold text-gray-700">🐾 Petición:</span>
                                    <span class="text-indigo-700 font-black">
                                        @foreach($careRequest->dogs as $dog)
                                            {{ $dog->name }}{{ !$loop->last ? ', ' : '' }}
                                        @endforeach
                                    </span>
                                    <span class="text-gray-300">|</span>
                                    <span class="text-indigo-600 font-bold">{{ number_format($careRequest->price, 0) }}€</span>
                                </div>

                                <a href="{{ route('care-requests.show', $careRequest) }}" class="inline-flex items-center px-3 py-1.5 bg-white hover:bg-gray-50 text-gray-700 border border-gray-200 rounded text-xs font-bold transition shadow-sm">
                                    {{ __('Ver Petición') }}
                                </a>

                                <!-- ACCIÓN PREMIUM: Elegir como Cuidador desde el Chat -->
                                @if($careRequest->user_id === auth()->id() && $careRequest->status === 'pending' && !$careRequest->isFinalized())
                                    <form action="{{ route('care-requests.accept', $careRequest) }}" method="POST" class="inline">
                                        @csrf
                                        <input type="hidden" name="accepted_by" value="{{ $partner->id }}">
                                        <button type="submit" class="inline-flex items-center px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded text-xs font-bold transition shadow-md uppercase tracking-wider" onclick="return confirm('¿Confirmas que deseas seleccionar a {{ $partner->name }} como cuidador para tus perros?')">
                                            🤝 {{ __('Elegir como Cuidador') }}
                                        </button>
                                    </form>
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
                                            <div class="w-7 h-7 rounded-full bg-indigo-100 text-indigo-700 flex items-center justify-center font-bold text-[10px] flex-shrink-0">
                                                {{ substr($msg->sender->name, 0, 1) }}
                                            </div>
                                        @endif
                                        <div class="flex flex-col {{ $isMine ? 'items-end' : 'items-start' }}">
                                            <div class="p-3 rounded-lg text-sm shadow-sm
                                                {{ $isMine ? 'bg-indigo-600 text-white rounded-br-none' : 'bg-white text-gray-800 border border-gray-150 rounded-bl-none' }}">
                                                <p class="whitespace-pre-wrap leading-relaxed">{{ $msg->content }}</p>
                                            </div>
                                            <span class="text-[9px] text-gray-400 font-semibold mt-1">
                                                {{ $msg->created_at->format('H:i') }}
                                                @if($isMine)
                                                    • {{ $msg->read_at ? __('Leído') : __('Entregado') }}
                                                @endif
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-16 text-gray-400">
                                    <span class="text-5xl block mb-3">👋</span>
                                    <p class="font-bold text-gray-700">{{ __('¡Saluda a tu compañero!') }}</p>
                                    <p class="text-xs text-gray-400 mt-1 max-w-xs mx-auto">{{ __('Escribe un mensaje para presentarte, hablar de la zona de cuidado o concretar detalles del servicio.') }}</p>
                                </div>
                            @endforelse
                        </div>

                        <!-- Formulario de Mensaje -->
                        <div class="p-4 bg-white border-t border-gray-150 flex-shrink-0">
                            @if($careRequest->isFinalized())
                                <div class="bg-gray-100 text-gray-500 text-center py-2 px-4 rounded text-xs font-bold italic">
                                    🔒 {{ __('Esta conversación está cerrada porque la petición ha finalizado.') }}
                                </div>
                            @else
                                <form action="{{ route('chats.messages.store', $activeChat) }}" method="POST" class="flex items-center gap-3">
                                    @csrf
                                    <div class="flex-grow">
                                        <input type="text" name="content" placeholder="{{ __('Escribe un mensaje...') }}" class="block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm font-semibold py-2.5 px-4 rounded-md" required autocomplete="off">
                                    </div>
                                    <button type="submit" class="inline-flex items-center justify-center p-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-md transition shadow-md flex-shrink-0">
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
                            });
                        </script>
                    @else
                        <!-- Estado vacío para chat derecho -->
                        <div class="text-center">
                            <span class="text-7xl block mb-4">💬</span>
                            <h3 class="text-xl font-black text-gray-800 uppercase tracking-wider mb-2">{{ __('Mensajes de GoPET') }}</h3>
                            <p class="text-gray-500 max-w-sm mx-auto text-sm">
                                {{ __('Selecciona una conversación del panel de la izquierda para empezar a hablar de las zonas de cuidado, acordar precios y concretar detalles.') }}
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
