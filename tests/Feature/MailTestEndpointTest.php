<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class MailTestEndpointTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        Config::set('mailtest.enabled', true);
    }

    public function test_simulation_returns_payload_without_sending()
    {
        Mail::fake();

        $resp = $this->postJson('/api/mail/test', [
            'subject' => 'Teste',
            'html' => '<p>Olá</p>',
            'to' => ['dev@example.com'],
            'simulate' => true,
        ]);

        $resp->assertOk()
            ->assertJsonPath('status', 'simulated')
            ->assertJsonPath('recipients.to.0', 'dev@example.com');

        Mail::assertNothingSent();
    }

    public function test_real_send_uses_multiple_recipients_and_attachments_validation()
    {
        Mail::fake();

        $tmp = tempnam(sys_get_temp_dir(), 'mailatt');
        file_put_contents($tmp, 'demo');

        $resp = $this->postJson('/api/mail/test', [
            'subject' => 'Teste Real',
            'html' => '<p>Body</p>',
            'to' => ['a@example.com', 'a@example.com'],
            'cc' => ['c@example.com'],
            'bcc' => ['b@example.com'],
            'attachments' => [
                ['path' => $tmp, 'name' => 'demo.txt', 'mime' => 'text/plain'],
            ],
            'simulate' => false,
        ]);

        $resp->assertOk()->assertJsonPath('status', 'sent')
            ->assertJsonPath('recipients.to.0', 'a@example.com');
    }

    public function test_invalid_template_returns_422()
    {
        $resp = $this->postJson('/api/mail/test', [
            'subject' => 'Teste',
            'template' => 'emails.inexistente',
            'to' => ['dev@example.com'],
            'simulate' => true,
        ]);

        $resp->assertStatus(422);
    }
}
