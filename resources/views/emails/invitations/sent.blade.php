<x-mail::message>
# Olá, {{ $invitation->guest_name ?? 'Convidado' }}!

Você foi convidado para o evento **{{ $event->title }}**.

**Data:** {{ $event->start_time->format('d/m/Y H:i') }}  
**Local:** {{ $event->location }}

Por favor, confirme sua presença clicando no botão abaixo.

<x-mail::button :url="$url">
Confirmar Presença
</x-mail::button>

Obrigado,<br>
{{ config('app.name') }}
</x-mail::message>
