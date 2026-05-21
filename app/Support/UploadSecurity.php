<?php

namespace App\Support;

use Closure;
use Illuminate\Http\UploadedFile;

final class UploadSecurity
{
    public const ALLOWED_EXTENSIONS = ['jpg', 'png', 'mp4'];
    public const IMAGE_EXTENSIONS = ['jpg', 'png'];
    public const MP4_EXTENSIONS = ['mp4'];

    public const IMAGE_MIME_TYPES = [
        'image/jpeg',
        'image/png',
    ];

    public const MP4_MIME_TYPES = [
        'video/mp4',
        'audio/mp4',
    ];

    public static function imageRules(int $maxKilobytes): array
    {
        return [
            'nullable',
            'file',
            'mimes:' . implode(',', self::IMAGE_EXTENSIONS),
            'mimetypes:' . implode(',', self::IMAGE_MIME_TYPES),
            self::allowedExtensionRule(self::IMAGE_EXTENSIONS),
            'max:' . $maxKilobytes,
        ];
    }

    public static function mp4Rules(int $maxKilobytes): array
    {
        return [
            'nullable',
            'file',
            'mimes:' . implode(',', self::MP4_EXTENSIONS),
            'mimetypes:' . implode(',', self::MP4_MIME_TYPES),
            self::allowedExtensionRule(self::MP4_EXTENSIONS),
            'max:' . $maxKilobytes,
        ];
    }

    public static function allowedExtensionRule(array $allowedExtensions): Closure
    {
        $normalized = array_map('strtolower', $allowedExtensions);

        return static function (string $attribute, $value, Closure $fail) use ($normalized): void {
            if (!$value instanceof UploadedFile) {
                return;
            }

            $extension = strtolower((string) $value->getClientOriginalExtension());

            if (!in_array($extension, $normalized, true)) {
                $fail('Le fichier doit avoir une extension autorisée : ' . implode(', ', $normalized) . '.');
            }
        };
    }

    public static function hasAllowedExtension(?string $path): bool
    {
        if (!$path) {
            return false;
        }

        $extension = strtolower((string) pathinfo($path, PATHINFO_EXTENSION));

        return in_array($extension, self::ALLOWED_EXTENSIONS, true);
    }
}
