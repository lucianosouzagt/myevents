@extends('layouts.app')

@section('content')
<div class="max-w-screen-xl mx-auto">
    <!-- Removido o bloco de session('success') daqui pois já existe no layout -->
    
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Gerenciar Convidados</h1>
            <p class="text-gray-600 dark:text-gray-400">Evento: {{ $event->title }}</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('events.show', $event->id) }}" class="text-gray-900 bg-white border border-gray-300 focus:outline-none hover:bg-gray-100 focus:ring-4 focus:ring-gray-200 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-gray-800 dark:text-white dark:border-gray-600 dark:hover:bg-gray-700 dark:hover:border-gray-600 dark:focus:ring-gray-700">Voltar ao Evento</a>
            <a href="{{ route('events.guests.create', $event->id) }}" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Adicionar Convidado</a>
        </div>
    </div>

    <div class="bg-white border border-gray-200 rounded-lg shadow-sm dark:bg-gray-800 dark:border-gray-700 overflow-hidden">
        <div class="relative overflow-x-auto">
            <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        <th scope="col" class="px-6 py-3">Nome</th>
                        <th scope="col" class="px-6 py-3">Contatos</th>
                        <th scope="col" class="px-6 py-3">Acompanhantes</th>
                        <th scope="col" class="px-6 py-3">Status</th>
                        <th scope="col" class="px-6 py-3">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($invitations as $invitation)
                        <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                            <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                {{ $invitation->guest_name }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-col">
                                    @if($invitation->email)
                                        <span class="flex items-center gap-1">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                            {{ $invitation->email }}
                                        </span>
                                    @endif
                                    @if($invitation->whatsapp)
                                        <span class="flex items-center gap-1 text-green-600 dark:text-green-400">
                                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                                            {{ $invitation->whatsapp }}
                                        </span>
                                    @endif
                                    @if(!$invitation->email && !$invitation->whatsapp)
                                        <span class="text-xs text-gray-400 italic">Sem contatos</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                {{ $invitation->confirmed_guests }} / {{ $invitation->allowed_guests }}
                            </td>
                            <td class="px-6 py-4">
                                @if($invitation->status == 'confirmed')
                                    <span class="bg-green-100 text-green-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded dark:bg-green-900 dark:text-green-300">Confirmado</span>
                                @elseif($invitation->status == 'declined')
                                    <span class="bg-red-100 text-red-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded dark:bg-red-900 dark:text-red-300">Recusado</span>
                                @else
                                    <span class="bg-yellow-100 text-yellow-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded dark:bg-yellow-900 dark:text-yellow-300">Pendente</span>
                                @endif
                                
                                @if($invitation->logs->isNotEmpty())
                                    <div class="mt-1 text-xs text-gray-500">
                                        Último envio: {{ $invitation->logs->last()->sent_at->diffForHumans() }}
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('events.guests.edit', ['eventId' => $event->id, 'invitationId' => $invitation->id]) }}" class="font-medium text-blue-600 dark:text-blue-500 hover:underline">Editar</a>
                                    
                                    <form action="{{ route('events.guests.destroy', ['eventId' => $event->id, 'invitationId' => $invitation->id]) }}" method="POST" onsubmit="return confirm('Tem certeza?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="font-medium text-red-600 dark:text-red-500 hover:underline">Excluir</button>
                                    </form>

                                    @php
                                        $hasEmail = !empty($invitation->email);
                                        $hasWhatsapp = !empty($invitation->whatsapp);
                                    @endphp
                                    @if($hasEmail || $hasWhatsapp)
                                        <!-- Dropdown de Envio -->
                                        <button id="dropdownSendButton-{{ $invitation->id }}" data-dropdown-toggle="dropdownSend-{{ $invitation->id }}" class="text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:outline-none focus:ring-green-300 font-medium rounded-lg text-xs px-3 py-1.5 text-center inline-flex items-center dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800" type="button">
                                            Enviar <svg class="w-2.5 h-2.5 ms-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4"/></svg>
                                        </button>
                                    @else
                                        <span class="text-xs text-gray-400 italic">Sem canais de envio</span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                                Nenhum convidado cadastrado.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4">
            {{ $invitations->links() }}
        </div>
    </div>
</div>

<!-- Dropdowns moved outside the overflow-hidden container -->
@foreach($invitations as $invitation)
    @php
        $hasEmail = !empty($invitation->email);
        $hasWhatsapp = !empty($invitation->whatsapp);
    @endphp
    <div id="dropdownSend-{{ $invitation->id }}" class="z-50 hidden bg-white divide-y divide-gray-100 rounded-lg shadow w-44 dark:bg-gray-700">
        <ul class="py-2 text-sm text-gray-700 dark:text-gray-200" aria-labelledby="dropdownSendButton-{{ $invitation->id }}">
            @if($hasEmail)
                <li>
                    <form action="{{ route('events.guests.send', ['eventId' => $event->id, 'invitationId' => $invitation->id]) }}" method="POST">
                        @csrf
                        <input type="hidden" name="channels[]" value="email">
                        <button type="submit" class="block w-full px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white text-left text-blue-600 dark:text-blue-400">Enviar Convite (E-mail)</button>
                    </form>
                </li>
            @endif
            @if($invitation->status === 'confirmed' && $hasEmail)
                <li>
                    <form action="{{ route('events.guests.send_qrcode', ['eventId' => $event->id, 'invitationId' => $invitation->id]) }}" method="POST">
                        @csrf
                        <button type="submit" class="block w-full px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white text-left text-blue-600 dark:text-blue-400">Enviar QR Code (E-mail)</button>
                    </form>
                </li>
            @endif
             @if($hasWhatsapp)
                @php
                    $rsvpLink = route('invitations.show', $invitation->token);
                    
                    if ($event->whatsapp_message_template) {
                        // Use custom template
                        $message = $event->whatsapp_message_template;
                        $message = str_replace('@{{guest_name}}', $invitation->guest_name, $message);
                        $message = str_replace('@{{event_title}}', $event->title, $message);
                        $message = str_replace('@{{event_date}}', $event->start_time->format('d/m/Y H:i'), $message);
                        $message = str_replace('@{{event_location}}', $event->location, $message);
                        $message = str_replace('@{{rsvp_link}}', $rsvpLink, $message);
                        
                        // Handle {{ }} placeholders just in case user typed them that way
                        $message = str_replace('{{guest_name}}', $invitation->guest_name, $message);
                        $message = str_replace('{{event_title}}', $event->title, $message);
                        $message = str_replace('{{event_date}}', $event->start_time->format('d/m/Y H:i'), $message);
                        $message = str_replace('{{event_location}}', $event->location, $message);
                        $message = str_replace('{{rsvp_link}}', $rsvpLink, $message);

                        // Fallback: Se o link não estiver na mensagem, adiciona ao final
                        if (strpos($message, $rsvpLink) === false) {
                            $message .= "\n\nConfirme aqui: " . $rsvpLink;
                        }
                    } else {
                        // Default template
                        $message = "Olá {$invitation->guest_name}! Você foi convidado para o evento {$event->title}.\n\n";
                        $message .= "Data: {$event->start_time->format('d/m/Y H:i')}\n";
                        $message .= "Local: {$event->location}\n\n";
                        $message .= "Confirme sua presença aqui: {$rsvpLink}";
                    }
                    
                    $encodedMessage = rawurlencode($message);
                    
                    // Remove non-digits for wa.me link
                    $cleanPhone = preg_replace('/\D/', '', $invitation->whatsapp);
                    // Ensure country code (basic BR assumption if length matches)
                    if (strlen($cleanPhone) >= 10 && strlen($cleanPhone) <= 11) {
                         $cleanPhone = '55' . $cleanPhone;
                    }
                    $waLink = "https://wa.me/{$cleanPhone}?text={$encodedMessage}";
                @endphp
                <li>
                    <a href="{{ $waLink }}" target="_blank" class="block w-full px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white text-left text-green-600 dark:text-green-400">
                        Enviar Convite (WhatsApp)
                    </a>
                </li>
            @endif
            @if($invitation->status === 'confirmed' && $hasWhatsapp)
                @php
                    $qrLink = route('invitations.qrcode', $invitation->token);
                    $waQrMessage = "Olá {$invitation->guest_name}, aqui está seu QR Code de acesso para o evento {$event->title}: {$qrLink}";
                    $encodedQrMessage = rawurlencode($waQrMessage);
                    
                    $cleanPhone = preg_replace('/\D/', '', $invitation->whatsapp);
                    if (strlen($cleanPhone) >= 10 && strlen($cleanPhone) <= 11) {
                         $cleanPhone = '55' . $cleanPhone;
                    }
                    $waQrLink = "https://wa.me/{$cleanPhone}?text={$encodedQrMessage}";
                @endphp
                <li>
                    <a href="{{ $waQrLink }}" target="_blank" class="block w-full px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white text-left text-green-600 dark:text-green-400">
                        Enviar QR Code (WhatsApp)
                    </a>
                </li>
            @endif
        </ul>
    </div>
@endforeach
@endsection
