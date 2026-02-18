<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\Invitation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class GuestSendOptionsVisibilityTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Ensure default app key exists for encryption during tests
        config(['app.key' => 'base64:'.base64_encode(random_bytes(32))]);
    }

    protected function createEventAndLogin(): array
    {
        $user = User::factory()->create();
        $event = Event::factory()->create(['organizer_id' => $user->id]);
        $this->actingAs($user);
        return [$user, $event];
    }

    protected function makeInvitation(Event $event, array $attrs = []): Invitation
    {
        $defaults = [
            'event_id' => $event->id,
            'email' => 'guest@example.com',
            'whatsapp' => '559999999999',
            'guest_name' => 'Convidado Teste',
            'allowed_guests' => 0,
            'confirmed_guests' => 0,
            'token' => Str::uuid()->toString(),
            'status' => 'pending',
        ];
        return Invitation::create(array_merge($defaults, $attrs));
    }

    public function test_mostra_opcoes_de_email_e_whatsapp_quando_ambos_presentes(): void
    {
        [$user, $event] = $this->createEventAndLogin();
        $this->makeInvitation($event, [
            'email' => 'guest@example.com',
            'whatsapp' => '559999999999',
        ]);

        $resp = $this->get(route('events.guests.index', $event->id));
        $resp->assertOk();
        $resp->assertSee('Enviar Convite (E-mail)', false);
        $resp->assertSee('Enviar Convite (WhatsApp)', false);
    }

    public function test_mostra_apenas_opcao_de_email_quando_sem_whatsapp(): void
    {
        [$user, $event] = $this->createEventAndLogin();
        $this->makeInvitation($event, [
            'email' => 'guest@example.com',
            'whatsapp' => null,
        ]);

        $resp = $this->get(route('events.guests.index', $event->id));
        $resp->assertOk();
        $resp->assertSee('Enviar Convite (E-mail)', false);
        $resp->assertDontSee('Enviar Convite (WhatsApp)', false);
    }

    public function test_mostra_apenas_opcao_de_whatsapp_quando_sem_email(): void
    {
        [$user, $event] = $this->createEventAndLogin();
        // Como a coluna email não é nula, usamos string vazia para simular ausência
        $this->makeInvitation($event, [
            'email' => '',
            'whatsapp' => '559999999999',
        ]);

        $resp = $this->get(route('events.guests.index', $event->id));
        $resp->assertOk();
        $resp->assertDontSee('Enviar Convite (E-mail)', false);
        $resp->assertSee('Enviar Convite (WhatsApp)', false);
    }

    public function test_sem_canais_esconde_botao_enviar_e_exibe_aviso(): void
    {
        [$user, $event] = $this->createEventAndLogin();
        $this->makeInvitation($event, [
            'email' => '',
            'whatsapp' => null,
        ]);

        $resp = $this->get(route('events.guests.index', $event->id));
        $resp->assertOk();
        $resp->assertDontSee('Enviar Convite (E-mail)', false);
        $resp->assertDontSee('Enviar Convite (WhatsApp)', false);
        $resp->assertSee('Sem canais de envio', false);
    }
}

