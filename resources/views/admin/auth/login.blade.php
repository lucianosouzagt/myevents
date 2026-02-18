@extends('layouts.app')

@section('content')
<div class="max-w-md mx-auto bg-white dark:bg-gray-800 p-6 rounded shadow">
    <h2 class="text-xl font-bold mb-4">Admin · Login</h2>
    <form method="POST" action="{{ route('admin.login.post') }}">
        @csrf
        <div class="mb-4">
            <label class="block text-sm mb-1">Email</label>
            <input type="email" name="email" class="w-full rounded border-gray-300 dark:bg-gray-700 dark:border-gray-600" required autofocus>
            @error('email')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
        </div>
        <div class="mb-6">
            <label class="block text-sm mb-1">Senha</label>
            <input type="password" name="password" class="w-full rounded border-gray-300 dark:bg-gray-700 dark:border-gray-600" required>
            @error('password')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
        </div>
        <button class="px-4 py-2 rounded bg-blue-600 text-white w-full">Entrar</button>
    </form>
</div>
@endsection
