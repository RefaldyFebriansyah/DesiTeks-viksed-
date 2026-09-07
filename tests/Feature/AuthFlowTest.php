<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthFlowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed initial branches
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

    public function test_login_with_email_and_role_redirect_for_admin(): void
    {
        $admin = User::create([
            'name'      => 'Admin User',
            'username'  => 'admin',
            'email'     => 'admin@desiteks.com',
            'password'  => bcrypt('password123'),
            'role'      => 'admin',
            'status'    => 'aktif',
            'branch_id' => 1,
        ]);

        $response = $this->post('/login', [
            'login'    => 'admin@desiteks.com',
            'password' => 'password123',
            'role'     => 'admin',
        ]);

        $response->assertRedirect('/login/verify-2fa');

        $vResponse = $this->post('/login/verify-2fa', [
            'one_time_password' => '123456',
        ]);

        $vResponse->assertRedirect('/admin/dashboard');
        $this->assertAuthenticatedAs($admin);
        $this->assertEquals(1, session('active_branch_id'));
    }

    public function test_login_with_username_and_role_redirect_for_kasir(): void
    {
        $kasir = User::create([
            'name'      => 'Kasir User',
            'username'  => 'kasir',
            'email'     => 'kasir@desiteks.com',
            'password'  => bcrypt('password123'),
            'role'      => 'kasir',
            'status'    => 'aktif',
            'branch_id' => 1,
        ]);

        $response = $this->post('/login', [
            'login'    => 'kasir',
            'password' => 'password123',
        ]);

        $response->assertRedirect('/login/verify-2fa');

        $vResponse = $this->post('/login/verify-2fa', [
            'one_time_password' => '123456',
        ]);

        $vResponse->assertRedirect('/kasir/sales/pos');
        $this->assertAuthenticatedAs($kasir);
        $this->assertEquals(1, session('active_branch_id'));
    }

    public function test_login_with_email_and_role_redirect_for_gudang(): void
    {
        $gudang = User::create([
            'name'      => 'Gudang User',
            'username'  => 'gudang',
            'email'     => 'gudang@desiteks.com',
            'password'  => bcrypt('password123'),
            'role'      => 'gudang',
            'status'    => 'aktif',
            'branch_id' => 2,
        ]);

        $response = $this->post('/login', [
            'login'    => 'gudang@desiteks.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect('/login/verify-2fa');

        $vResponse = $this->post('/login/verify-2fa', [
            'one_time_password' => '123456',
        ]);

        $vResponse->assertRedirect('/gudang/stocks');
        $this->assertAuthenticatedAs($gudang);
        $this->assertEquals(2, session('active_branch_id'));
    }

    public function test_login_fails_if_role_selection_mismatches(): void
    {
        User::create([
            'name'      => 'Kasir User',
            'username'  => 'kasir',
            'email'     => 'kasir@desiteks.com',
            'password'  => bcrypt('password123'),
            'role'      => 'kasir',
            'status'    => 'aktif',
            'branch_id' => 1,
        ]);

        $response = $this->post('/login', [
            'login'    => 'kasir@desiteks.com',
            'password' => 'password123',
            'role'     => 'admin', // Mismatch!
        ]);

        $response->assertSessionHasErrors('role');
        $this->assertGuest();
    }
}
