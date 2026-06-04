{!! '<' . '?xml version="1.0" encoding="UTF-8"?' . '>' !!}
<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">
    <channel>
        <title>GoPET - Peticiones Activas de Cuidado de Perros</title>
        <link>{{ url('/') }}</link>
        <description>Canal de sindicación RSS de las últimas peticiones activas de cuidado de perros en GoPET</description>
        <language>es-es</language>
        <pubDate>{{ now()->toRssString() }}</pubDate>
        <atom:link href="{{ url('/feed/care-requests.xml') }}" rel="self" type="application/rss+xml" />

        @foreach($requests as $request)
            <item>
                <title>Cuidado para: {{ $request->dogs->pluck('name')->implode(', ') }} ({{ number_format($request->price, 0) }}€)</title>
                <link>{{ route('care-requests.show', $request) }}</link>
                <description><![CDATA[
                    <p><strong>Dueño:</strong> {{ $request->user->name }}</p>
                    <p><strong>Fechas:</strong> Del {{ \Carbon\Carbon::parse($request->start_date)->format('d/m/Y') }} al {{ \Carbon\Carbon::parse($request->end_date)->format('d/m/Y') }}</p>
                    <p><strong>Precio:</strong> {{ number_format($request->price, 2) }}€</p>
                    <p><strong>Descripción:</strong> {{ $request->description ?? 'Sin especificaciones añadidas.' }}</p>
                    <p><strong>Detalles de las Mascotas:</strong></p>
                    <ul>
                        @foreach($request->dogs as $dog)
                            <li>{{ $dog->name }} (Raza: {{ $dog->breed ?? 'Mestizo' }}, Tamaño: {{ ucfirst($dog->size) }}, Edad: {{ $dog->age ?? 'N/D' }} años)</li>
                        @endforeach
                    </ul>
                ]]></description>
                <guid isPermaLink="false">gopet-request-{{ $request->id }}</guid>
                <pubDate>{{ $request->created_at->toRssString() }}</pubDate>
            </item>
        @endforeach
    </channel>
</rss>
