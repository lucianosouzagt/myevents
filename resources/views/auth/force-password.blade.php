@extends('layouts.app')

@section('content')
<div class="max-w-md mx-auto bg-white dark:bg-gray-800 p-6 rounded shadow">
    <h2 class="text-xl font-bold mb-4">Defina uma nova senha</h2>
    <form method="POST" action="{{ route('password.force.update') }}">
        @csrf
        <div class="mb-4">
            <label class="block text-sm mb-1">Nova senha</label>
            <input type="password" name="password" class="w-full rounded border-gray-300 dark:bg-gray-700 dark:border-gray-600" required>
            <p class="text-xs text-gray-500 mt-1">Mín. 8, com maiúscula, minúscula, número e símbolo.</p>
            @error('password')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
        </div>
        <div class="mb-6">
            <label class="block text-sm mb-1">Confirmar senha</label>
            <input type="password" name="password_confirmation" class="w-full rounded border-gray-300 dark:bg-gray-700 dark:border-gray-600" required>
        </div>
        <button class="px-4 py-2 rounded bg-blue-600 text-white">Atualizar senha</button>
    </form>
    <p class="text-xs text-gray-500 mt-4">Por segurança, você precisa alterar a senha no primeiro acesso.</p>
 </div>
@endsection
