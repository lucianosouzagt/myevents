@extends('layouts.app')

@section('content')
<div class="flex justify-between items-center mb-8">
    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Meus Eventos</h1>
    <a href="{{ route('events.create') }}" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">
        Novo Evento
    </a>
</div>

<div class="relative overflow-x-auto shadow-md sm:rounded-lg">
    <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
            <tr>
                <th scope="col" class="px-6 py-3">Título</th>
                <th scope="col" class="px-6 py-3">Data</th>
                <th scope="col" class="px-6 py-3">Local</th>
                <th scope="col" class="px-6 py-3">Status</th>
                <th scope="col" class="px-6 py-3 text-right">Ações</th>
            </tr>
        </thead>
        <tbody>
            @forelse($events as $event)
                <tr class="bg-white border-b hover:bg-gray-50 dark:bg-gray-800 dark:border-gray-700 dark:hover:bg-gray-600">
                    <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                        <a href="{{ route('events.show', $event->id) }}" class="hover:underline hover:text-blue-600 dark:hover:text-blue-500">{{ $event->title }}</a>
                    </th>
                    <td class="px-6 py-4">
                        {{ $event->start_time->format('d/m/Y H:i') }}
                    </td>
                    <td class="px-6 py-4">
                        {{ $event->location }}
                    </td>
                    <td class="px-6 py-4">
                        @if($event->is_public)
                            <span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-green-900 dark:text-green-300">Público</span>
                        @else
                            <span class="bg-yellow-100 text-yellow-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-yellow-900 dark:text-yellow-300">Privado</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-right">
                        <a href="{{ route('events.edit', $event->id) }}" class="font-medium text-blue-600 hover:underline mr-3 dark:text-blue-500">Editar</a>
                        <form action="{{ route('events.destroy', $event->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Tem certeza que deseja excluir este evento?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="font-medium text-red-600 hover:underline dark:text-red-500">Excluir</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                    <td colspan="5" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                        Você ainda não criou nenhum evento.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
