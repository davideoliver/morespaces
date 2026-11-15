<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\ContactMessage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContactMessagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_valid_contact_submission_persists_and_flashes_success(): void
    {
        $response = $this->post(route('contact'), [
            'nome' => 'Visitante',
            'contatoEmail' => 'visitante@example.com',
            'mensagem' => 'Gostaria de saber mais sobre os espaços.',
        ]);

        $response->assertRedirect(route('home').'#contact')
            ->assertSessionHas('success', 'Mensagem enviada com sucesso!');
        $this->assertDatabaseHas('contact_messages', [
            'user_id' => null,
            'name' => 'Visitante',
            'email' => 'visitante@example.com',
        ]);
    }

    public function test_authenticated_contact_submission_stores_user_id(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post(route('contact'), [
            'nome' => 'Usuário autenticado',
            'contatoEmail' => 'usuario@example.com',
            'mensagem' => 'Mensagem enviada com uma conta ativa.',
        ])->assertSessionHasNoErrors();

        $this->assertDatabaseHas('contact_messages', [
            'user_id' => $user->id,
            'email' => 'usuario@example.com',
        ]);
    }

    public function test_contact_rejects_invalid_email_and_short_message(): void
    {
        $this->from(route('home').'#contact')
            ->post(route('contact'), [
                'nome' => 'Visitante',
                'contatoEmail' => 'invalido',
                'mensagem' => 'curta',
            ])
            ->assertRedirect(route('home').'#contact')
            ->assertSessionHasErrors(['contatoEmail', 'mensagem']);

        $this->assertDatabaseCount('contact_messages', 0);
    }

    public function test_contact_rejects_missing_name_and_overlong_message(): void
    {
        $this->from(route('home').'#contact')
            ->post(route('contact'), [
                'nome' => '',
                'contatoEmail' => 'visitante@example.com',
                'mensagem' => str_repeat('M', 5001),
            ])
            ->assertRedirect(route('home').'#contact')
            ->assertSessionHasErrors(['nome', 'mensagem']);

        $this->assertDatabaseCount('contact_messages', 0);
    }

    public function test_only_admins_can_read_contact_messages(): void
    {
        $this->actingAs(User::factory()->member()->create())
            ->get(route('admin.messages'))
            ->assertForbidden();

        $this->actingAs(User::factory()->admin()->create())
            ->get(route('admin.messages'))
            ->assertOk()
            ->assertSee('Mensagens recebidas');
    }

    public function test_admin_messages_are_newest_first(): void
    {
        ContactMessage::query()->create([
            'name' => 'Primeira',
            'email' => 'primeira@example.com',
            'message' => 'Mensagem mais antiga recebida.',
            'created_at' => now()->subMinute(),
        ]);
        ContactMessage::query()->create([
            'name' => 'Segunda',
            'email' => 'segunda@example.com',
            'message' => 'Mensagem mais recente recebida.',
            'created_at' => now(),
        ]);

        $response = $this->actingAs(User::factory()->admin()->create())
            ->get(route('admin.messages'));

        $response->assertOk();
        $response->assertSeeInOrder(['Segunda', 'Primeira']);
    }
}
