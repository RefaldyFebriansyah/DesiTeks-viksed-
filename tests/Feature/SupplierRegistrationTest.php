<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SupplierRegistrationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Branch::create([
            'id'          => 1,
            'nama_cabang' => 'Cabang Utama (Pusat)',
            'kode_cabang' => 'CBG-001',
            'alamat'      => 'Jl. Kebon Jati No. 45, Bandung',
            'telepon'     => '0812-3456-7890',
            'is_main'     => true,
            'status'      => 'aktif',
        ]);
    }

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
        $response->assertSee('Daftar Akun Mitra Supplier');
    }

    public function test_new_supplier_can_register_and_creates_supplier_in_admin_database(): void
    {
        $postData = [
            'nama_supplier'         => 'PT. Sinar Tekstil Jaya',
            'asal_kota'             => 'Bandung',
            'no_telepon'            => '0812-9988-7766',
            'alamat'                => 'Kawasan Industri Cimahi No. 18',
            'name'                  => 'Hendro Wijaya',
            'username'              => 'sinartekstil',
            'email'                 => 'admin@sinartekstil.co.id',
            'password'              => 'Rahasia123',
            'password_confirmation' => 'Rahasia123',
        ];

        $response = $this->post('/register', $postData);

        $response->assertRedirect(route('supplier.dashboard'));

        // 1. Verifikasi Supplier berhasil dibuat di database (masuk ke data supplier admin)
        $this->assertDatabaseHas('suppliers', [
            'nama_supplier' => 'PT. Sinar Tekstil Jaya',
            'email'         => 'admin@sinartekstil.co.id',
            'asal_kota'     => 'Bandung',
            'no_telepon'    => '0812-9988-7766',
        ]);

        $supplier = Supplier::where('email', 'admin@sinartekstil.co.id')->first();
        $this->assertNotNull($supplier);
        $this->assertStringStartsWith('SUP', $supplier->kode_supplier);

        // 2. Verifikasi User supplier berhasil dibuat dan terhubung ke supplier_id
        $this->assertDatabaseHas('users', [
            'username'    => 'sinartekstil',
            'email'       => 'admin@sinartekstil.co.id',
            'role'        => 'supplier',
            'supplier_id' => $supplier->id,
        ]);

        // 3. Verifikasi User sudah otomatis login
        $this->assertAuthenticated();
    }

    public function test_registered_supplier_appears_in_admin_suppliers_list(): void
    {
        $admin = User::create([
            'name'      => 'Admin DesiTeks',
            'username'  => 'admin',
            'email'     => 'admin@desiteks.com',
            'password'  => bcrypt('password'),
            'role'      => 'admin',
            'status'    => 'aktif',
            'branch_id' => 1,
        ]);

        // Daftarkan supplier baru via form registrasi
        $this->post('/register', [
            'nama_supplier'         => 'CV. Tenun Makmur Bersama',
            'asal_kota'             => 'Solo',
            'no_telepon'            => '0813-1122-3344',
            'alamat'                => 'Jl. Slamet Riyadi No. 45',
            'name'                  => 'Agus Prasetyo',
            'username'              => 'tenunmakmur',
            'email'                 => 'kontak@tenunmakmur.com',
            'password'              => 'Password123',
            'password_confirmation' => 'Password123',
        ]);

        // Login sebagai admin dan cek halaman daftar supplier admin
        $response = $this->actingAs($admin)->get('/admin/suppliers');

        $response->assertStatus(200);
        $response->assertSee('CV. Tenun Makmur Bersama');
        $response->assertSee('Solo');
        $response->assertSee('0813-1122-3344');
    }
}
