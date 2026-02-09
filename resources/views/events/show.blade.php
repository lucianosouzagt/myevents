@extends('layouts.app')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <!-- Detalhes do Evento -->
    <div class="lg:col-span-2 space-y-8">
        <div class="bg-white p-8 border border-gray-200 rounded-lg shadow-sm dark:bg-gray-800 dark:border-gray-700">
            <div class="flex justify-between items-start mb-6">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 mb-2 dark:text-white">{{ $event->title }}</h1>
                    <div class="flex items-center text-gray-500 text-sm dark:text-gray-400">
                        <span class="mr-4">Organizado por {{ $event->organizer->name }}</span>
                        @if($event->is_public)
                            <span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-green-900 dark:text-green-300">Público</span>
                        @else
                            <span class="bg-yellow-100 text-yellow-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-yellow-900 dark:text-yellow-300">Privado</span>
                        @endif
                    </div>
                </div>
                @if(auth()->id() === $event->organizer_id)
                    <div class="flex gap-2">
                        <a href="{{ route('events.edit', $event->id) }}" class="text-blue-600 hover:underline text-sm dark:text-blue-500">Editar</a>
                    </div>
                @endif
            </div>

            <div class="prose max-w-none text-gray-600 mb-8 dark:text-gray-300">
                <p>{{ $event->description }}</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 border-t pt-6 dark:border-gray-700">
                <div>
                    <h3 class="font-semibold text-gray-900 mb-2 dark:text-white">Data e Hora</h3>
                    <p class="text-gray-600 dark:text-gray-300">Início: {{ $event->start_time->format('d/m/Y H:i') }}</p>
                    @if($event->end_time)
                        <p class="text-gray-600 dark:text-gray-300">Fim: {{ $event->end_time->format('d/m/Y H:i') }}</p>
                    @endif
                </div>
                <div>
                    <h3 class="font-semibold text-gray-900 mb-2 dark:text-white">Localização</h3>
                    <p class="text-gray-600 mb-2 dark:text-gray-300">{{ $event->location }}</p>
                    @if($event->google_maps_link)
                        <a href="{{ $event->google_maps_link }}" target="_blank" class="text-blue-600 hover:underline text-sm flex items-center mb-2 dark:text-blue-500">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            Ver no Google Maps
                        </a>
                    @endif
                </div>
            </div>

            <!-- Mapa -->
            @if(isset($coordinates))
                <div class="mt-6">
                    <h3 class="font-semibold text-gray-900 mb-2 text-sm dark:text-white">Visualização do local</h3>
                    <div id="map" class="w-full h-[200px] rounded-lg shadow-sm z-0"></div>
                </div>
                
                <!-- Leaflet CSS & JS -->
                <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
                <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
                
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        var lat = {{ $coordinates['lat'] }};
                        var lng = {{ $coordinates['lng'] }};
                        
                        var map = L.map('map').setView([lat, lng], 15);

                        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
                        }).addTo(map);

                        L.marker([lat, lng]).addTo(map)
                            .bindPopup('{{ $event->location }}')
                            .openPopup();
                    });
                </script>
            @endif
        </div>

        <!-- Área do Organizador: Convites e Check-in -->
        @if(auth()->id() === $event->organizer_id)
            <div class="bg-white p-8 border border-gray-200 rounded-lg shadow-sm dark:bg-gray-800 dark:border-gray-700">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Gerenciar Convidados</h2>
                    <a href="{{ route('events.guests.index', $event->id) }}" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Gerenciar Lista Completa</a>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                    <div class="p-4 bg-gray-50 rounded-lg dark:bg-gray-700">
                        <p class="text-sm text-gray-500 dark:text-gray-400">Total de Convidados</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $event->invitations->count() }}</p>
                    </div>
                    <div class="p-4 bg-green-50 rounded-lg dark:bg-green-900/30">
                        <p class="text-sm text-green-600 dark:text-green-400">Confirmados</p>
                        <p class="text-2xl font-bold text-green-700 dark:text-green-300">{{ $event->invitations->where('status', 'confirmed')->count() }}</p>
                    </div>
                </div>

                <div class="relative overflow-x-auto">
                    <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                            <tr>
                                <th scope="col" class="px-6 py-3">Email</th>
                                <th scope="col" class="px-6 py-3">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($event->invitations->take(5) as $invitation)
                                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                    <td class="px-6 py-4">{{ $invitation->email }}</td>
                                    <td class="px-6 py-4">
                                        @if($invitation->status == 'confirmed')
                                            <span class="text-green-600 font-bold dark:text-green-400">Confirmado</span>
                                        @elseif($invitation->status == 'declined')
                                            <span class="text-red-600 dark:text-red-400">Recusado</span>
                                        @else
                                            <span class="text-yellow-600 dark:text-yellow-400">Pendente</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @if($event->invitations->count() > 5)
                        <div class="mt-4 text-center">
                            <a href="{{ route('events.guests.index', $event->id) }}" class="text-blue-600 hover:underline text-sm dark:text-blue-500">Ver todos os convidados</a>
                        </div>
                    @endif
                </div>
            </div>
            
             <div class="bg-white p-8 border border-gray-200 rounded-lg shadow-sm mt-8 dark:bg-gray-800 dark:border-gray-700">
                <h2 class="text-2xl font-bold mb-6 text-gray-900 dark:text-white">Realizar Check-in</h2>
                <form action="{{ route('checkin.store') }}" method="POST" class="flex gap-2">
                    @csrf
                    <input type="hidden" name="event_id" value="{{ $event->id }}">
                    <input type="text" name="token" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Token do QR Code" required>
                    <button type="submit" class="text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:outline-none focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800">Validar Entrada</button>
                </form>
            </div>
        @endif
    </div>

    <!-- Sidebar / Participantes Confirmados -->
    <div class="lg:col-span-1">
        <div class="bg-white p-6 border border-gray-200 rounded-lg shadow-sm dark:bg-gray-800 dark:border-gray-700">
            <h3 class="text-lg font-bold text-gray-900 mb-4 dark:text-white">Participantes Confirmados</h3>
            <div class="flex items-center justify-between mb-4">
                <span class="text-sm text-gray-600 dark:text-gray-400">Total</span>
                <span class="text-lg font-bold text-blue-600 dark:text-blue-500">{{ $event->attendees->count() }} / {{ $event->capacity }}</span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-2.5 mb-6 dark:bg-gray-700">
                <div class="bg-blue-600 h-2.5 rounded-full dark:bg-blue-500" style="width: {{ min(100, ($event->attendees->count() / $event->capacity) * 100) }}%"></div>
            </div>
            
            <ul class="space-y-3">
                @forelse($event->attendees->take(10) as $attendee)
                    <li class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-gray-300 flex items-center justify-center text-xs font-bold text-gray-600 dark:bg-gray-600 dark:text-gray-300">
                            {{ substr($attendee->name ?? $attendee->email, 0, 2) }}
                        </div>
                        <span class="text-sm text-gray-700 truncate dark:text-gray-300">{{ $attendee->name ?? $attendee->email }}</span>
                    </li>
                @empty
                    <li class="text-sm text-gray-500 dark:text-gray-400">Nenhum participante confirmado ainda.</li>
                @endforelse
            </ul>
        </div>

        @if($event->invitation_image_path)
            <div class="mt-6 bg-white p-6 border border-gray-200 rounded-lg shadow-sm dark:bg-gray-800 dark:border-gray-700">
                <h3 class="text-lg font-bold text-gray-900 mb-4 dark:text-white">Convite</h3>
                <div class="flex justify-center">
                    <img src="{{ Storage::url($event->invitation_image_path) }}" alt="Convite: {{ $event->title }}" class="rounded-lg shadow-sm border border-gray-200 dark:border-gray-600 max-w-full h-auto cursor-pointer hover:opacity-90 transition-opacity" onclick="window.open(this.src, '_blank')">
                </div>
                <p class="mt-2 text-xs text-center text-gray-500 dark:text-gray-400">Clique para ampliar</p>
            </div>
        @endif
    </div>
</div>
@endsection
