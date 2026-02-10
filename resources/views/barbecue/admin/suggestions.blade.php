@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="mb-6 flex items-center justify-between">
        <h1 class="text-2xl font-bold">Sugestões de Itens</h1>
        <a href="{{ route('barbecue.index') }}" class="px-4 py-2 rounded bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600">Planner</a>
    </div>

    @if($pending->isEmpty())
        <p class="text-gray-600 dark:text-gray-300">Nenhuma sugestão pendente.</p>
    @else
        <div class="space-y-4">
            @foreach($pending as $s)
                <div class="rounded border border-gray-200 dark:border-gray-700 p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="font-semibold">{{ $s->name }}</p>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Categoria: {{ $s->category_slug }} • por {{ $s->user->name ?? 'Anônimo' }}</p>
                        </div>
                        <form method="POST" action="{{ route('barbecue.admin.moderate', $s->id) }}" class="flex items-center gap-2">
                            @csrf
                            @method('PATCH')
                            <input type="text" name="notes" class="rounded border-gray-300 dark:bg-gray-800 dark:border-gray-700" placeholder="Notas (opcional)" />
                            <button name="action" value="approve" class="px-3 py-2 rounded bg-green-600 text-white hover:bg-green-700">Aprovar</button>
                            <button name="action" value="reject" class="px-3 py-2 rounded bg-red-600 text-white hover:bg-red-700">Rejeitar</button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection

