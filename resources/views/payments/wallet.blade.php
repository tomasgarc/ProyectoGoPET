<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-800 leading-tight">
            {{ __('Mi Cartera') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-slate-50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if(session('success'))
                <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-lg relative mb-6 flex items-center shadow-sm" role="alert">
                    <svg class="w-5 h-5 mr-2 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span class="block sm:inline font-medium text-sm">{{ session('success') }}</span>
                </div>
            @endif

            <!-- Grid de Tarjetas de Balance -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                
                <!-- Tarjeta 1: Saldo Disponible -->
                <div class="bg-white rounded-2xl p-6 border border-slate-100 shadow-sm relative overflow-hidden flex flex-col justify-between h-40">
                    <div class="absolute -right-6 -bottom-6 w-24 h-24 bg-emerald-500/5 rounded-full blur-xl"></div>
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-xs uppercase text-slate-400 font-black tracking-wider">{{ __('Saldo Disponible') }}</p>
                            <p class="text-3xl font-black text-emerald-600 mt-2 font-mono">{{ number_format($availableBalance, 2) }}€</p>
                        </div>
                        <span class="text-2xl bg-emerald-50 p-2 rounded-xl text-emerald-600">💰</span>
                    </div>
                    <p class="text-[10px] text-slate-400 font-bold border-t border-slate-50 pt-2">
                        {{ __('Fondos liberados listos para retirar o transferir.') }}
                    </p>
                </div>

                <!-- Tarjeta 2: Saldo en Depósito (Escrow) -->
                <div class="bg-white rounded-2xl p-6 border border-slate-100 shadow-sm relative overflow-hidden flex flex-col justify-between h-40">
                    <div class="absolute -right-6 -bottom-6 w-24 h-24 bg-amber-500/5 rounded-full blur-xl"></div>
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-xs uppercase text-slate-400 font-black tracking-wider">{{ __('Retenido en Garantía') }}</p>
                            <p class="text-3xl font-black text-amber-600 mt-2 font-mono">{{ number_format($escrowBalance, 2) }}€</p>
                        </div>
                        <span class="text-2xl bg-amber-50 p-2 rounded-xl text-amber-600">🔒</span>
                    </div>
                    <p class="text-[10px] text-slate-400 font-bold border-t border-slate-50 pt-2">
                        {{ __('Fondos en depósito de seguridad hasta finalizar los cuidados.') }}
                    </p>
                </div>

                <!-- Tarjeta 3: Total Gastado -->
                <div class="bg-white rounded-2xl p-6 border border-slate-100 shadow-sm relative overflow-hidden flex flex-col justify-between h-40">
                    <div class="absolute -right-6 -bottom-6 w-24 h-24 bg-indigo-500/5 rounded-full blur-xl"></div>
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-xs uppercase text-slate-400 font-black tracking-wider">{{ __('Total Gastado') }}</p>
                            <p class="text-3xl font-black text-indigo-700 mt-2 font-mono">{{ number_format($totalSpent, 2) }}€</p>
                        </div>
                        <span class="text-2xl bg-indigo-50 p-2 rounded-xl text-indigo-600">📊</span>
                    </div>
                    <p class="text-[10px] text-slate-400 font-bold border-t border-slate-50 pt-2">
                        {{ __('Inversión total realizada para el cuidado de tus perros.') }}
                    </p>
                </div>

            </div>

            <!-- Tabla de Historial de Transacciones -->
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                <div class="p-6 border-b border-slate-50">
                    <h3 class="font-black text-base text-slate-800 uppercase tracking-wider">{{ __('Historial de Transacciones') }}</h3>
                    <p class="text-xs text-slate-400 mt-0.5">{{ __('Listado completo de cobros, pagos y reembolsos.') }}</p>
                </div>

                @if($transactions->isEmpty())
                    <div class="p-12 text-center text-slate-400">
                        <span class="text-4xl block mb-3">💸</span>
                        <p class="font-bold text-sm">{{ __('Aún no tienes movimientos registrados en tu cuenta.') }}</p>
                        <p class="text-xs mt-1 text-slate-400/80">{{ __('Realiza un pago o acepta un servicio para registrar movimientos.') }}</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-slate-50/50 border-b border-slate-100 text-[10px] uppercase font-black text-slate-400 tracking-wider">
                                    <th class="py-4 px-6">{{ __('Fecha') }}</th>
                                    <th class="py-4 px-6">{{ __('ID Transacción') }}</th>
                                    <th class="py-4 px-6">{{ __('Concepto / Detalles') }}</th>
                                    <th class="py-4 px-6 text-center">{{ __('Estado') }}</th>
                                    <th class="py-4 px-6 text-right">{{ __('Importe') }}</th>
                                    <th class="py-4 px-6 text-center">{{ __('Acción') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-50 text-sm font-semibold text-slate-700">
                                @foreach($transactions as $tx)
                                    @php
                                        $isPayer = $tx->user_id === auth()->id();
                                        $dogsList = $tx->careRequest ? $tx->careRequest->dogs->pluck('name')->join(', ') : 'Mascotas';
                                    @endphp
                                    <tr class="hover:bg-slate-50/20 transition duration-150">
                                        <!-- Fecha -->
                                        <td class="py-4.5 px-6 text-xs text-slate-500 font-mono">
                                            {{ $tx->created_at->format('d/m/Y H:i') }}
                                        </td>
                                        
                                        <!-- ID Transacción -->
                                        <td class="py-4.5 px-6 font-mono text-xs text-slate-400">
                                            {{ $tx->transaction_id }}
                                        </td>
                                        
                                        <!-- Concepto -->
                                        <td class="py-4.5 px-6">
                                            @if($isPayer)
                                                <div class="flex items-center space-x-2">
                                                    <span class="px-2 py-0.5 bg-rose-50 text-rose-700 text-[10px] uppercase font-black rounded border border-rose-100">{{ __('Pago') }}</span>
                                                    <span>
                                                        {{ __('Cuidado de ') }}<span class="font-bold text-slate-800">{{ $dogsList }}</span> 
                                                        {{ __(' por ') }}<span class="font-bold text-slate-800">{{ $tx->receiver->name }}</span>
                                                    </span>
                                                </div>
                                            @else
                                                <div class="flex items-center space-x-2">
                                                    <span class="px-2 py-0.5 bg-emerald-50 text-emerald-700 text-[10px] uppercase font-black rounded border border-emerald-100">{{ __('Ingreso') }}</span>
                                                    <span>
                                                        {{ __('Cuidado de ') }}<span class="font-bold text-slate-800">{{ $dogsList }}</span> 
                                                        {{ __(' de ') }}<span class="font-bold text-slate-800">{{ $tx->user->name }}</span>
                                                    </span>
                                                </div>
                                                <div class="text-[10px] text-slate-400 mt-0.5 font-bold ml-14">
                                                    {{ __('(Comisión de plataforma del 10% incluida: -') }}{{ number_format($tx->fee, 2) }}{{ __('€)') }}
                                                </div>
                                            @endif
                                        </td>

                                        <!-- Estado -->
                                        <td class="py-4.5 px-6 text-center">
                                            @if($tx->status === 'escrow')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-50 text-amber-800 border border-amber-200">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500 mr-1.5"></span>
                                                    {{ __('En Escrow (Garantizado)') }}
                                                </span>
                                            @elseif($tx->status === 'released')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-50 text-emerald-800 border border-emerald-200">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-1.5"></span>
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
                                                <span class="text-emerald-600">+{{ number_format($tx->net_amount, 2) }}€</span>
                                            @endif
                                        </td>

                                        <!-- Acción -->
                                        <td class="py-4.5 px-6 text-center">
                                            @if($tx->careRequest)
                                                <a href="{{ route('care-requests.show', $tx->careRequest) }}" class="text-indigo-600 hover:text-indigo-800 hover:underline text-xs font-black uppercase tracking-wider">
                                                    {{ __('Detalles') }}
                                                </a>
                                            @else
                                                <span class="text-slate-400 text-xs italic">{{ __('Eliminada') }}</span>
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
