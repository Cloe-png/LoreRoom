<?php

namespace Tests\Unit;

use App\Support\UploadSecurity;
use PHPUnit\Framework\TestCase;

class UploadSecurityTest extends TestCase
{
    public function test_allowed_extension_list_is_strict(): void
    {
        $this->assertTrue(UploadSecurity::hasAllowedExtension('characters/avatar.jpg'));
        $this->assertTrue(UploadSecurity::hasAllowedExtension('places/map.png'));
        $this->assertTrue(UploadSecurity::hasAllowedExtension('voices/sample.mp4'));
        $this->assertFalse(UploadSecurity::hasAllowedExtension('voices/sample.wav'));
        $this->assertFalse(UploadSecurity::hasAllowedExtension('avatars/photo.jpeg'));
        $this->assertFalse(UploadSecurity::hasAllowedExtension('archive.php'));
    }

    public function test_image_rules_only_allow_jpg_and_png(): void
    {
        $rules = UploadSecurity::imageRules(4096);

        $this->assertContains('mimes:jpg,png', $rules);
        $this->assertContains('mimetypes:image/jpeg,image/png', $rules);
        $this->assertIsCallable($rules[4]);
    }

    public function test_mp4_rules_only_allow_mp4(): void
    {
        $rules = UploadSecurity::mp4Rules(20480);

        $this->assertContains('mimes:mp4', $rules);
        $this->assertContains('mimetypes:video/mp4,audio/mp4', $rules);
        $this->assertIsCallable($rules[4]);
    }
}
