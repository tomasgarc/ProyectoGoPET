<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-bold text-2xl text-brand-900 leading-tight">
                {{ __('Historial de Peticiones Finalizadas') }}
            </h2>
            <a href="{{ route('care-requests.index') }}" class="inline-flex items-center text-sm font-bold text-accent-600 hover:text-brand-600 transition-colors">
                <svg class="w-4 h-4 mr-1.5 text-brand-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                {{ __('Volver a Mis Peticiones') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm rounded-3xl border border-brand-100/50">
                <div class="p-6 sm:p-8 text-gray-900">
                    <div class="mb-6">
                        <p class="text-sm text-accent-600 font-semibold">
                            {{ __('Aquí se muestran todas las peticiones en las que has participado (como creador o como cuidador) cuyo plazo de vigencia ha finalizado.') }}
                        </p>
                    </div>

                    @if($finalizedRequests->isEmpty())
                        <div class="text-center py-16 bg-accent-50/10 border-2 border-dashed border-brand-200 rounded-3xl max-w-md mx-auto space-y-4">
                            <span class="text-6xl mb-4 block">📜</span>
                            <h3 class="text-lg font-black text-brand-900 mt-2">{{ __('Tu historial está vacío') }}</h3>
                            <p class="text-accent-600 text-sm font-medium leading-relaxed">{{ __('Las peticiones finalizadas aparecerán aquí una vez que expire su fecha de finalización o marques la liberación del pago.') }}</p>
                            <div class="pt-2">
                                <a href="{{ route('care-requests.index') }}" class="inline-flex items-center px-5 py-2.5 bg-brand-600 hover:bg-brand-700 text-white font-bold text-sm rounded-2xl shadow-sm transition">
                                    {{ __('Ver Mis Peticiones Activas') }}
                                </a>
                            </div>
                        </div>
                    @else
                        <div class="overflow-x-auto border border-brand-100 rounded-2xl">
                            <table class="min-w-full divide-y divide-brand-50">
                                <thead>
                                    <tr class="bg-accent-50/50">
                                        <th class="px-6 py-4 text-left text-xs font-bold text-accent-600 uppercase tracking-wider">{{ __('Perros Cuidado') }}</th>
                                        <th class="px-6 py-4 text-left text-xs font-bold text-accent-600 uppercase tracking-wider">{{ __('Fechas') }}</th>
                                        <th class="px-6 py-4 text-left text-xs font-bold text-accent-600 uppercase tracking-wider">{{ __('Precio Pagado') }}</th>
                                        <th class="px-6 py-4 text-left text-xs font-bold text-accent-600 uppercase tracking-wider">{{ __('Rol') }}</th>
                                        <th class="px-6 py-4 text-left text-xs font-bold text-accent-600 uppercase tracking-wider">{{ __('Participantes') }}</th>
                                        <th class="px-6 py-4 text-left text-xs font-bold text-accent-600 uppercase tracking-wider">{{ __('Estado') }}</th>
                                        <th class="px-6 py-4 text-left text-xs font-bold text-accent-600 uppercase tracking-wider">{{ __('Acciones') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-brand-50/50">
                                    @foreach($finalizedRequests as $request)
                                        <tr class="hover:bg-accent-50/30 transition-colors">
                                            <!-- Perros -->
                                            <td class="px-6 py-4">
                                                <div class="flex flex-wrap gap-1.5">
                                                    @foreach($request->dogs as $dog)
                                                        <span class="inline-flex items-center px-2 py-0.5 bg-brand-50 border border-brand-100 text-brand-700 text-xs font-bold rounded-full uppercase">
                                                            🐾 {{ $dog->name }}
                                                        </span>
                                                    @endforeach
                                                </div>
                                            </td>

                                            <!-- Fechas -->
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-bold text-brand-900 leading-tight">
                                                    {{ \Carbon\Carbon::parse($request->start_date)->format('d/m/Y') }}
                                                </div>
                                                <div class="text-xs text-accent-500 font-semibold">
                                                    {{ __('hasta') }} {{ \Carbon\Carbon::parse($request->end_date)->format('d/m/Y') }}
                                                </div>
                                            </td>

                                            <!-- Precio -->
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-black text-brand-600">
                                                    {{ number_format($request->price, 0) }}€
                                                </div>
                                            </td>

                                            <!-- Rol -->
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @if($request->user_id === auth()->id())
                                                    <span class="inline-flex items-center px-2.5 py-0.5 bg-brand-50 text-brand-700 border border-brand-100/50 text-[9px] font-black uppercase tracking-wider rounded-full">
                                                        📤 {{ __('Dueño') }}
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center px-2.5 py-0.5 bg-brand-200 text-brand-900 border border-brand-200/50 text-[9px] font-black uppercase tracking-wider rounded-full">
                                                        🤝 {{ __('Cuidador') }}
                                                    </span>
                                                @endif
                                            </td>

                                            <!-- Participantes -->
                                            <td class="px-6 py-4 whitespace-nowrap text-xs text-accent-600 font-medium">
                                                <div class="space-y-1">
                                                    @if($request->user_id === auth()->id())
                                                        <div>
                                                            <span class="font-black text-accent-500 uppercase tracking-widest text-[8px] block">{{ __('Cuidador:') }}</span>
                                                            @if($request->acceptedBy)
                                                                <span class="font-bold text-brand-900 block mt-0.5">{{ $request->acceptedBy->name }}</span>
                                                            @else
                                                                <span class="text-accent-400 italic block mt-0.5">{{ __('Ninguno') }}</span>
                                                            @endif
                                                        </div>
                                                    @else
                                                        <div>
                                                            <span class="font-black text-accent-500 uppercase tracking-widest text-[8px] block">{{ __('Dueño:') }}</span>
                                                            <span class="font-bold text-brand-900 block mt-0.5">{{ $request->user->name }}</span>
                                                        </div>
                                                    @endif
                                                </div>
                                            </td>

                                            <!-- Estado -->
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="px-2.5 py-1 inline-flex text-xs leading-5 font-bold rounded-full bg-accent-100 text-accent-700 border border-brand-100/10 uppercase tracking-wider">
                                                    {{ __('Finalizada') }}
                                                </span>
                                            </td>

                                            <!-- Acciones -->
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-bold">
                                                <a href="{{ route('care-requests.show', $request) }}" class="text-brand-600 hover:text-brand-800 hover:underline">
                                                    {{ __('Ver Detalles') }}
                                                </a>
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
    </div>
</x-app-layout>
