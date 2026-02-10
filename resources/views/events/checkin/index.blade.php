@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex flex-col md:flex-row justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">{{ $event->title }}</h1>
            <p class="text-gray-600 dark:text-gray-400">Check-in de Convidados</p>
        </div>
        <div class="flex gap-2 mt-4 md:mt-0">
            <a href="{{ route('events.show', $event->id) }}" class="text-gray-900 bg-white border border-gray-300 focus:outline-none hover:bg-gray-100 focus:ring-4 focus:ring-gray-200 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-gray-800 dark:text-white dark:border-gray-600 dark:hover:bg-gray-700 dark:hover:border-gray-600 dark:focus:ring-gray-700">
                Voltar
            </a>
            <a href="{{ route('events.checkin.report', $event->id) }}" target="_blank" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">
                <svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                Relatório PDF
            </a>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
        <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow border border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300">Total Confirmados</h3>
            <p class="text-3xl font-bold text-blue-600 dark:text-blue-400">{{ $totalConfirmed }}</p>
        </div>
        <div class="bg-green-50 dark:bg-green-900/20 p-4 rounded-lg shadow border border-green-200 dark:border-green-800">
            <h3 class="text-lg font-semibold text-green-700 dark:text-green-300">Presentes (Check-in)</h3>
            <p class="text-3xl font-bold text-green-600 dark:text-green-400">{{ $totalPresent }}</p>
        </div>
        <div class="bg-red-50 dark:bg-red-900/20 p-4 rounded-lg shadow border border-red-200 dark:border-red-800">
            <h3 class="text-lg font-semibold text-red-700 dark:text-red-300">Pendentes</h3>
            <p class="text-3xl font-bold text-red-600 dark:text-red-400">{{ $totalAbsent }}</p>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow border border-gray-200 dark:border-gray-700 mb-6">
        <form action="{{ route('events.checkin.index', $event->id) }}" method="GET" class="flex flex-col md:flex-row gap-4">
            <div class="flex-grow">
                <label for="search" class="sr-only">Buscar convidado</label>
                <div class="relative w-full">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"/>
                        </svg>
                    </div>
                    <input type="text" name="search" id="search" value="{{ request('search') }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full pl-10 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Buscar por nome ou email...">
                </div>
            </div>
            <div class="w-full md:w-48">
                <select name="status" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" onchange="this.form.submit()">
                    <option value="">Todos os Status</option>
                    <option value="present" {{ request('status') === 'present' ? 'selected' : '' }}>Presentes</option>
                    <option value="absent" {{ request('status') === 'absent' ? 'selected' : '' }}>Pendentes</option>
                </select>
            </div>
            <div class="w-full md:w-48">
                <select name="sort" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" onchange="this.form.submit()">
                    <option value="name" {{ request('sort') === 'name' ? 'selected' : '' }}>Ordem Alfabética</option>
                    <option value="arrival" {{ request('sort') === 'arrival' ? 'selected' : '' }}>Chegada (Recente)</option>
                </select>
            </div>
            <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800 md:hidden">
                Filtrar
            </button>
        </form>
    </div>

    <!-- Guests List -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-200 dark:border-gray-700 overflow-hidden">
        @if($guests->isEmpty())
            <div class="p-8 text-center text-gray-500 dark:text-gray-400">
                Nenhum convidado encontrado com os filtros selecionados.
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th scope="col" class="px-6 py-3">Convidado</th>
                            <th scope="col" class="px-6 py-3">Status</th>
                            <th scope="col" class="px-6 py-3">Horário Chegada</th>
                            <th scope="col" class="px-6 py-3 text-right">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($guests as $guest)
                            @php
                                $isCheckedIn = $guest->checkins->isNotEmpty();
                                $checkinTime = $isCheckedIn ? $guest->checkins->first()->checked_in_at : null;
                            @endphp
                            <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors {{ $isCheckedIn ? 'bg-green-50/30 dark:bg-green-900/10' : '' }}">
                                <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                    <div class="flex flex-col">
                                        <span class="text-base">{{ $guest->guest_name ?? 'Convidado Sem Nome' }}</span>
                                        <span class="text-xs text-gray-500">{{ $guest->email }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    @if($isCheckedIn)
                                        <span class="bg-green-100 text-green-800 text-xs font-medium mr-2 px-2.5 py-0.5 rounded dark:bg-green-900 dark:text-green-300 border border-green-200 dark:border-green-800">
                                            Presente
                                        </span>
                                    @else
                                        <span class="bg-red-100 text-red-800 text-xs font-medium mr-2 px-2.5 py-0.5 rounded dark:bg-red-900 dark:text-red-300 border border-red-200 dark:border-red-800">
                                            Pendente
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    {{ $checkinTime ? $checkinTime->setTimezone('America/Sao_Paulo')->format('H:i:s') : '-' }}
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <form action="{{ route('events.checkin.toggle', ['eventId' => $event->id, 'invitationId' => $guest->id]) }}" method="POST">
                                        @csrf
                                        @if($isCheckedIn)
                                            <button type="submit" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300 font-medium text-sm">
                                                Desfazer Check-in
                                            </button>
                                        @else
                                            <button type="submit" class="text-white bg-green-600 hover:bg-green-700 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-4 py-2 dark:bg-green-600 dark:hover:bg-green-700 focus:outline-none dark:focus:ring-green-800">
                                                Fazer Check-in
                                            </button>
                                        @endif
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
@endsection
