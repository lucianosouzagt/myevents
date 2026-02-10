@extends('layouts.app')

@section('content')
<div class="max-w-xl mx-auto">
    <div class="mb-6">
        <h1 class="text-2xl font-bold">Sugerir novo item</h1>
        <p class="text-gray-600 dark:text-gray-300">Sugira novos tipos de carnes ou acompanhamentos para o catálogo.</p>
    </div>

    <form method="POST" action="{{ route('barbecue.suggest.store') }}" class="space-y-4">
        @csrf
        <div>
            <label class="block text-sm font-medium mb-1">Categoria</label>
            <select name="category_slug" class="w-full rounded border-gray-300 dark:bg-gray-800 dark:border-gray-700">
                <option value="meat" @selected(old('category_slug')==='meat')>Carnes</option>
                <option value="side" @selected(old('category_slug')==='side')>Acompanhamentos</option>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Nome do item</label>
            <input type="text" name="name" value="{{ old('name') }}" class="w-full rounded border-gray-300 dark:bg-gray-800 dark:border-gray-700" placeholder="Ex.: Cupim, Queijo coalho, etc." />
        </div>
        <div class="flex items-center gap-2">
            <button type="submit" class="px-4 py-2 rounded bg-blue-600 text-white hover:bg-blue-700">Enviar sugestão</button>
            <a href="{{ route('barbecue.index') }}" class="px-4 py-2 rounded bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600">Voltar ao planejador</a>
        </div>
    </form>
</div>
@endsection

