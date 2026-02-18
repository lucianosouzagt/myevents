@extends('layouts.app')

@section('head')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endsection

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-semibold mb-2">Dashboard de Analytics</h1>
    <form class="flex flex-col md:flex-row gap-3 items-end" method="get" action="{{ route('admin.analytics.dashboard') }}">
        <div>
            <label for="from" class="block text-sm font-medium mb-1">De</label>
            <input type="date" id="from" name="from" value="{{ $from }}" class="border rounded px-3 py-2 dark:bg-gray-800" />
        </div>
        <div>
            <label for="to" class="block text-sm font-medium mb-1">Até</label>
            <input type="date" id="to" name="to" value="{{ $to }}" class="border rounded px-3 py-2 dark:bg-gray-800" />
        </div>
        <button class="bg-blue-600 text-white px-4 py-2 rounded">Filtrar</button>
        <a href="{{ route('admin.analytics.export.csv', ['from' => $from, 'to' => $to]) }}" class="bg-emerald-600 text-white px-4 py-2 rounded">Exportar CSV</a>
        <button type="button" onclick="window.print()" class="bg-gray-700 text-white px-4 py-2 rounded">Salvar PDF</button>
    </form>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
    <div class="p-4 rounded border dark:border-gray-700">
        <div class="text-sm text-gray-500">Usuários Totais</div>
        <div class="text-3xl font-bold">{{ $data['totalUsers'] }}</div>
    </div>
    <div class="p-4 rounded border dark:border-gray-700">
        <div class="text-sm text-gray-500">Visitas</div>
        <div class="text-3xl font-bold">{{ $data['totalVisits'] }}</div>
    </div>
    <div class="p-4 rounded border dark:border-gray-700">
        <div class="text-sm text-gray-500">Taxa de Rejeição</div>
        <div class="text-3xl font-bold">{{ $data['bounceRate'] }}%</div>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <div class="p-4 rounded border dark:border-gray-700">
        <div class="mb-2 font-medium">Crescimento Diário de Usuários</div>
        <canvas id="dailyUsers"></canvas>
    </div>
    <div class="p-4 rounded border dark:border-gray-700">
        <div class="mb-2 font-medium">Tráfego por Dispositivo</div>
        <canvas id="devicesChart"></canvas>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
    <div class="p-4 rounded border dark:border-gray-700">
        <div class="mb-2 font-medium">Páginas Mais Acessadas</div>
        <ul class="divide-y divide-gray-200 dark:divide-gray-700">
            @foreach ($data['topPages'] as $p)
                <li class="py-2 flex justify-between"><span class="truncate">{{ $p->path }}</span><span class="font-semibold">{{ $p->views }}</span></li>
            @endforeach
        </ul>
    </div>
    <div class="p-4 rounded border dark:border-gray-700">
        <div class="mb-2 font-medium">Origem do Tráfego</div>
        <canvas id="sourcesChart"></canvas>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
    <div class="p-4 rounded border dark:border-gray-700">
        <div class="mb-2 font-medium">Usuários Ativos vs Inativos</div>
        <div class="text-sm text-gray-500">Período selecionado</div>
        <div class="mt-2 text-lg">Ativos: <span class="font-semibold">{{ $data['activeUsers'] }}</span></div>
        <div class="text-lg">Inativos: <span class="font-semibold">{{ $data['inactiveUsers'] }}</span></div>
    </div>
    <div class="p-4 rounded border dark:border-gray-700">
        <div class="mb-2 font-medium">Taxa de Conversão (Visitantes → Usuários)</div>
        <div class="text-3xl font-bold">{{ $data['conversions'] }}%</div>
    </div>
</div>

<script id="daily-labels" type="application/json">{{ json_encode(collect($data['dailyGrowth'])->map(function($d){ return \Carbon\Carbon::parse($d->day)->format('d/m'); })->values()->toArray()) }}</script>
<script id="daily-values" type="application/json">{{ json_encode(collect($data['dailyGrowth'])->map(function($d){ return $d->total; })->values()->toArray()) }}</script>
<script id="devices-data" type="application/json">{{ json_encode($data['devices']) }}</script>
<script id="sources-data" type="application/json">{{ json_encode($data['sources']) }}</script>

<script>
    const dailyCtx = document.getElementById('dailyUsers').getContext('2d');
    const dailyLabelsEl = document.getElementById('daily-labels');
    const dailyValuesEl = document.getElementById('daily-values');
    const dailyLabels = JSON.parse(dailyLabelsEl ? dailyLabelsEl.textContent : '[]');
    const dailyValues = JSON.parse(dailyValuesEl ? dailyValuesEl.textContent : '[]');
    new Chart(dailyCtx, {
        type: 'line',
        data: {
            labels: dailyLabels,
            datasets: [{label: 'Novos usuários', data: dailyValues, borderColor: '#3b82f6', tension: 0.3}]},
        options: {scales: {y: {beginAtZero: true}}}
    });

    const devicesCtx = document.getElementById('devicesChart').getContext('2d');
    const devicesEl = document.getElementById('devices-data');
    const devicesData = JSON.parse(devicesEl ? devicesEl.textContent : '{}');
    new Chart(devicesCtx, {
        type: 'doughnut',
        data: {
            labels: Object.keys(devicesData),
            datasets: [{data: Object.values(devicesData), backgroundColor: ['#1f2937','#3b82f6','#10b981','#f59e0b']}]
        }
    });

    const sourcesCtx = document.getElementById('sourcesChart').getContext('2d');
    const sourcesEl = document.getElementById('sources-data');
    const sourcesData = JSON.parse(sourcesEl ? sourcesEl.textContent : '{}');
    new Chart(sourcesCtx, {
        type: 'bar',
        data: {
            labels: Object.keys(sourcesData),
            datasets: [{label: 'Visitas', data: Object.values(sourcesData), backgroundColor: '#10b981'}]
        },
        options: {scales: {y: {beginAtZero: true}}}
    });
</script>
@endsection
