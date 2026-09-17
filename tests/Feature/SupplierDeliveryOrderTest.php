<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\Category;
use App\Models\DeliveryOrder;
use App\Models\Fabric;
use App\Models\Stock;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class SupplierDeliveryOrderTest extends TestCase
{
    use RefreshDatabase;

    private User $supplierUser;
    private Supplier $supplier;
    private User $gudangUser;
    private Branch $branch;

    protected function setUp(): void
    {
        parent::setUp();

        $this->branch = Branch::firstOrCreate(
            ['id' => 1],
            ['nama_cabang' => 'DesiTeks Pusat', 'kode_cabang' => 'CBG-001', 'is_main' => true]
        );

        $this->supplier = Supplier::firstOrCreate(
            ['kode_supplier' => 'SUPTEST'],
            [
                'nama_supplier' => 'PT Test Kain Mitra',
                'email'         => 'supplier.test@example.com',
                'asal_kota'     => 'Bandung',
            ]
        );

        $this->supplierUser = User::firstOrCreate(
            ['username' => 'suppliertest'],
            [
                'name'        => 'Budi Supplier Test',
                'email'       => 'suppliertest@example.com',
                'password'    => Hash::make('password123'),
                'role'        => 'supplier',
                'status'      => 'aktif',
                'branch_id'   => $this->branch->id,
                'supplier_id' => $this->supplier->id,
            ]
        );

        $this->gudangUser = User::firstOrCreate(
            ['username' => 'gudangtest'],
            [
                'name'      => 'Staff Gudang Test',
                'email'     => 'gudangtest@example.com',
                'password'  => Hash::make('password123'),
                'role'      => 'gudang',
                'status'    => 'aktif',
                'branch_id' => $this->branch->id,
            ]
        );
    }

    public function test_login_routes_accessible(): void
    {
        $responseSupplierLogin = $this->get('/login');
        $responseSupplierLogin->assertStatus(200);
        $responseSupplierLogin->assertSee('Portal Rekanan Supplier');

        $responseStaffLogin = $this->get('/login/staff');
        $responseStaffLogin->assertStatus(200);
        $responseStaffLogin->assertSee('Portal Staf Internal');
    }

    public function test_guest_can_access_supplier_landing_page_and_protected_when_creating(): void
    {
        // Akses root / menampilkan Landing Page Modern Publik
        $rootResponse = $this->get('/');
        $rootResponse->assertStatus(200);
        $rootResponse->assertSee('Surat Jalan Online');

        // Akses /supplier juga menampilkan Landing Page
        $guestLanding = $this->get('/supplier');
        $guestLanding->assertStatus(200);
        $guestLanding->assertSee('DesiTeks');

        // Saat guest mencoba mau isi form pengiriman, harus login dulu
        $guestCreate = $this->get('/supplier/delivery-orders/create');
        $guestCreate->assertRedirect('/login');
        $guestCreate->assertSessionHas('info');

        // Staf lain (gudang/kasir) dilarang mengakses form pengisian supplier
        $forbiddenResponse = $this->actingAs($this->gudangUser)->get('/supplier/delivery-orders/create');
        $forbiddenResponse->assertStatus(403);

        // Supplier yang sudah login dapat mengakses dashboard workspace
        $supplierDashboard = $this->actingAs($this->supplierUser)->get('/supplier/dashboard');
        $supplierDashboard->assertStatus(200);
    }

    public function test_supplier_can_create_delivery_order_online(): void
    {
        $category = Category::firstOrCreate(['nama_kategori' => 'Katun Premium']);
        $fabric = Fabric::firstOrCreate(
            ['kode_kain' => 'KTN-TEST-01'],
            [
                'nama_kain'       => 'Katun Rayon Super',
                'category_id'     => $category->id,
                'harga_per_meter' => 40000,
                'meter_per_rol'   => 50,
                'status'          => 'aktif',
            ]
        );

        $postData = [
            'nomor_surat_jalan' => 'SJ-TEST-ONLINE-001',
            'tanggal_kirim'     => now()->toDateString(),
            'branch_id'         => $this->branch->id,
            'nama_supir'        => 'Pak Joko',
            'plat_nomor'        => 'D 1234 XYZ',
            'ekspedisi'         => 'Armada Sendiri',
            'catatan'           => 'Barang dalam kondisi roll plastik kedap air.',
            'items' => [
                [
                    'fabric_id'    => $fabric->id,
                    'nama_kain'    => $fabric->nama_kain,
                    'jenis_kain'   => 'Katun',
                    'warna'        => 'Navy Blue',
                    'jumlah_rol'   => 5,
                    'jumlah_meter' => 250,
                    'harga_satuan' => 30000,
                ],
            ],
        ];

        $response = $this->actingAs($this->supplierUser)
            ->post('/supplier/delivery-orders', $postData);

        $this->assertDatabaseHas('delivery_orders', [
            'nomor_surat_jalan' => 'SJ-TEST-ONLINE-001',
            'supplier_id'       => $this->supplier->id,
            'status'            => 'menunggu_approval',
            'total_rol'         => 5,
        ]);

        $deliveryOrder = DeliveryOrder::where('nomor_surat_jalan', 'SJ-TEST-ONLINE-001')->first();
        $this->assertNotNull($deliveryOrder);

        $response->assertRedirect(route('supplier.delivery-orders.show', $deliveryOrder->id));
    }

    public function test_gudang_can_inspect_and_accept_delivery_order_which_updates_stock(): void
    {
        $category = Category::firstOrCreate(['nama_kategori' => 'Sutra Halus']);
        $fabric = Fabric::firstOrCreate(
            ['kode_kain' => 'STR-TEST-01'],
            [
                'nama_kain'       => 'Sutra Organza',
                'category_id'     => $category->id,
                'harga_per_meter' => 60000,
                'meter_per_rol'   => 50,
                'status'          => 'aktif',
            ]
        );

        // Ensure baseline stock
        $initialStock = Stock::firstOrCreate(
            ['fabric_id' => $fabric->id, 'branch_id' => $this->branch->id],
            ['stok_rol' => 2, 'stok_meter' => 0]
        );
        $initialRol = $initialStock->stok_rol;

        // Create a delivery order in 'menunggu_approval' state
        $order = DeliveryOrder::create([
            'nomor_surat_jalan' => 'SJ-TEST-ACCEPT-001',
            'supplier_id'       => $this->supplier->id,
            'user_id'           => $this->supplierUser->id,
            'branch_id'         => $this->branch->id,
            'tanggal_kirim'     => now()->toDateString(),
            'nama_supir'        => 'Bambang',
            'plat_nomor'        => 'B 9999 KAI',
            'status'            => 'menunggu_approval',
            'total_rol'         => 4,
            'total_meter'       => 200,
            'total_nominal'     => 8000000,
        ]);

        $order->items()->create([
            'fabric_id'    => $fabric->id,
            'nama_kain'    => $fabric->nama_kain,
            'jumlah_rol'   => 4,
            'jumlah_meter' => 200,
            'harga_satuan' => 40000,
            'subtotal'     => 8000000,
        ]);

        // Gudang/Admin views list
        $viewList = $this->actingAs($this->gudangUser)->get('/gudang/delivery-orders');
        $viewList->assertStatus(200);
        $viewList->assertSee('SJ-TEST-ACCEPT-001');

        // Step 1: Admin ACCs the delivery order
        $approveResponse = $this->actingAs($this->gudangUser)
            ->post("/gudang/delivery-orders/{$order->id}/approve-admin");
        
        $order->refresh();
        $this->assertEquals('disetujui_admin', $order->status);

        // Transition order to dalam_perjalanan (shipped by supplier)
        $order->update(['status' => 'dalam_perjalanan']);

        // Step 2: Gudang accepts physical goods
        $acceptResponse = $this->actingAs($this->gudangUser)
            ->post("/gudang/delivery-orders/{$order->id}/accept", [
                'catatan_gudang' => 'Semua rol diperiksa lengkap dan kondisi prima.',
            ]);

        $acceptResponse->assertRedirect(route('gudang.delivery-orders.show', $order->id));

        // Refresh and check assertions
        $order->refresh();
        $this->assertEquals('diterima', $order->status);
        $this->assertEquals($this->gudangUser->id, $order->received_by_user_id);
        $this->assertNotNull($order->received_at);
        $this->assertNotNull($order->incoming_good_id);

        // Check stock updated
        $updatedStock = Stock::where('fabric_id', $fabric->id)
            ->where('branch_id', $this->branch->id)
            ->first();

        $this->assertEquals($initialRol + 4, $updatedStock->stok_rol);

        // Check incoming goods record created
        $this->assertDatabaseHas('incoming_goods', [
            'id'           => $order->incoming_good_id,
            'nomor_faktur' => 'SJ-TEST-ACCEPT-001',
            'supplier_id'  => $this->supplier->id,
            'total_rol'    => 4,
        ]);
    }
}
