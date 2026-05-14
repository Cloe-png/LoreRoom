<?php

namespace App\Support;

class PasswordRules
{
    public static function defaults(): array
    {
        return [
            'required',
            'string',
            'min:12',
            'regex:/[a-z]/',
            'regex:/[A-Z]/',
            'regex:/[0-9]/',
            'regex:/[^A-Za-z0-9]/',
        ];
    }

    public static function defaultsWithConfirmation(): array
    {
        return array_merge(self::defaults(), ['confirmed']);
    }

    public static function helpText(): string
    {
        return '12 caracteres minimum, avec au moins 1 majuscule, 1 minuscule, 1 chiffre et 1 caractere special.';
    }
}
