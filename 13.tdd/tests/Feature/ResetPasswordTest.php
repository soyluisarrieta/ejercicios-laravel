<?php

namespace Tests\Feature;

use App\Models\User;
use App\Notifications\ResetPasswordNotification;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class ResetPasswordTest extends TestCase
{
    use RefreshDatabase;

    protected $token = '';
    protected $email = '';

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(UserSeeder::class);
    }

    /**
     * Un usuario existente puede reestablecer su contraseña.
     */
    public function test_an_existing_user_can_reset_their_password(): void
    {
        # Haciendo
        $response = $this->sendPasswordResetWithToken();

        # Esperando
        $response->assertStatus(200);
        $response->assertJsonStructure(['message', 'data', 'errors', 'status']);
        $user = User::find(1);
        $this->assertTrue(Hash::check('newPassword', $user->password));
    }

    /**
     * Se envía un enlace de reestablecimiento de contraseña.
     */
    public function sendPasswordResetLink()
    {
        Notification::fake();

        # Teniendo
        $data = ['email' => 'luis@arrieta.com'];

        # Haciendo
        $response = $this->postJson("{$this->apiBase}/reset-password", $data);

        # Esperando
        $response->assertStatus(200);
        $response->assertJsonFragment(['message' => 'OK']);
    }

    /**
     * Se envía un enlace de reestablecimiento de contraseña.
     */
    public function sendPasswordResetWithToken($email = null, $password = 'newPassword', $passwordConfirmation = 'newPassword', $token = null)
    {
        # Teniendo
        $this->sendPasswordResetLink();
        $user = User::find(1);

        # Haciendo
        Notification::assertSentTo([$user], function (ResetPasswordNotification $notification) use ($token) {
            $url = $notification->url;
            $parts = parse_url($url);
            parse_str($parts['query'], $query);
            $this->token = $token ?? $query['token'];
            $this->email = $query['email'];
            return str_contains($url, 'http://examplefront.app/reset-password?token=');
        });

        $response = $this->putJson("{$this->apiBase}/reset-password?token={$this->token}", [
            'email' => $email === null ? $this->email : $email,
            'password' => $password,
            'password_confirmation' => $passwordConfirmation
        ]);

        return $response;
    }

    /**
     * El correo electrónico es requerido
     */
    public function test_email_must_be_required(): void
    {
        # Teniendo
        $data = ['email' => ''];

        # Haciendo
        $response = $this->postJson("{$this->apiBase}/reset-password", $data);

        # Esperando
        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'data', 'status', 'errors' => ['email']]);
    }

    /**
     * El correo electrónico es inválido
     */
    public function test_email_must_be_valid_email(): void
    {
        # Teniendo
        $data = ['email' => 'notemail.com'];

        # Haciendo
        $response = $this->postJson("{$this->apiBase}/reset-password", $data);

        # Esperando
        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'data', 'status', 'errors' => ['email']]);
    }

    /**
     * El correo electrónico debe existir
     */
    public function test_email_must_be_an_existing_email(): void
    {
        # Teniendo
        $data = ['email' => 'notexisting@example.com'];

        # Haciendo
        $response = $this->postJson("{$this->apiBase}/reset-password", $data);

        # Esperando
        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'data', 'status', 'errors' => ['email']]);
    }

    /**
     * La contraseña es requerida
     */
    public function test_password_must_be_required(): void
    {
        # Haciendo
        $response = $this->sendPasswordResetWithToken(password: '', passwordConfirmation: 'newPassword');

        # Esperando
        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'data', 'status', 'errors' => ['password']]);
    }

    /**
     * La contraseña debe contener almenos 8 caracteres
     */
    public function test_password_must_have_at_lease_8_characters(): void
    {
        # Haciendo
        $response = $this->sendPasswordResetWithToken(password: 'pass', passwordConfirmation: 'pass');

        # Esperando
        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'data', 'status', 'errors' => ['password']]);
    }

    /**
     * El token debe ser válido
     */
    public function test_token_must_be_valid(): void
    {
        # Haciendo
        $response = $this->sendPasswordResetWithToken(token: 'notvalidtoken');

        # Esperando
        $response->assertStatus(500);
        $response->assertJsonStructure(['message', 'data', 'status', 'errors' => ['token']]);
    }

    /**
     * El correo electrónico debe estar asociado con el token
     */
    public function test_email_must_be_associated_with_the_token(): void
    {
        # Haciendo
        $response = $this->sendPasswordResetWithToken(email: 'other@email.com');

        # Esperando
        $response->assertStatus(500);
        $response->assertJsonStructure(['message', 'data', 'status', 'errors' => ['email']]);
    }
}
