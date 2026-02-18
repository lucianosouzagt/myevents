@extends('admin.layouts.app')

@section('content')
<div class="max-w-lg mx-auto bg-white dark:bg-gray-800 p-6 rounded shadow">
  <h2 class="text-xl font-bold mb-4">Editar Usuário Admin</h2>
  <form method="POST" action="{{ route('admin.admins.update', $user) }}" class="space-y-4">
    @csrf
    @method('PUT')
    <div centroid>
      <label class="block text-sm mb-1">Nome</label>
      <input type="text" name="name" value="{{ old('name',$user->name) }}" class="w-full rounded border-gray-300 dark:bg-gray-700 dark:border-gray-600" required>
      @error('name')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
    </div>
    <div>
      <label class="block text-sm mb-1">E-mail</label>
      <input type="email" name="email" value="{{ old('email',$user->email) }}" class="w-full rounded border-gray-300 dark:bg-gray-700 dark:border-gray-600" required>
      @error('email')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
    </div>
    <div>
      <label class="block text-sm mb-1">Nova senha (opcional)</label>
      <input type="password" name="password" class="w-full rounded border-gray-300 dark:bg-gray-700 dark:border-gray-600">
      <p class="text-xs text-gray-500">Deixe em branco para manter.</p>
      @error('password')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
    </div>
    <div>
      <label class="block text-sm mb-1">Funções</label>
      <div class="space-y-2">
        @foreach($roles as $r)
          <label class="flex items-center gap-2">
            <input type="checkbox" name="roles[]" value="{{ $r->id }}" @checked($user->roles->pluck('id')->contains($r->id))> <span>{{ $r->name }}</span>
          </label>
        @endforeach
      </div>
      @error('roles')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
    </div>
    <div class="flex justify-between">
      <button formaction="{{ route('admin.admins.reset',$user) }}" formmethod="POST" class="px-3 py-2 rounded bg-yellow-600 text-white" onclick="return confirm('Redefinir senha e enviar por e-mail?')">@csrf Redefinir Senha</button>
      <div class="flex gap-2">
        <a href="{{ route('admin.admins.index') }}" class="px-4 py-2 rounded border">Cancelar</a>
        <button class="px-4 py-2 rounded bg-blue-600 text-white">Salvar</button>
      </div>
    </div>
  </form>
  <form method="POST" action="{{ route('admin.admins.destroy',$user) }}" class="mt-4" onsubmit="return confirm('Excluir usuário admin?')">
    @csrf
    @method('DELETE')
    <button class="px-4 py-2 rounded bg-red-600 text-white">Excluir</button>
  </form>
</div>
@endsection
