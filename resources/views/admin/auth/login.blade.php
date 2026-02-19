@extends('admin.layouts.app')

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
            <div class="relative">
                <input id="admin-password" type="password" name="password" class="w-full rounded border-gray-300 dark:bg-gray-700 dark:border-gray-600 pr-10" required>
                <button type="button" id="togglePassword" class="absolute inset-y-0 right-0 px-3 text-sm">👁️</button>
            </div>
            <div id="strength" class="text-xs mt-1 text-gray-500"></div>
            @error('password')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
        </div>
        <div class="flex items-center mb-4">
            <input id="remember" type="checkbox" name="remember" class="mr-2">
            <label for="remember" class="text-sm">Lembrar-me</label>
        </div>
        <button class="px-4 py-2 rounded bg-blue-600 hover:bg-blue-700 text-white w-full">Entrar</button>
        @if(session('error'))<p class="text-red-500 text-sm mt-2">{{ session('error') }}</p>@endif
    </form>
</div>
<script>
const input = document.getElementById('admin-password');
const strength = document.getElementById('strength');
const toggle = document.getElementById('togglePassword');
toggle.addEventListener('click', () => {
  input.type = input.type === 'password' ? 'text' : 'password';
});
input.addEventListener('input', () => {
  const v = input.value;
  let score = 0;
  if (v.length >= 8) score++;
  if (/[A-Z]/.test(v)) score++;
  if (/[a-z]/.test(v)) score++;
  if (/[0-9]/.test(v)) score++;
  if (/[^A-Za-z0-9]/.test(v)) score++;
  const levels = ['Muito fraca','Fraca','Média','Forte','Muito forte'];
  strength.textContent = 'Força: ' + levels[Math.max(0, Math.min(score-1, levels.length-1))];
});
</script>
@endsection
