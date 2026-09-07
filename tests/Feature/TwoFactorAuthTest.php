<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\User;
use App\Services\TwoFactorService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TwoFactorAuthTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Branch::create([
            'id'          => 1,
            'nama_cabang' => 'Cabang Utama (Pusat)',
            'kode_cabang' => 'CBG-001',
            'is_main'     => true,
            'status'      => 'aktif',
        ]);
        Branch::create([
            'id'          => 2,
            'nama_cabang' => 'Cabang Gudang',
            'kode_cabang' => 'CBG-002',
            'is_main'     => false,
            'status'      => 'aktif',
        ]);
    }

    public function test_admin_role_requires_2fa_verification(): void
    {
        $user = User::create([
            'name'      => 'Refaldi Febriansyah',
            'username'  => 'admin',
            'email'     => 'refaldi.febriansyah@gmail.com',
            'password'  => bcrypt('pakade73'),
            'role'      => 'admin',
            'status'    => 'aktif',
            'branch_id' => 1,
        ]);

        $response = $this->post('/login', [
            'login'    => 'refaldi.febriansyah@gmail.com',
            'password' => 'pakade73',
        ]);

        $response->assertRedirect('/login/verify-2fa');
        $this->assertGuest();
        $this->assertEquals($user->id, session('pending_user_id'));
    }

    public function test_gudang_role_requires_2fa_verification(): void
    {
        $gudang = User::create([
            'name'      => 'Staff Gudang',
            'username'  => 'gudang',
            'email'     => 'refaldi.febriansyahh@gmail.com',
            'password'  => bcrypt('Ipang123'),
            'role'      => 'gudang',
            'status'    => 'aktif',
            'branch_id' => 2,
        ]);

        $response = $this->post('/login', [
            'login'    => 'refaldi.febriansyahh@gmail.com',
            'password' => 'Ipang123',
            'role'     => 'gudang',
        ]);

        $response->assertRedirect('/login/verify-2fa');
        $this->assertGuest();

        $vResponse = $this->post('/login/verify-2fa', [
            'one_time_password' => '123456',
        ]);

        $vResponse->assertRedirect('/gudang/stocks');
        $this->assertAuthenticatedAs($gudang);
    }

    public function test_kasir_role_requires_2fa_verification(): void
    {
        $kasir = User::create([
            'name'      => 'Staff Kasir',
            'username'  => 'kasir',
            'email'     => 'refaldi.febriansyahh@gmail.com',
            'password'  => bcrypt('Ipang123'),
            'role'      => 'kasir',
            'status'    => 'aktif',
            'branch_id' => 1,
        ]);

        $response = $this->post('/login', [
            'login'    => 'refaldi.febriansyahh@gmail.com',
            'password' => 'Ipang123',
            'role'     => 'kasir',
        ]);

        $response->assertRedirect('/login/verify-2fa');
        $this->assertGuest();

        $vResponse = $this->post('/login/verify-2fa', [
            'one_time_password' => '123456',
        ]);

        $vResponse->assertRedirect('/kasir/sales/pos');
        $this->assertAuthenticatedAs($kasir);
    }

    public function test_login_step_2_fails_with_invalid_2fa_code(): void
    {
        User::create([
            'name'      => 'Refaldi Febriansyah',
            'username'  => 'admin',
            'email'     => 'refaldi.febriansyah@gmail.com',
            'password'  => bcrypt('pakade73'),
            'role'      => 'admin',
            'status'    => 'aktif',
            'branch_id' => 1,
        ]);

        // Step 1
        $this->post('/login', [
            'login'    => 'refaldi.febriansyah@gmail.com',
            'password' => 'pakade73',
        ]);

        // Step 2: Submit invalid 6-digit code
        $response = $this->post('/login/verify-2fa', [
            'one_time_password' => '999999',
        ]);

        $response->assertSessionHasErrors('one_time_password');
        $this->assertGuest();
    }
}
