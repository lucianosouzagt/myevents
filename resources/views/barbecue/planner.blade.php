@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="mb-6">
        <h1 class="text-2xl font-bold">Planejador de Churrasco</h1>
        <p class="text-gray-600 dark:text-gray-300">Informe a quantidade de homens, mulheres e crianças e selecione os itens desejados.</p>
    </div>

    <form method="POST" action="{{ route('barbecue.calculate') }}" class="space-y-6">
        @csrf
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium mb-1">Homens</label>
                <input type="number" name="men" min="0" value="{{ old('men', 0) }}" class="w-full rounded border-gray-300 dark:bg-gray-800 dark:border-gray-700" />
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Mulheres</label>
                <input type="number" name="women" min="0" value="{{ old('women', 0) }}" class="w-full rounded border-gray-300 dark:bg-gray-800 dark:border-gray-700" />
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Crianças</label>
                <input type="number" name="children" min="0" value="{{ old('children', 0) }}" class="w-full rounded border-gray-300 dark:bg-gray-800 dark:border-gray-700" />
            </div>
        </div>

        <div class="space-y-4">
            @foreach ($categories as $category)
                <div class="rounded border border-gray-200 dark:border-gray-700 p-4">
                    <h2 class="text-lg font-semibold mb-2">{{ $category->name }}</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                        @foreach ($category->itemTypes as $type)
                            <label class="inline-flex items-center">
                                <input type="checkbox" name="types[]" value="{{ $type->id }}" class="rounded mr-2"
                                    @checked(collect(old('types'))->contains($type->id)) />
                                <span>{{ $type->name }} <span class="text-xs text-gray-500">({{ $type->unit }})</span></span>
                            </label>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>

        <div class="flex items-center gap-2">
            <button type="submit" class="px-4 py-2 rounded bg-blue-600 text-white hover:bg-blue-700">Calcular Lista</button>
            <a href="{{ route('barbecue.suggest') }}" class="px-4 py-2 rounded bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600">Sugerir novo item</a>
        </div>
    </form>

    @isset($result)
    <div class="mt-8">
        <h2 class="text-xl font-bold mb-4">Lista de Compras</h2>
        <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">Baseada em {{ $result['men'] }} homem(ns), {{ $result['women'] }} mulher(es) e {{ $result['children'] }} criança(s).</p>
        @foreach ($result['groups'] as $slug => $group)
            <div class="mb-4">
                <h3 class="text-lg font-semibold">{{ $group['category'] }}</h3>
                <ul class="list-disc list-inside">
                    @foreach ($group['items'] as $item)
                        <li>{{ $item['name'] }}: <strong>{{ $item['quantity'] }} {{ $item['unit'] }}</strong></li>
                    @endforeach
                </ul>
            </div>
        @endforeach
        <div class="mt-6">
            <button id="wa-share" class="px-4 py-2 rounded bg-green-600 text-white hover:bg-green-700">Compartilhar no WhatsApp</button>
        </div>
        <script>
            const shareBtn = document.getElementById('wa-share');
            shareBtn.addEventListener('click', function () {
                const data = @json($result);
                let lines = [];
                lines.push('Planejador de Churrasco');
                lines.push('Homens: ' + data.men + ' | Mulheres: ' + data.women + ' | Crianças: ' + data.children);
                lines.push('Lista de Compras:');
                Object.keys(data.groups).forEach(function (slug) {
                    const g = data.groups[slug];
                    lines.push(g.category + ':');
                    g.items.forEach(function (it) {
                        lines.push('- ' + it.name + ': ' + it.quantity + ' ' + it.unit);
                    });
                });
                const text = lines.join('\n');
                const url = 'https://wa.me/?text=' + encodeURIComponent(text);
                window.open(url, '_blank');
            });
        </script>
    </div>
    @endisset
</div>
@endsection
