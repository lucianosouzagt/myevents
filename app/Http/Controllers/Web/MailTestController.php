<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\MailTestService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Throwable;

class MailTestController extends Controller
{
    public function __construct(private MailTestService $service)
    {
    }

    public function send(Request $request): JsonResponse
    {
        if (!Config::get('mailtest.enabled', false)) {
            return response()->json(['error' => 'Mail test desativado'], 403);
        }
        $validated = $request->validate([
            'subject' => ['nullable', 'string', 'max:255'],
            'template' => ['nullable', 'string'],
            'html' => ['nullable', 'string'],
            'data' => ['array'],
            'to' => ['required', 'array', 'min:1'],
            'to.*' => ['string'],
            'cc' => ['array'],
            'cc.*' => ['string'],
            'bcc' => ['array'],
            'bcc.*' => ['string'],
            'attachments' => ['array'],
            'attachments.*.path' => ['required', 'string'],
            'attachments.*.name' => ['nullable', 'string'],
            'attachments.*.mime' => ['nullable', 'string'],
            'simulate' => ['sometimes', 'boolean'],
        ]);

        $simulate = (bool) ($validated['simulate'] ?? false);
        try {
            $result = $this->service->send($validated, $simulate);
            return response()->json($result, 200);
        } catch (Throwable $e) {
            Log::channel(config('mailtest.channel', 'mailtest'))->error('MailTest failed', [
                'message' => $e->getMessage(),
                'trace' => app()->hasDebugModeEnabled() ? $e->getTraceAsString() : null,
            ]);
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }
}

