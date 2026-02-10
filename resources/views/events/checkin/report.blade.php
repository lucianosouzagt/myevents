<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Relatório de Presença - {{ $event->title }}</title>
    <style>
        body { font-family: sans-serif; padding: 20px; }
        h1 { margin-bottom: 10px; }
        .meta { margin-bottom: 20px; color: #666; font-size: 14px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; font-size: 12px; }
        th { background-color: #f2f2f2; }
        .present { color: green; font-weight: bold; }
        .absent { color: red; }
        .summary { margin-bottom: 20px; padding: 10px; background: #f9f9f9; border: 1px solid #ddd; }
        @media print {
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <button onclick="window.print()" class="no-print" style="padding: 10px 20px; margin-bottom: 20px; cursor: pointer;">Imprimir / Salvar PDF</button>

    <h1>{{ $event->title }}</h1>
    <div class="meta">
        Data do Evento: {{ $event->start_time->format('d/m/Y H:i') }} <br>
        Local: {{ $event->location }}
    </div>

    @php
        $totalConfirmed = $event->invitations->count();
        $presentCount = 0;
        foreach($event->invitations as $invitation) {
            if($invitation->checkins->isNotEmpty()) $presentCount++;
        }
        $absentCount = $totalConfirmed - $presentCount;
    @endphp

    <div class="summary">
        <strong>Resumo:</strong><br>
        Total Confirmados: {{ $totalConfirmed }} <br>
        Presentes: {{ $presentCount }} <br>
        Ausentes: {{ $absentCount }}
    </div>

    <table>
        <thead>
            <tr>
                <th>Nome do Convidado</th>
                <th>Email</th>
                <th>Status</th>
                <th>Horário Check-in</th>
            </tr>
        </thead>
        <tbody>
            @foreach($event->invitations->sortBy('guest_name') as $guest)
                @php
                    $checkin = $guest->checkins->first();
                    $isPresent = $checkin !== null;
                @endphp
                <tr>
                    <td>{{ $guest->guest_name ?? 'N/A' }}</td>
                    <td>{{ $guest->email }}</td>
                    <td class="{{ $isPresent ? 'present' : 'absent' }}">
                        {{ $isPresent ? 'PRESENTE' : 'AUSENTE' }}
                    </td>
                    <td>
                        {{ $isPresent ? $checkin->checked_in_at->setTimezone('America/Sao_Paulo')->format('d/m/Y H:i:s') : '-' }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
