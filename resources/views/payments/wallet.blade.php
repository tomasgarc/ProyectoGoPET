<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-brand-900 leading-tight">
            {{ __('Mi Cartera') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-accent-50/50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if(session('success'))
                <div class="bg-brand-50 border border-brand-200 text-brand-900 px-4 py-3 rounded-2xl relative mb-6 flex items-center shadow-sm" role="alert">
                    <span class="mr-2">✨</span>
                    <span class="font-bold text-sm">{{ session('success') }}</span>
                </div>
            @endif

            <!-- Grid de Tarjetas de Balance -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                
                <!-- Tarjeta 1: Saldo Disponible -->
                <div class="bg-gradient-to-br from-brand-600 to-brand-700 rounded-3xl p-6 shadow-lg shadow-brand-200/50 relative overflow-hidden flex flex-col justify-between h-40 text-white">
                    <div class="absolute -right-6 -bottom-6 w-24 h-24 bg-white/5 rounded-full blur-xl"></div>
                    <div class="flex justify-between items-start relative z-10">
                        <div>
                            <p class="text-xs uppercase text-brand-100 font-bold tracking-wider">{{ __('Saldo Disponible') }}</p>
                            <p class="text-3xl font-black mt-2 font-mono text-white">{{ number_format($availableBalance, 2) }}€</p>
                        </div>
                        <span class="text-2xl bg-white/20 p-2.5 rounded-2xl text-white">💰</span>
                    </div>
                    <p class="text-[10px] text-brand-100 font-semibold border-t border-white/10 pt-2 relative z-10">
                        {{ __('Fondos liberados listos para retirar o transferir.') }}
                    </p>
                </div>

                <!-- Tarjeta 2: Saldo en Depósito (Escrow) -->
                <div class="bg-white rounded-3xl p-6 border border-brand-100/50 shadow-md relative overflow-hidden flex flex-col justify-between h-40">
                    <div class="absolute -right-6 -bottom-6 w-24 h-24 bg-amber-500/5 rounded-full blur-xl"></div>
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-xs uppercase text-accent-500 font-black tracking-wider">{{ __('Retenido en Garantía') }}</p>
                            <p class="text-3xl font-black text-amber-600 mt-2 font-mono">{{ number_format($escrowBalance, 2) }}€</p>
                        </div>
                        <span class="text-2xl bg-amber-50 p-2.5 rounded-2xl text-amber-600">🔒</span>
                    </div>
                    <p class="text-[10px] text-accent-550 font-bold border-t border-brand-50 pt-2">
                        {{ __('Fondos en depósito de seguridad hasta finalizar los cuidados.') }}
                    </p>
                </div>

                <!-- Tarjeta 3: Total Gastado -->
                <div class="bg-white rounded-3xl p-6 border border-brand-100/50 shadow-md relative overflow-hidden flex flex-col justify-between h-40">
                    <div class="absolute -right-6 -bottom-6 w-24 h-24 bg-brand-500/5 rounded-full blur-xl"></div>
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-xs uppercase text-accent-500 font-black tracking-wider">{{ __('Total Gastado') }}</p>
                            <p class="text-3xl font-black text-brand-900 mt-2 font-mono">{{ number_format($totalSpent, 2) }}€</p>
                        </div>
                        <span class="text-2xl bg-brand-50 p-2.5 rounded-2xl text-brand-700">📊</span>
                    </div>
                    <p class="text-[10px] text-accent-550 font-bold border-t border-brand-50 pt-2">
                        {{ __('Inversión total realizada para el cuidado de tus perros.') }}
                    </p>
                </div>

            </div>

            <!-- Tabla de Historial de Transacciones -->
            <div class="bg-white rounded-3xl border border-brand-100/50 shadow-md overflow-hidden">
                <div class="p-6 border-b border-brand-50">
                    <h3 class="font-black text-base text-brand-900 uppercase tracking-wider">{{ __('Historial de Transacciones') }}</h3>
                    <p class="text-xs text-accent-550 mt-0.5">{{ __('Listado completo de cobros, pagos y reembolsos.') }}</p>
                </div>

                @if($transactions->isEmpty())
                    <div class="p-12 text-center text-accent-400 space-y-3">
                        <span class="text-5xl block">💸</span>
                        <p class="font-black text-brand-900 text-sm">{{ __('Aún no tienes movimientos registrados.') }}</p>
                        <p class="text-xs text-accent-600 font-medium">{{ __('Realiza un pago o acepta un servicio para registrar movimientos.') }}</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-accent-50/50 border-b border-brand-50 text-[10px] uppercase font-black text-accent-600 tracking-wider">
                                    <th class="py-4 px-6">{{ __('Fecha') }}</th>
                                    <th class="py-4 px-6">{{ __('ID Transacción') }}</th>
                                    <th class="py-4 px-6">{{ __('Concepto / Detalles') }}</th>
                                    <th class="py-4 px-6 text-center">{{ __('Estado') }}</th>
                                    <th class="py-4 px-6 text-right">{{ __('Importe') }}</th>
                                    <th class="py-4 px-6 text-center">{{ __('Acción') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-brand-50/50 text-sm font-semibold text-accent-950">
                                @foreach($transactions as $tx)
                                    @php
                                        $isPayer = $tx->user_id === auth()->id();
                                        $dogsList = $tx->careRequest ? $tx->careRequest->dogs->pluck('name')->join(', ') : 'Mascotas';
                                    @endphp
                                    <tr class="hover:bg-accent-50/20 transition duration-150">
                                        <!-- Fecha -->
                                        <td class="py-4.5 px-6 text-xs text-accent-600 font-mono">
                                            {{ $tx->created_at->format('d/m/Y H:i') }}
                                        </td>
                                        
                                        <!-- ID Transacción -->
                                        <td class="py-4.5 px-6 font-mono text-xs text-accent-400">
                                            {{ $tx->transaction_id }}
                                        </td>
                                        
                                        <!-- Concepto -->
                                        <td class="py-4.5 px-6">
                                            @if($isPayer)
                                                <div class="flex items-center space-x-2">
                                                    <span class="px-2.5 py-0.5 bg-rose-50 text-rose-700 text-[9px] uppercase font-black rounded-full border border-rose-100">{{ __('Pago') }}</span>
                                                    <span>
                                                        {{ __('Cuidado de ') }}<span class="font-black text-brand-900">{{ $dogsList }}</span> 
                                                        {{ __(' por ') }}<span class="font-black text-brand-900">{{ $tx->receiver->name }}</span>
                                                    </span>
                                                </div>
                                            @else
                                                <div class="flex items-center space-x-2">
                                                    <span class="px-2.5 py-0.5 bg-brand-50 text-brand-700 text-[9px] uppercase font-black rounded-full border border-brand-100">{{ __('Ingreso') }}</span>
                                                    <span>
                                                        {{ __('Cuidado de ') }}<span class="font-black text-brand-900">{{ $dogsList }}</span> 
                                                        {{ __(' de ') }}<span class="font-black text-brand-900">{{ $tx->user->name }}</span>
                                                    </span>
                                                </div>
                                                <div class="text-[10px] text-accent-500 mt-0.5 font-bold ml-14">
                                                    {{ __('(Comisión de plataforma del 10% incluida: -') }}{{ number_format($tx->fee, 2) }}{{ __('€)') }}
                                                </div>
                                            @endif
                                        </td>

                                        <!-- Estado -->
                                        <td class="py-4.5 px-6 text-center">
                                            @if($tx->status === 'escrow')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-50 text-amber-800 border border-amber-200/50">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500 mr-1.5"></span>
                                                    {{ __('En depósito') }}
                                                </span>
                                            @elseif($tx->status === 'released')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-brand-50 text-brand-850 border border-brand-100">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-brand-500 mr-1.5"></span>
                                                    {{ __('Disponible') }}
                                                </span>
                                            @elseif($tx->status === 'refunded')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-rose-50 text-rose-800 border border-rose-200">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-rose-500 mr-1.5"></span>
                                                    {{ __('Reembolsado') }}
                                                </span>
                                            @endif
                                        </td>

                                        <!-- Importe -->
                                        <td class="py-4.5 px-6 text-right font-mono text-base font-black">
                                            @if($isPayer)
                                                <span class="text-rose-600">-{{ number_format($tx->amount, 2) }}€</span>
                                            @else
                                                <span class="text-brand-600">+{{ number_format($tx->net_amount, 2) }}€</span>
                                            @endif
                                        </td>

                                        <!-- Acción -->
                                        <td class="py-4.5 px-6 text-center">
                                            @if($tx->careRequest)
                                                <a href="{{ route('care-requests.show', $tx->careRequest) }}" class="text-brand-600 hover:text-brand-800 hover:underline text-xs font-black uppercase tracking-wider">
                                                    {{ __('Detalles') }}
                                                </a>
                                            @else
                                                <span class="text-accent-400 text-xs italic">{{ __('Eliminada') }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
