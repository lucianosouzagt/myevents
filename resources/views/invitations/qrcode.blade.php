@extends('layouts.app')

@section('content')
<div class="max-w-md mx-auto bg-white p-8 border border-gray-200 rounded-lg shadow-sm text-center">
    <h1 class="text-2xl font-bold text-gray-900 mb-4">Seu Ingresso</h1>
    <p class="text-gray-600 mb-8">{{ $event->title }}</p>

    <div class="flex justify-center mb-8">
        {!! $qrCode !!}
    </div>

    <p class="text-sm text-gray-500 mb-4">Apresente este QR Code na entrada do evento.</p>
    <p class="font-mono bg-gray-100 p-2 rounded text-xs">{{ $token }}</p>

    <div class="mt-8">
        <a href="{{ route('events.show', $event->id) }}" class="text-blue-600 hover:underline">Voltar para o evento</a>
    </div>
</div>
@endsection
