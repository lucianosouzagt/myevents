<?php

namespace App\Http\Controllers\Web;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Arr;
use Illuminate\Http\JsonResponse;

class MailTestController extends Controller
{
    public function send(Request $request): JsonResponse
    {
        if (!config('mailtest.enabled', false) && app()->environment('production')) {
            abort(404);
        }

        $data = $request->validate([
            'subject' => 'required|string|max:255',
            'html' => 'nullable|string',
            'template' => 'nullable|string',
            'to' => 'required|array|min:1',
            'to.*' => 'email',
            'cc' => 'sometimes|array',
            'cc.*' => 'email',
            'bcc' => 'sometimes|array',
            'bcc.*' => 'email',
            'attachments' => 'sometimes|array',
            'attachments.*.path' => 'required_with:attachments|string',
            'attachments.*.name' => 'sometimes|string',
            'attachments.*.mime' => 'sometimes|string',
            'simulate' => 'sometimes|boolean',
        ]);

        $to = array_values(array_unique($data['to']));
        $cc = array_values(array_unique($data['cc'] ?? []));
        $bcc = array_values(array_unique($data['bcc'] ?? []));

        if (empty($data['html']) && empty($data['template'])) {
            return response()->json(['message' => 'Informe html ou template.'], 422);
        }

        if (!empty($data['template']) && !View::exists($data['template'])) {
            return response()->json(['message' => 'Template inválido.'], 422);
        }

        $payload = [
            'subject' => $data['subject'],
            'html' => $data['html'] ?? null,
            'template' => $data['template'] ?? null,
            'recipients' => compact('to', 'cc', 'bcc'),
            'attachments' => $data['attachments'] ?? [],
        ];

        if (!empty($data['simulate'])) {
            return response()->json(['status' => 'simulated'] + $payload);
        }

        Mail::send([], [], function ($message) use ($data, $to, $cc, $bcc) {
            $message->subject($data['subject'])
                ->to($to);
            if (!empty($cc)) $message->cc($cc);
            if (!empty($bcc)) $message->bcc($bcc);

            if (!empty($data['template'])) {
                $message->setBody(view($data['template'])->render(), 'text/html');
            } else {
                $message->setBody($data['html'] ?? '', 'text/html');
            }

            foreach ($data['attachments'] ?? [] as $att) {
                $path = Arr::get($att, 'path');
                if ($path && is_readable($path)) {
                    $name = Arr::get($att, 'name');
                    $mime = Arr::get($att, 'mime');
                    $message->attach($path, array_filter(['as' => $name, 'mime' => $mime]));
                }
            }
        });

        return response()->json(['status' => 'sent'] + $payload);
    }
}

