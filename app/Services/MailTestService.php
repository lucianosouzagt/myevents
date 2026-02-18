<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use InvalidArgumentException;

class MailTestService
{
    public function validateRecipients(array $to, array $cc = [], array $bcc = []): array
    {
        $all = ['to' => $to, 'cc' => $cc, 'bcc' => $bcc];
        foreach ($all as $key => $list) {
            $list = array_values(array_filter(array_map('trim', $list)));
            foreach ($list as $email) {
                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    throw new InvalidArgumentException("E-mail inválido em {$key}: {$email}");
                }
            }
            $all[$key] = array_values(array_unique($list));
        }
        return $all;
    }

    public function validateAttachments(array $attachments): array
    {
        $validated = [];
        foreach ($attachments as $att) {
            $path = Arr::get($att, 'path');
            $name = Arr::get($att, 'name', basename((string) $path));
            $mime = Arr::get($att, 'mime');
            if (!$path || !is_readable($path)) {
                throw new InvalidArgumentException("Anexo inválido ou inacessível: {$path}");
            }
            $size = @filesize($path) ?: 0;
            if ($size > 8 * 1024 * 1024) {
                throw new InvalidArgumentException("Anexo excede 8MB: {$name}");
            }
            $validated[] = ['path' => $path, 'name' => $name, 'mime' => $mime];
        }
        return $validated;
    }

    public function renderTemplate(?string $template, array $data, ?string $html): string
    {
        if ($html && Str::of($html)->trim()->isNotEmpty()) {
            return $html;
        }
        if ($template) {
            if (!View::exists($template)) {
                throw new InvalidArgumentException("Template não encontrado: {$template}");
            }
            $rendered = view($template, $data)->render();
            if (!Str::of($rendered)->trim()->isNotEmpty()) {
                throw new InvalidArgumentException("Template renderizou vazio: {$template}");
            }
            return $rendered;
        }
        throw new InvalidArgumentException('Informe "html" ou "template".');
    }

    public function send(array $payload, bool $simulate = false): array
    {
        $subject = trim((string) Arr::get($payload, 'subject', 'Teste de E-mail'));
        $data = (array) Arr::get($payload, 'data', []);
        $template = Arr::get($payload, 'template');
        $html = Arr::get($payload, 'html');
        $attachments = (array) Arr::get($payload, 'attachments', []);
        $recipients = $this->validateRecipients(
            (array) Arr::get($payload, 'to', []),
            (array) Arr::get($payload, 'cc', []),
            (array) Arr::get($payload, 'bcc', []),
        );
        if (empty($recipients['to'])) {
            throw new InvalidArgumentException('Pelo menos um destinatário em "to" é obrigatório.');
        }
        $attachments = $this->validateAttachments($attachments);
        $body = $this->renderTemplate($template, $data, $html);

        $logCtx = [
            'subject' => $subject,
            'to' => $recipients['to'],
            'cc' => $recipients['cc'],
            'bcc' => $recipients['bcc'],
            'attachments' => array_map(fn ($a) => Arr::only($a, ['name', 'path', 'mime']), $attachments),
            'simulate' => $simulate,
        ];
        Log::channel(config('mailtest.channel', 'mailtest'))->info('MailTest request received', $logCtx);

        if ($simulate) {
            return [
                'status' => 'simulated',
                'subject' => $subject,
                'recipients' => $recipients,
                'attachments' => $attachments,
                'preview_bytes' => min(strlen($body), 200),
            ];
        }

        Mail::html($body, function ($message) use ($subject, $recipients, $attachments) {
            $message->subject($subject);
            foreach ($recipients['to'] as $email) {
                $message->to($email);
            }
            foreach ($recipients['cc'] as $email) {
                $message->cc($email);
            }
            foreach ($recipients['bcc'] as $email) {
                $message->bcc($email);
            }
            foreach ($attachments as $att) {
                $message->attach($att['path'], array_filter([
                    'as' => $att['name'] ?? null,
                    'mime' => $att['mime'] ?? null,
                ]));
            }
        });

        Log::channel(config('mailtest.channel', 'mailtest'))->info('MailTest sent successfully', [
            'to' => $recipients['to'],
            'subject' => $subject,
        ]);

        return [
            'status' => 'sent',
            'subject' => $subject,
            'recipients' => $recipients,
            'attachments' => $attachments,
        ];
    }
}

