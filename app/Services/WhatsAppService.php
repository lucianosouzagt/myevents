<?php

namespace App\Services;

use Twilio\Rest\Client;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    protected $client;
    protected $from;

    public function __construct()
    {
        // Mock client if credentials are not present or for testing
        if (config('services.twilio.sid') && config('services.twilio.token')) {
            $this->client = new Client(config('services.twilio.sid'), config('services.twilio.token'));
            $this->from = config('services.twilio.whatsapp_from');
        }
    }

    public function sendMessage(string $to, string $message, bool $debug = false)
    {
        // Debug Mode: Just log content
        if ($debug) {
            Log::info("WhatsApp Debug - To: $to, Message: $message");
            return [
                'status' => 'debug',
                'sid' => 'debug_' . uniqid(),
                'response' => 'Debug mode: Message logged only.'
            ];
        }

        // Format number: Remove non-digits, ensure +55
        $to = preg_replace('/\D/', '', $to);
        if (!str_starts_with($to, '55')) {
             // Basic assumption for BR numbers if missing country code
             $to = '55' . $to;
        }
        
        // Twilio requires "whatsapp:" prefix
        $formattedTo = "whatsapp:+" . $to;
        $formattedFrom = "whatsapp:" . ($this->from ?? '+14155238886'); // Default Sandbox number

        if (!$this->client) {
             Log::warning("WhatsApp Client not configured. Message to $to failed.");
             return [
                 'status' => 'failed',
                 'response' => 'Twilio credentials not configured.'
             ];
        }

        try {
            $messageInstance = $this->client->messages->create(
                $formattedTo,
                [
                    'from' => $formattedFrom,
                    'body' => $message
                ]
            );

            return [
                'status' => 'sent', // Twilio status is usually 'queued', we assume sent for simplicity
                'sid' => $messageInstance->sid,
                'response' => $messageInstance->status
            ];

        } catch (\Exception $e) {
            Log::error("Twilio Error: " . $e->getMessage());
            return [
                'status' => 'failed',
                'response' => $e->getMessage()
            ];
        }
    }
}
