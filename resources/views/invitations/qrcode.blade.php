@extends('layouts.app')

@section('content')
<div class="max-w-md mx-auto bg-white p-8 border border-gray-200 rounded-lg shadow-sm text-center">
    <h1 class="text-2xl font-bold text-gray-900 mb-4">Seu Ingresso</h1>
    <p class="text-gray-600 mb-8">{{ $event->title }}</p>

    <div class="flex justify-center mb-8">
        {!! $qrCode !!}
    </div>

    <p class="text-sm text-gray-500 mb-4">Apresente este QR Code na entrada do evento.</p>
    
    <p class="text-xs font-mono bg-gray-100 p-3 rounded break-all mx-auto max-w-full text-gray-600 mb-6">
        {{ $token }}
    </p>

    <div class="mt-8">
        <a href="{{ route('invitations.show', $token) }}" class="text-blue-600 hover:underline">Voltar para o convite</a>
    </div>
</div>
@endsection
