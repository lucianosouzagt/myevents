@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto">
    <h1 class="text-2xl font-bold mb-4">Gerenciar Criadores</h1>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-white dark:bg-gray-800 p-4 rounded shadow">
            <h2 class="font-semibold mb-3">Criar Novo</h2>
            <form method="POST" action="{{ route('admin.users.store') }}" class="space-y-3">
                @csrf
                <input type="text" name="name" placeholder="Nome" class="w-full rounded border-gray-300 dark:bg-gray-700 dark:border-gray-600" required>
                <input type="email" name="email" placeholder="E-mail" class="w-full rounded border-gray-300 dark:bg-gray-700 dark:border-gray-600" required>
                <input type="password" name="password" placeholder="Senha temporária" class="w-full rounded border-gray-300 dark:bg-gray-700 dark:border-gray-600" required>
                <button class="px-4 py-2 rounded bg-blue-600 text-white">Criar</button>
            </form>
        </div>
        <div class="bg-white dark:bg-gray-800 p-4 rounded shadow">
            <h2 class="font-semibold mb-3">Lista</h2>
            <table class="w-full text-sm">
                <thead><tr><th class="text-left p-2">Nome</th><th class="text-left p-2">E-mail</th><th class="p-2">Ações</th></tr></thead>
                <tbody>
                @forelse($creators as $u)
                    <tr class="border-t border-gray-200 dark:border-gray-700">
                        <td class="p-2">{{ $u->name }}</td>
                        <td class="p-2">{{ $u->email }}</td>
                        <td class="p-2 text-right space-x-2">
                            <form method="POST" action="{{ route('admin.users.password.reset', $u) }}" class="inline">
                                @csrf
                                <input type="hidden" name="password" value="Temp@1234">
                                <button class="px-2 py-1 rounded bg-yellow-600 text-white">Reset Senha</button>
                            </form>
                            <form method="POST" action="{{ route('admin.users.deactivate', $u) }}" class="inline">
                                @csrf
                                <button class="px-2 py-1 rounded bg-red-600 text-white">Desativar</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="3" class="p-2 text-gray-500">Nenhum criador encontrado.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
