<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Historial de Peticiones Finalizadas') }}
            </h2>
            <a href="{{ route('care-requests.index') }}" class="inline-flex items-center text-sm font-semibold text-indigo-600 hover:text-indigo-800 transition-colors">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                {{ __('Volver a Mis Peticiones') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm rounded-lg border border-gray-100">
                <div class="p-6 sm:p-8 text-gray-900">
                    <div class="mb-6">
                        <p class="text-sm text-gray-500">
                            {{ __('Aquí se muestran todas las peticiones en las que has participado (como creador o como cuidador) cuyo plazo de vigencia ha finalizado.') }}
                        </p>
                    </div>

                    @if($finalizedRequests->isEmpty())
                        <div class="text-center py-16 bg-gray-50/50 border border-dashed border-gray-200 rounded-lg">
                            <span class="text-6xl mb-4 block">📜</span>
                            <h3 class="text-lg font-bold text-gray-900 mt-2">{{ __('Tu historial está vacío') }}</h3>
                            <p class="text-gray-500 mt-1 max-w-md mx-auto">{{ __('Las peticiones finalizadas aparecerán aquí una vez que expire su fecha de finalización.') }}</p>
                            <div class="mt-6">
                                <a href="{{ route('care-requests.index') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-sm shadow-sm transition">
                                    {{ __('Ver Mis Peticiones Activas') }}
                                </a>
                            </div>
                        </div>
                    @else
                        <div class="overflow-x-auto border border-gray-100 rounded-sm">
                            <table class="min-w-full divide-y divide-gray-150">
                                <thead>
                                    <tr class="bg-gray-50/50">
                                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">{{ __('Perros Cuidado') }}</th>
                                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">{{ __('Fechas') }}</th>
                                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">{{ __('Precio Pagado') }}</th>
                                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">{{ __('Rol') }}</th>
                                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">{{ __('Participantes') }}</th>
                                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">{{ __('Estado') }}</th>
                                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">{{ __('Acciones') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-100">
                                    @foreach($finalizedRequests as $request)
                                        <tr class="hover:bg-gray-50/30 transition-colors">
                                            <!-- Perros -->
                                            <td class="px-6 py-4">
                                                <div class="flex flex-wrap gap-1.5">
                                                    @foreach($request->dogs as $dog)
                                                        <span class="inline-flex items-center px-2 py-0.5 bg-indigo-50 border border-indigo-100 text-indigo-700 text-xs font-bold rounded-sm uppercase">
                                                            🐾 {{ $dog->name }}
                                                        </span>
                                                    @endforeach
                                                </div>
                                            </td>

                                            <!-- Fechas -->
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-semibold text-gray-900">
                                                    {{ \Carbon\Carbon::parse($request->start_date)->format('d/m/Y') }}
                                                </div>
                                                <div class="text-xs text-gray-500">
                                                    {{ __('hasta') }} {{ \Carbon\Carbon::parse($request->end_date)->format('d/m/Y') }}
                                                </div>
                                            </td>

                                            <!-- Precio -->
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-black text-indigo-600">
                                                    {{ number_format($request->price, 0) }}€
                                                </div>
                                            </td>

                                            <!-- Rol -->
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @if($request->user_id === auth()->id())
                                                    <span class="inline-flex items-center px-2.5 py-0.5 bg-blue-50 text-blue-700 border border-blue-100 text-[10px] font-black uppercase tracking-wider">
                                                        📤 {{ __('Dueño') }}
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center px-2.5 py-0.5 bg-emerald-50 text-emerald-700 border border-emerald-100 text-[10px] font-black uppercase tracking-wider">
                                                        🤝 {{ __('Cuidador') }}
                                                    </span>
                                                @endif
                                            </td>

                                            <!-- Participantes -->
                                            <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-600">
                                                <div class="space-y-1">
                                                    @if($request->user_id === auth()->id())
                                                        <div>
                                                            <span class="font-bold text-gray-400 uppercase tracking-widest text-[9px]">{{ __('Cuidador:') }}</span>
                                                            @if($request->acceptedBy)
                                                                <span class="font-semibold text-gray-900 block">{{ $request->acceptedBy->name }}</span>
                                                            @else
                                                                <span class="text-gray-400 italic block">{{ __('Ninguno') }}</span>
                                                            @endif
                                                        </div>
                                                    @else
                                                        <div>
                                                            <span class="font-bold text-gray-400 uppercase tracking-widest text-[9px]">{{ __('Dueño:') }}</span>
                                                            <span class="font-semibold text-gray-900 block">{{ $request->user->name }}</span>
                                                        </div>
                                                    @endif
                                                </div>
                                            </td>

                                            <!-- Estado -->
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="px-2.5 py-1 inline-flex text-xs leading-5 font-black rounded-sm bg-gray-100 text-gray-800 border border-gray-200 uppercase tracking-wider">
                                                    {{ __('Finalizada') }}
                                                </span>
                                            </td>

                                            <!-- Acciones -->
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-bold">
                                                <a href="{{ route('care-requests.show', $request) }}" class="text-indigo-600 hover:text-indigo-900 underline">
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
