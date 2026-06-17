<?php

namespace Tests\Feature;

use App\Models\CertificateSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CertificateSignatureSettingTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_upload_official_signature(): void
    {
        Storage::fake('public');

        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $file = UploadedFile::fake()->image('sig.png', 120, 40);

        $this->actingAs($admin)
            ->post(route('settings.signature.update'), [
                'signature' => $file,
            ])
            ->assertRedirect(route('settings.index', ['section' => 'signature']))
            ->assertSessionHas('status');

        CertificateSetting::flushSingletonCache();
        $path = CertificateSetting::singleton()->fresh()->official_signature_path;
        $this->assertNotNull($path);
        Storage::disk('public')->assertExists($path);
    }

    public function test_fleet_officer_cannot_upload_signature(): void
    {
        Storage::fake('public');

        $user = User::factory()->create(['role' => User::ROLE_FLEET_OFFICER]);
        $file = UploadedFile::fake()->image('sig.png');

        $this->actingAs($user)
            ->post(route('settings.signature.update'), ['signature' => $file])
            ->assertForbidden();
    }

    public function test_admin_can_remove_official_signature(): void
    {
        Storage::fake('public');

        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $file = UploadedFile::fake()->image('sig.png', 120, 40);

        $this->actingAs($admin)
            ->post(route('settings.signature.update'), ['signature' => $file])
            ->assertRedirect(route('settings.index', ['section' => 'signature']));

        CertificateSetting::flushSingletonCache();
        $path = CertificateSetting::singleton()->fresh()->official_signature_path;
        $this->assertNotNull($path);

        $this->actingAs($admin)
            ->delete(route('settings.signature.destroy'))
            ->assertRedirect(route('settings.index', ['section' => 'signature']))
            ->assertSessionHas('status');

        CertificateSetting::flushSingletonCache();
        $this->assertNull(CertificateSetting::singleton()->fresh()->official_signature_path);
        Storage::disk('public')->assertMissing($path);
    }

    public function test_fleet_officer_cannot_remove_signature(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_FLEET_OFFICER]);

        $this->actingAs($user)
            ->delete(route('settings.signature.destroy'))
            ->assertForbidden();
    }
}
