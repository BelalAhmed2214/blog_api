<?php

namespace Tests\Traits;

use Illuminate\Support\Facades\Http;

trait AuthTrait
{
    /**
     * Authenticate a user and return a token.
     *
     * @param string $email
     * @param string $password
     * @return string|null
     */
    public function loginAndSetToken(string $email, string $password): void
    {
        $response = $this->postJson('/api/auth/login', [
            'email' => $email,
            'password' => $password,
        ]);
        $token = $response->json('data.token');
        $this->assertNotNull($token, 'Failed to authenticate user and retrieve token.');
        $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ]);
    }
}
