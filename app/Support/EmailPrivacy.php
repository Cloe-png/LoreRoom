<?php

namespace App\Support;

use Illuminate\Support\Facades\Crypt;

final class EmailPrivacy
{
    public static function normalize(?string $email): string
    {
        return mb_strtolower(trim((string) $email));
    }

    public static function hash(?string $email): string
    {
        return hash('sha256', self::normalize($email));
    }

    public static function encrypt(?string $email): ?string
    {
        $normalized = self::normalize($email);

        if ($normalized === '') {
            return null;
        }

        return Crypt::encryptString($normalized);
    }

    public static function decrypt(?string $payload): ?string
    {
        if ($payload === null || $payload === '') {
            return null;
        }

        try {
            return Crypt::decryptString($payload);
        } catch (\Throwable $e) {
            return $payload;
        }
    }
}
