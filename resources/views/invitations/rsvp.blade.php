@extends('layouts.app')

@section('content')
<div class="max-w-md mx-auto bg-white p-8 border border-gray-200 rounded-lg shadow-sm text-center">
    <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ $event->title }}</h1>
    <p class="text-gray-600 mb-6">{{ $event->start_time->format('d/m/Y H:i') }} • {{ $event->location }}</p>

    <div class="p-4 mb-6 bg-blue-50 text-blue-800 rounded-lg">
        <p>Olá, <strong>{{ $invitation->guest_name ?? $invitation->email }}</strong>!</p>
        <p>Você foi convidado para este evento. Por favor, confirme sua presença.</p>
    </div>

    @if($invitation->status === 'pending')
        <div class="flex justify-center gap-4">
            <form action="{{ route('invitations.rsvp.store', $invitation->token) }}" method="POST" id="confirmForm">
                @csrf
                <input type="hidden" name="status" value="confirmed">
                
                @if($invitation->allowed_guests > 0)
                    <div class="mb-4 text-left">
                        <label for="confirmed_guests" class="block mb-2 text-sm font-medium text-gray-900">Quantos acompanhantes você levará?</label>
                        <select name="confirmed_guests" id="confirmed_guests" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                            <option value="0">Vou sozinho(a)</option>
                            @for($i = 1; $i <= $invitation->allowed_guests; $i++)
                                <option value="{{ $i }}">+{{ $i }} acompanhante{{ $i > 1 ? 's' : '' }}</option>
                            @endfor
                        </select>
                    </div>
                @endif

                <button type="submit" class="text-white bg-green-600 hover:bg-green-700 focus:ring-4 focus:outline-none focus:ring-green-300 font-medium rounded-lg text-lg px-8 py-3 text-center w-full">Confirmar Presença</button>
            </form>
        </div>
        
        <div class="mt-4 flex justify-center">
            <form action="{{ route('invitations.rsvp.store', $invitation->token) }}" method="POST">
                @csrf
                <input type="hidden" name="status" value="declined">
                <button type="submit" class="text-gray-900 bg-white border border-gray-300 focus:outline-none hover:bg-gray-100 focus:ring-4 focus:ring-gray-200 font-medium rounded-lg text-lg px-8 py-3 text-center">Não Poderei Ir</button>
            </form>
        </div>
    @elseif($invitation->status === 'confirmed')
        <div class="text-green-600 mb-6">
            <svg class="w-16 h-16 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <h3 class="text-xl font-bold">Presença Confirmada!</h3>
            <p>Seu QR Code de acesso está pronto.</p>
        </div>
        <a href="{{ route('invitations.qrcode', $invitation->token) }}" class="inline-block text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5">Ver meu QR Code</a>
    @else
        <div class="text-red-600">
            <svg class="w-16 h-16 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <h3 class="text-xl font-bold">Convite Recusado</h3>
            <p>Esperamos vê-lo na próxima!</p>
        </div>
    @endif
</div>
@endsection
