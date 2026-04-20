<?php

namespace Tests\Feature\Auth;

use App\Models\AuthProvider;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\RedirectResponse;
use Laravel\Socialite\Contracts\Provider;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User as SocialiteUser;
use Mockery;
use Tests\TestCase;

class GoogleAuthenticationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config()->set('services.google.client_id', 'google-client-id');
        config()->set('services.google.client_secret', 'google-client-secret');
        config()->set('services.google.redirect', 'http://localhost/auth/google/callback');
    }

    public function test_guest_can_be_redirected_to_google_provider(): void
    {
        $provider = Mockery::mock(Provider::class);
        $provider->shouldReceive('redirect')
            ->once()
            ->andReturn(new RedirectResponse('https://accounts.google.com/o/oauth2/auth'));

        Socialite::shouldReceive('driver')
            ->once()
            ->with(AuthProvider::PROVIDER_GOOGLE)
            ->andReturn($provider);

        $response = $this->get(route('auth.google.redirect'));

        $response->assertRedirect('https://accounts.google.com/o/oauth2/auth');
    }

    public function test_existing_google_link_can_log_in(): void
    {
        $user = User::factory()->unverified()->create([
            'email' => 'existing.user@example.com',
        ]);

        AuthProvider::query()->create([
            'user_id' => $user->id,
            'provider' => AuthProvider::PROVIDER_GOOGLE,
            'provider_user_id' => 'google-existing-id',
            'provider_email' => 'old.email@example.com',
            'provider_email_verified_at' => null,
        ]);

        $this->mockGoogleUser($this->makeGoogleUser([
            'id' => 'google-existing-id',
            'email' => 'EXISTING.USER@example.com',
            'name' => 'Existing User',
        ]));

        $response = $this->get(route('auth.google.callback'));

        $this->assertAuthenticatedAs($user->fresh());
        $response->assertRedirect(route('dashboard', absolute: false));

        $this->assertDatabaseHas('auth_providers', [
            'user_id' => $user->id,
            'provider' => AuthProvider::PROVIDER_GOOGLE,
            'provider_user_id' => 'google-existing-id',
            'provider_email' => 'existing.user@example.com',
        ]);

        $this->assertNotNull($user->fresh()->email_verified_at);
    }

    public function test_existing_local_user_is_auto_linked_by_email(): void
    {
        $user = User::factory()->unverified()->create([
            'email' => 'local.user@example.com',
        ]);

        $this->mockGoogleUser($this->makeGoogleUser([
            'id' => 'google-new-id',
            'email' => 'local.user@example.com',
            'name' => 'Local User',
        ]));

        $response = $this->get(route('auth.google.callback'));

        $this->assertAuthenticatedAs($user->fresh());
        $response->assertRedirect(route('dashboard', absolute: false));

        $this->assertDatabaseHas('auth_providers', [
            'user_id' => $user->id,
            'provider' => AuthProvider::PROVIDER_GOOGLE,
            'provider_user_id' => 'google-new-id',
            'provider_email' => 'local.user@example.com',
        ]);

        $this->assertNotNull($user->fresh()->email_verified_at);
    }

    public function test_new_user_is_created_from_google_sign_in(): void
    {
        $this->mockGoogleUser($this->makeGoogleUser([
            'id' => 'google-fresh-id',
            'email' => 'Fresh.User@example.com',
            'name' => 'Fresh User',
        ]));

        $response = $this->get(route('auth.google.callback'));

        $response->assertRedirect(route('dashboard', absolute: false));
        $this->assertAuthenticated();

        $user = User::query()->where('email', 'fresh.user@example.com')->firstOrFail();

        $this->assertDatabaseHas('auth_providers', [
            'user_id' => $user->id,
            'provider' => AuthProvider::PROVIDER_GOOGLE,
            'provider_user_id' => 'google-fresh-id',
            'provider_email' => 'fresh.user@example.com',
        ]);
        $this->assertNotNull($user->email_verified_at);
    }

    public function test_google_callback_fails_when_email_is_missing(): void
    {
        $this->mockGoogleUser($this->makeGoogleUser([
            'id' => 'google-no-email',
            'email' => null,
            'name' => 'No Email',
        ]));

        $response = $this->get(route('auth.google.callback'));

        $response->assertRedirect(route('login'));
        $response->assertSessionHasErrors(['oauth']);
        $this->assertGuest();

        $this->assertDatabaseMissing('auth_providers', [
            'provider_user_id' => 'google-no-email',
        ]);
    }

    public function test_google_callback_fails_when_google_email_is_not_verified(): void
    {
        $this->mockGoogleUser($this->makeGoogleUser([
            'id' => 'google-unverified-email',
            'email' => 'unverified@example.com',
            'name' => 'Unverified Email',
        ], [
            'email_verified' => false,
        ]));

        $response = $this->get(route('auth.google.callback'));

        $response->assertRedirect(route('login'));
        $response->assertSessionHasErrors(['oauth']);
        $this->assertGuest();

        $this->assertDatabaseMissing('users', [
            'email' => 'unverified@example.com',
        ]);
    }

    private function mockGoogleUser(SocialiteUser $socialiteUser): void
    {
        $provider = Mockery::mock(Provider::class);
        $provider->shouldReceive('user')
            ->once()
            ->andReturn($socialiteUser);

        Socialite::shouldReceive('driver')
            ->once()
            ->with(AuthProvider::PROVIDER_GOOGLE)
            ->andReturn($provider);
    }

    private function makeGoogleUser(array $attributes = [], array $raw = []): SocialiteUser
    {
        $mapped = array_merge([
            'id' => 'google-default-id',
            'name' => 'Google User',
            'email' => 'google.user@example.com',
        ], $attributes);

        $rawPayload = array_merge([
            'email_verified' => true,
            'email' => $mapped['email'],
            'name' => $mapped['name'],
            'sub' => $mapped['id'],
        ], $raw);

        return (new SocialiteUser())
            ->setRaw($rawPayload)
            ->map($mapped);
    }
}
