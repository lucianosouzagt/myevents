<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class MailWebExtraTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Config::set('mailtest.enabled', true);
    }

    public function test_missing_required_fields_return_422(): void
    {
        $resp = $this->postJson('/api/mail/test', [
            // missing subject and to
            'html' => '<p>Oi</p>',
            'simulate' => true,
        ]);
        $resp->assertStatus(422);
    }

    public function test_invalid_email_returns_422(): void
    {
        $resp = $this->postJson('/api/mail/test', [
            'subject' => 'Teste',
            'html' => '<p>Oi</p>',
            'to' => ['invalido@'],
            'simulate' => true,
        ]);
        $resp->assertStatus(422);
    }

    public function test_rate_limiting_returns_429_on_excess(): void
    {
        for ($i = 0; $i < 5; $i++) {
            $this->postJson('/api/mail/test', [
                'subject' => 'Teste '.$i,
                'html' => '<p>Oi</p>',
                'to' => ['dev@example.com'],
                'simulate' => true,
            ])->assertOk();
        }
        $this->postJson('/api/mail/test', [
            'subject' => 'Teste 6',
            'html' => '<p>Oi</p>',
            'to' => ['dev@example.com'],
            'simulate' => true,
        ])->assertStatus(429);
    }

    public function test_template_valid_sends_mail(): void
    {
        Mail::fake();

        $resp = $this->postJson('/api/mail/test', [
            'subject' => 'Template',
            'template' => 'emails.invitations.sent',
            'to' => ['dev@example.com'],
            'simulate' => false,
        ]);

        $resp->assertOk()->assertJsonPath('status', 'sent');
        Mail::assertSent(function () {
            return true;
        });
    }
}

