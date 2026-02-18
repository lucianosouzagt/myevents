@extends('admin.layouts.app')

@section('content')
<div class="max-w-lg mx-auto bg-white dark:bg-gray-800 p-6 rounded shadow">
  <h2 class="text-xl font-bold mb-4">Novo Usuário Admin</h2>
  <form method="POST" action="{{ route('admin.admins.store') }}" class="space-y-4">
    @csrf
    <div>
      <label class="block text-sm mb-1">Nome</label>
      <input type="text" name="name" class="w-full rounded border-gray-300 dark:bg-gray-700 dark:border-gray-600" required>
      @error('name')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
    </div>
    <div>
      <label class="block text-sm mb-1">E-mail</label>
      <input type="email" name="email" class="w-full rounded border-gray-300 dark:bg-gray-700 dark:border-gray-600" required>
      @error('email')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
    </div>
    <div>
      <label class="block text-sm mb-1">Senha</label>
      <input type="password" name="password" class="w-full rounded border-gray-300 dark:bg-gray-700 dark:border-gray-600" required>
      <p class="text-xs text-gray-500">Mín. 8, com maiúscula, minúscula, número e símbolo.</p>
      @error('password')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
    </div>
    <div>
      <label class="block text-sm mb-1">Funções</label>
      <div class="space-y-2">
        @foreach($roles as $r)
          <label class="flex items-center gap-2">
            <input type="checkbox" name="roles[]" value="{{ $r->id }}"> <span>{{ $r->name }}</span>
          </label>
        @endforeach
      </div>
      @error('roles')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
    </div>
    <div class="flex justify-end gap-2">
      <a href="{{ route('admin.admins.index') }}" class="px-4 py-2 rounded border">Cancelar</a>
      <button class="px-4 py-2 rounded bg-blue-600 text-white">Criar</button>
    </div>
  </form>
</div>
@endsection
