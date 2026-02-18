@extends('admin.layouts.app')

@section('content')
<div class="max-w-6xl mx-auto">
  <div class="flex justify-between items-center mb-4">
    <h1 class="text-2xl font-bold">Usuários Administrativos</h1>
    <a href="{{ route('admin.admins.create') }}" class="px-4 py-2 rounded bg-blue-600 text-white">Novo</a>
  </div>
  @if(session('success'))<div class="p-3 bg-green-100 text-green-800 rounded mb-3">{{ session('success') }}</div>@endif
  <div class="overflow-x-auto bg-white dark:bg-gray-800 rounded shadow">
    <table class="min-w-full text-sm">
      <thead>
        <tr class="text-left border-b">
          <th class="p-3">Nome</th>
          <th class="p-3">E-mail</th>
          <th class="p-3">Funções</th>
          <th class="p-3 text-right">Ações</th>
        </tr>
      </thead>
      <tbody>
        @foreach($users as $u)
          <tr class="border-b">
            <td class="p-3">{{ $u->name }}</td>
            <td class="p-3">{{ $u->email }}</td>
            <td class="p-3">{{ $u->roles->pluck('name')->join(', ') }}</td>
            <td class="p-3 text-right">
              <a href="{{ route('admin.admins.edit',$u) }}" class="px-3 py-1 rounded bg-gray-700 text-white">Editar</a>
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>
  <div class="mt-3">
    {{ $users->links() }}
  </div>
</div>
@endsection
