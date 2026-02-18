@extends('admin.layouts.app')

@section('content')
<div class="max-w-5xl">
  <h1 class="text-2xl font-bold mb-4">Sugestões de Itens para Churrasco</h1>
  @if(session('success'))<div class="p-3 rounded bg-green-100 text-green-800 mb-3">{{ session('success') }}</div>@endif
  <div class="overflow-x-auto bg-white rounded shadow">
    <table class="min-w-full text-sm">
      <thead>
        <tr class="text-left border-b">
          <th class="p-3">Nome</th>
          <th class="p-3">Categoria</th>
          <th class="p-3">Status</th>
          <th class="p-3">Criado em</th>
          <th class="p-3 text-right">Ações</th>
        </tr>
      </thead>
      <tbody>
        @forelse($pending as $s)
        <tr class="border-b">
          <td class="p-3">{{ $s->name }}</td>
          <td class="p-3">{{ $s->category_slug }}</td>
          <td class="p-3">{{ ucfirst($s->status) }}</td>
          <td class="p-3">{{ $s->created_at?->format('d/m/Y H:i') }}</td>
          <td class="p-3 text-right">
            <form action="{{ route('admin.barbecue.moderate', $s->id) }}" method="POST" class="inline">
              @csrf
              @method('PATCH')
              <input type="hidden" name="action" value="approve">
              <button class="px-3 py-1 rounded bg-emerald-600 text-white" onclick="return confirm('Aprovar sugestão?')">Aprovar</button>
            </form>
            <form action="{{ route('admin.barbecue.moderate', $s->id) }}" method="POST" class="inline ml-2">
              @csrf
              @method('PATCH')
              <input type="hidden" name="action" value="reject">
              <input type="hidden" name="notes" value="Reprovado pelo admin">
              <button class="px-3 py-1 rounded bg-red-600 text-white" onclick="return confirm('Rejeitar sugestão?')">Rejeitar</button>
            </form>
          </td>
        </tr>
        @empty
        <tr><td colspan="5" class="p-4 text-gray-500">Nenhuma sugestão pendente.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
 </div>
@endsection
