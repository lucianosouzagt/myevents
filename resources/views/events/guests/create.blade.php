@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto bg-white p-8 border border-gray-200 rounded-lg shadow-sm dark:bg-gray-800 dark:border-gray-700">
    <h2 class="text-2xl font-bold mb-6 text-gray-900 dark:text-white">Adicionar Convidado</h2>
    <p class="mb-6 text-gray-500 dark:text-gray-400">Evento: {{ $event->title }}</p>
    
    <form action="{{ route('events.guests.store', $event->id) }}" method="POST">
        @csrf
        <div class="mb-5">
            <label for="guest_name" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Nome Completo</label>
            <input type="text" name="guest_name" id="guest_name" value="{{ old('guest_name') }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" required>
        </div>
        
        <div class="mb-5">
            <label for="email" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">E-mail</label>
            <input type="email" name="email" id="email" value="{{ old('email') }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" required>
        </div>

        <div class="mb-5">
            <label for="whatsapp" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">WhatsApp (Opcional)</label>
            <input type="text" name="whatsapp" id="whatsapp" value="{{ old('whatsapp') }}" placeholder="11 99999-9999" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Formato: DDD + Número (ex: 11 98765-4321)</p>
        </div>

        <div class="mb-5">
            <label for="allowed_guests" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Quantidade de Acompanhantes Permitidos</label>
            <input type="number" name="allowed_guests" id="allowed_guests" value="{{ old('allowed_guests', 0) }}" min="0" max="10" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" required>
            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">0 = Apenas o convidado principal.</p>
        </div>

        <div class="flex items-center gap-4">
            <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Salvar Convidado</button>
            <a href="{{ route('events.guests.index', $event->id) }}" class="text-gray-900 bg-white border border-gray-300 focus:outline-none hover:bg-gray-100 focus:ring-4 focus:ring-gray-200 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-gray-800 dark:text-white dark:border-gray-600 dark:hover:bg-gray-700 dark:hover:border-gray-600 dark:focus:ring-gray-700">Cancelar</a>
        </div>
    </form>
</div>
@endsection
