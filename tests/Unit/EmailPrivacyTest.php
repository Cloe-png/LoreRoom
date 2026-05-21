<?php

namespace Tests\Unit;

use App\Models\User;
use App\Support\EmailPrivacy;
use Tests\TestCase;

class EmailPrivacyTest extends TestCase
{
    public function test_normalize_and_hash_are_stable(): void
    {
        $this->assertSame('test@example.com', EmailPrivacy::normalize(' Test@Example.com '));
        $this->assertSame(
            EmailPrivacy::hash('test@example.com'),
            EmailPrivacy::hash(' Test@Example.com ')
        );
    }

    public function test_user_email_accessor_encrypts_and_decrypts(): void
    {
        $user = new User();
        $user->email = 'Test@Example.com';

        $this->assertNotSame('test@example.com', $user->getRawOriginal('email'));
        $this->assertSame('test@example.com', $user->email);
        $this->assertSame(User::emailHash('test@example.com'), $user->email_hash);
    }
}
