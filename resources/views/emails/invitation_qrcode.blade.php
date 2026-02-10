<!DOCTYPE html>
<html>
<head>
    <title>Seu QR Code para {{ $invitation->event->title }}</title>
</head>
<body style="font-family: sans-serif; text-align: center; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #eee; border-radius: 8px;">
        <h2 style="color: #2563eb;">Olá, {{ $invitation->guest_name }}!</h2>
        
        <p>Sua presença está confirmada no evento <strong>{{ $invitation->event->title }}</strong>.</p>
        
        <p>Data: {{ $invitation->event->start_time->format('d/m/Y H:i') }}<br>
           Local: {{ $invitation->event->location }}</p>
           
        <p>Abaixo está o seu QR Code de acesso. Apresente-o na entrada (impresso ou no celular).</p>
        
        <div style="margin: 30px 0;">
            <!-- Embedding the image directly since we are attaching it -->
            <img src="cid:qrcode.png" alt="QR Code" style="width: 250px; height: 250px; border: 2px solid #333; padding: 10px; border-radius: 8px;">
        </div>
        
        <p style="font-size: 12px; color: #777;">Este QR Code é único e intransferível.</p>
        
        <hr style="border: none; border-top: 1px solid #eee; margin: 20px 0;">
        
        <p style="font-size: 12px; color: #999;">Enviado por MyEvents</p>
    </div>
</body>
</html>
