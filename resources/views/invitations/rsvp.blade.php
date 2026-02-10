@extends('layouts.app')

@section('content')
<div class="max-w-md lg:max-w-5xl mx-auto bg-white border border-gray-200 rounded-lg shadow-sm overflow-hidden dark:bg-gray-800 dark:border-gray-700">
    <div class="flex flex-col lg:flex-row min-h-[500px]">
        @if($event->invitation_image_path)
            <!-- Open Graph Meta Tags for WhatsApp Preview -->
            @section('head')
                <meta property="og:title" content="Convite: {{ $event->title }}" />
                <meta property="og:description" content="{{ $event->start_time->format('d/m/Y H:i') }} • {{ $event->location }}" />
                <meta property="og:image" content="{{ asset('storage/' . $event->invitation_image_path) }}" />
                <meta property="og:url" content="{{ url()->current() }}" />
                <meta property="og:type" content="website" />
            @endsection

            <!-- Imagem (Coluna Esquerda no Desktop, Topo no Mobile) -->
            <div class="lg:w-1/2 h-96 lg:h-auto relative bg-white dark:bg-gray-800 flex items-center justify-center p-4 mt-2.5 lg:mt-0">
                <img src="{{ asset('storage/' . $event->invitation_image_path) }}" 
                     alt="Convite" 
                     class="w-full h-full object-contain max-h-[600px]">
            </div>
        @else
            <!-- Placeholder caso não tenha imagem (opcional, ou apenas removemos a coluna) -->
            <div class="hidden lg:block lg:w-1/3 bg-gray-100 dark:bg-gray-700 flex items-center justify-center">
                <svg class="w-24 h-24 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
            </div>
        @endif

        <!-- Conteúdo (Coluna Direita no Desktop) -->
        <div class="{{ $event->invitation_image_path ? 'lg:w-1/2' : 'w-full' }} p-8 flex flex-col justify-center text-center lg:text-left">
            <h1 class="text-3xl lg:text-4xl font-bold text-gray-900 dark:text-white mb-2">{{ $event->title }}</h1>
            <p class="text-gray-600 dark:text-gray-400 mb-6 text-lg">
                <span class="inline-block bg-blue-100 text-blue-800 text-sm font-medium px-2.5 py-0.5 rounded dark:bg-blue-900 dark:text-blue-300 mb-2 lg:mb-0 lg:mr-2">
                    {{ $event->start_time->format('d/m/Y H:i') }}
                </span>
                <span class="block lg:inline mt-1 lg:mt-0">
                    @if($event->location_url)
                        📍 <a href="{{ $event->location_url }}" target="_blank" class="hover:underline hover:text-blue-600 dark:hover:text-blue-400 transition-colors">{{ $event->location }}</a>
                    @elseif($event->google_maps_link)
                        📍 <a href="{{ $event->google_maps_link }}" target="_blank" class="hover:underline hover:text-blue-600 dark:hover:text-blue-400 transition-colors">{{ $event->location }}</a>
                    @else
                        📍 <a href="https://www.google.com/maps/search/?api=1&query={{ urlencode($event->location) }}" target="_blank" class="hover:underline hover:text-blue-600 dark:hover:text-blue-400 transition-colors">{{ $event->location }}</a>
                    @endif
                </span>
            </p>

            <div class="p-4 mb-8 bg-blue-50 text-blue-800 rounded-lg dark:bg-blue-900/30 dark:text-blue-300 border border-blue-100 dark:border-blue-800">
                <p class="text-lg">Olá, <strong>{{ $invitation->guest_name ?? $invitation->email }}</strong>!</p>
                <p class="mt-1">Você foi convidado para este evento. Por favor, confirme sua presença.</p>
                @if($event->rsvp_deadline)
                    <p class="mt-2 text-sm font-semibold text-red-600 dark:text-red-400">
                        ⚠️ Data limite para confirmação: {{ $event->rsvp_deadline->format('d/m/Y H:i') }}
                    </p>
                @endif
            </div>

            @if($event->rsvp_deadline && now()->gt($event->rsvp_deadline) && $invitation->status === 'pending')
                <div class="p-4 mb-6 bg-red-50 text-red-800 rounded-lg dark:bg-red-900/30 dark:text-red-300 border border-red-100 dark:border-red-800">
                    <div class="flex items-center gap-2">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <span class="font-bold">Prazo Encerrado</span>
                    </div>
                    <p class="mt-1">Infelizmente o prazo para confirmar presença neste evento já encerrou.</p>
                </div>
            @elseif($invitation->status === 'pending')
                <div class="w-full">
                    <form action="{{ route('invitations.rsvp.store', $invitation->token) }}" method="POST" id="confirmForm">
                        @csrf
                        <input type="hidden" name="status" value="confirmed">
                        
                        @if($invitation->allowed_guests > 0)
                            <div class="mb-6 text-left">
                                <label for="confirmed_guests" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Quantos acompanhantes você levará?</label>
                                <select name="confirmed_guests" id="confirmed_guests" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-3 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                    <option value="0">Vou sozinho(a)</option>
                                    @for($i = 1; $i <= $invitation->allowed_guests; $i++)
                                        <option value="{{ $i }}">+{{ $i }} acompanhante{{ $i > 1 ? 's' : '' }}</option>
                                    @endfor
                                </select>
                            </div>
                        @endif

                        <div class="flex flex-col gap-3 sm:flex-row lg:justify-start">
                            <button type="submit" class="text-white bg-green-600 hover:bg-green-700 focus:ring-4 focus:outline-none focus:ring-green-300 font-medium rounded-lg text-lg px-8 py-3 text-center w-full sm:w-auto dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800 transition-colors shadow-md">
                                Confirmar Presença
                            </button>
                        
                            <button type="submit" formaction="{{ route('invitations.rsvp.store', $invitation->token) }}" name="status" value="declined" class="text-gray-900 bg-white border border-gray-300 focus:outline-none hover:bg-gray-100 focus:ring-4 focus:ring-gray-200 font-medium rounded-lg text-lg px-8 py-3 text-center w-full sm:w-auto dark:bg-gray-800 dark:text-white dark:border-gray-600 dark:hover:bg-gray-700 dark:hover:border-gray-600 dark:focus:ring-gray-700 transition-colors">
                                Não Poderei Ir
                            </button>
                        </div>
                    </form>
                </div>
            @elseif($invitation->status === 'confirmed')
                <div class="text-green-600 dark:text-green-400 mb-6 flex flex-col items-center lg:items-start">
                    <div class="flex items-center gap-2 mb-2">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        <h3 class="text-2xl font-bold">Presença Confirmada!</h3>
                    </div>
                    <p class="text-lg">Seu QR Code de acesso está pronto.</p>
                </div>
                <div class="flex flex-col gap-3 lg:items-start">
                    <!-- Botão Ver QR Code (Existente) -->
                    <a href="{{ route('invitations.qrcode', $invitation->token) }}" class="inline-flex items-center justify-center text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-lg px-6 py-3 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800 shadow-md transition-transform hover:scale-105 w-full sm:w-auto">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 013.75 9.375v-4.5zM3.75 14.625c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5a1.125 1.125 0 01-1.125-1.125v-4.5zM13.5 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0113.5 9.375v-4.5z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 6.75h.75v.75h-.75v-.75zM6.75 16.5h.75v.75h-.75v-.75zM16.5 6.75h.75v.75h-.75v-.75zM13.5 13.5h.75v.75h-.75v-.75zM13.5 19.5h.75v.75h-.75v-.75zM19.5 13.5h.75v.75h-.75v-.75zM19.5 19.5h.75v.75h-.75v-.75zM16.5 16.5h.75v.75h-.75v-.75z"></path>
                        </svg>
                        Ver meu QR Code
                    </a>
                    
                    @if(!$event->rsvp_deadline || now()->lte($event->rsvp_deadline))
                        <form action="{{ route('invitations.rsvp.store', $invitation->token) }}" method="POST" class="w-full sm:w-auto mt-2 text-center lg:text-left">
                            @csrf
                            <input type="hidden" name="status" value="declined">
                            <button type="submit" class="text-gray-500 hover:text-gray-700 underline text-sm dark:text-gray-400 dark:hover:text-gray-200">
                                Alterar resposta: Não poderei ir
                            </button>
                        </form>
                        
                        @if($invitation->allowed_guests > 0)
                            <div class="mt-4 w-full border-t pt-4 dark:border-gray-700">
                                <h4 class="font-semibold mb-2 text-gray-900 dark:text-white">Alterar Acompanhantes</h4>
                                <form action="{{ route('invitations.rsvp.store', $invitation->token) }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="status" value="confirmed">
                                    <label for="confirmed_guests_change" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Total de Acompanhantes</label>
                                    <div class="flex gap-2">
                                        <select name="confirmed_guests" id="confirmed_guests_change" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                            <option value="0" {{ $invitation->confirmed_guests == 0 ? 'selected' : '' }}>Vou sozinho(a)</option>
                                            @for($i = 1; $i <= $invitation->allowed_guests; $i++)
                                                <option value="{{ $i }}" {{ $invitation->confirmed_guests == $i ? 'selected' : '' }}>+{{ $i }} acompanhante{{ $i > 1 ? 's' : '' }}</option>
                                            @endfor
                                        </select>
                                        <button type="submit" class="text-white bg-blue-600 hover:bg-blue-700 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-4 py-2 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                                            Atualizar
                                        </button>
                                    </div>
                                </form>
                            </div>
                        @endif
                    @endif
                </div>
            @else
                <div class="text-red-600 dark:text-red-400 flex flex-col items-center lg:items-start">
                    <div class="flex items-center gap-2 mb-2">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <h3 class="text-2xl font-bold">Convite Recusado</h3>
                    </div>
                    <p class="text-lg">Esperamos vê-lo na próxima!</p>
                    
                    @if(!$event->rsvp_deadline || now()->lte($event->rsvp_deadline))
                        <form action="{{ route('invitations.rsvp.store', $invitation->token) }}" method="POST" class="mt-4 w-full">
                            @csrf
                            <input type="hidden" name="status" value="confirmed">
                            @if($invitation->allowed_guests > 0)
                                <input type="hidden" name="confirmed_guests" value="0"> <!-- Default to 0 if quick changing back -->
                            @endif
                            <button type="submit" class="text-blue-600 hover:text-blue-800 underline text-sm dark:text-blue-400 dark:hover:text-blue-300">
                                Mudei de ideia: Confirmar Presença
                            </button>
                        </form>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
