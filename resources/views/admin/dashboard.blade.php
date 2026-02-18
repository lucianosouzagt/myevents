@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto">
  <h1 class="text-2xl font-bold mb-4">Admin · Dashboard</h1>
  <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <div class="p-4 bg-white dark:bg-gray-800 rounded shadow">
      <h2 class="font-semibold mb-2">Usuários</h2>
      <p>Total: <strong>{{ $metrics['users_total'] }}</strong></p>
    </div>
    <div class="p-4 bg-white dark:bg-gray-800 rounded shadow">
      <h2 class="font-semibold mb-2">Eventos</h2>
      <p>Total: <strong>{{ $metrics['events_total'] }}</strong></p>
    </div>
  </div>
</div>
@endsection
