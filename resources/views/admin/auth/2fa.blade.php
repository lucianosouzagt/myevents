@extends('layouts.app')

@section('content')
<div class="max-w-md mx-auto bg-white dark:bg-gray-800 p-6 rounded shadow">
    <h2 class="text-xl font-bold mb-4">Verificação em duas etapas</h2>
    <p class="text-sm text-gray-600 dark:text-gray-300 mb-3">Enviamos um código para o seu e-mail.</p>
    <form method="POST" action="{{ route('admin.2fa.verify') }}">
        @csrf
        <div class="mb-4">
            <label class="block text-sm mb-1">Código (6 dígitos)</label>
            <input type="text" name="code" pattern="\d{6}" class="w-full rounded border-gray-300 dark:bg-gray-700 dark:border-gray-600" required autofocus>
            @error('code')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
        </div>
        <button class="px-4 py-2 rounded bg-blue-600 text-white w-full">Verificar</button>
    </form>
</div>
@endsection
