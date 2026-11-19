<?php

namespace Tests\Feature;

use App\Models\Scholarship;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ScholarshipPortalTest extends TestCase
{
    use RefreshDatabase;

    private function scholarship(array $overrides = []): Scholarship
    {
        return Scholarship::create(array_merge([
            'name' => 'Bakti BCA', 'slug' => 'bakti-bca', 'provider' => 'BCA', 'level' => 'S1',
            'deadline' => now()->addDays(20), 'summary' => 'Dukungan pendidikan untuk mahasiswa.',
            'description' => 'Program pendidikan.', 'requirements' => ['Mahasiswa aktif.'],
            'benefits' => ['Bantuan studi.'], 'application_url' => 'https://example.com', 'is_active' => true,
        ], $overrides));
    }

    public function test_home_shows_active_scholarships_without_admin_entrypoint(): void
    {
        $this->scholarship();
        $this->get('/')->assertOk()->assertSee('Ayo Daftar Beasiswa Sekarang!')->assertSee('Bakti BCA')->assertDontSee('/pengelola/masuk');
    }

    public function test_search_can_filter_scholarships_by_level(): void
    {
        $this->scholarship();
        $this->scholarship(['name' => 'Beasiswa Pascasarjana', 'slug' => 'pascasarjana', 'level' => 'S2']);
        $this->get('/pencarian?level%5B%5D=S1')->assertOk()->assertSee('Bakti BCA')->assertDontSee('Beasiswa Pascasarjana');
    }

    public function test_inactive_scholarship_is_not_publicly_visible(): void
    {
        $scholarship = $this->scholarship(['is_active' => false]);
        $this->get('/beasiswa/'.$scholarship->slug)->assertNotFound();
    }

    public function test_admin_panel_rejects_guests_and_non_admin_users(): void
    {
        $this->get('/pengelola')->assertRedirect('/pengelola/masuk');
        $this->actingAs(User::factory()->create())->get('/pengelola')->assertForbidden();
    }

    public function test_admin_can_login_and_create_scholarship(): void
    {
        $admin = User::factory()->create(['email' => 'admin@example.test', 'password' => bcrypt('password1234'), 'is_admin' => true]);
        $this->post('/pengelola/masuk', ['email' => $admin->email, 'password' => 'password1234'])->assertRedirect('/pengelola');
        $this->post('/pengelola/beasiswa', [
            'name' => 'Beasiswa Uji', 'provider' => 'Universitas Uji', 'level' => 'S1', 'deadline' => now()->addDays(30)->format('Y-m-d'),
            'summary' => 'Ringkasan tes.', 'description' => 'Deskripsi tes.', 'requirements_text' => "Mahasiswa aktif\nIPK 3.0",
            'benefits_text' => 'Biaya pendidikan', 'application_url' => 'https://example.test/apply', 'is_active' => '1',
        ])->assertRedirect('/pengelola');
        $this->assertDatabaseHas('scholarships', ['name' => 'Beasiswa Uji', 'provider' => 'Universitas Uji']);
    }

    public function test_admin_can_update_and_delete_a_scholarship(): void
    {
        $scholarship = $this->scholarship();
        $admin = User::factory()->create(['is_admin' => true]);
        $this->actingAs($admin)->put('/pengelola/beasiswa/'.$scholarship->id, [
            'name' => 'Bakti BCA Terbaru', 'provider' => 'BCA', 'level' => 'S1',
            'deadline' => now()->addDays(40)->format('Y-m-d'), 'summary' => 'Ringkasan terbaru.',
            'description' => 'Deskripsi terbaru.', 'requirements_text' => 'Mahasiswa aktif.',
            'benefits_text' => 'Bantuan studi.', 'application_url' => 'https://example.com/apply', 'is_active' => '1',
        ])->assertRedirect('/pengelola');
        $this->assertDatabaseHas('scholarships', ['id' => $scholarship->id, 'name' => 'Bakti BCA Terbaru']);
        $this->delete('/pengelola/beasiswa/'.$scholarship->id)->assertRedirect('/pengelola');
        $this->assertDatabaseMissing('scholarships', ['id' => $scholarship->id]);
    }
}
