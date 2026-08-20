<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Fabric;
use App\Models\Stock;
use App\Services\StockService;
use Illuminate\Foundation\Testing\RefreshDatabase;

class StockServiceTest extends TestCase
{
    use RefreshDatabase;

    private StockService $stockService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->stockService = app(StockService::class);

        // Membuat user testing untuk memenuhi foreign key user_id di stock_movements
        $user = \App\Models\User::create([
            'name' => 'Test User',
            'username' => 'test_admin',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'status' => 'aktif',
        ]);
        $this->actingAs($user);
    }

    public function test_automatic_roll_splitting_when_buying_meters()
    {
        // 1. Create a Category
        $category = \App\Models\Category::create(['nama_kategori' => 'Cotton']);

        // 2. Create a Fabric with 50m per roll
        $fabric = Fabric::create([
            'kode_kain' => 'COT-TEST',
            'nama_kain' => 'Cotton Test',
            'category_id' => $category->id,
            'harga_per_meter' => 10000,
            'harga_per_rol' => 450000,
            'meter_per_rol' => 50.00,
            'stok_minimum' => 5,
            'status' => 'aktif',
        ]);

        // 3. Initialize stock: 2 rolls utuh, 8 meter eceran
        $stock = Stock::create([
            'fabric_id' => $fabric->id,
            'stok_rol' => 2,
            'stok_meter' => 8.00,
        ]);

        // 4. Deduct 12 meters (loose stock only has 8m, so it must split 1 roll)
        // This will reduce stok_rol to 1, and loose stock becomes 8 + 50 - 12 = 46m
        $this->stockService->kurangiStok($fabric, 'meter', 12, 1);

        $stock->refresh();
        $this->assertEquals(1, $stock->stok_rol);
        $this->assertEquals(46.00, $stock->stok_meter);

        // 5. Deduct 50 meters (loose stock has 46m, so it must split the remaining 1 roll)
        // This will reduce stok_rol to 0, and loose stock becomes 46 + 50 - 50 = 46m
        $this->stockService->kurangiStok($fabric, 'meter', 50, 1);

        $stock->refresh();
        $this->assertEquals(0, $stock->stok_rol);
        $this->assertEquals(46.00, $stock->stok_meter);
    }

    public function test_roll_splitting_throws_exception_when_out_of_stock()
    {
        $category = \App\Models\Category::create(['nama_kategori' => 'Cotton']);
        $fabric = Fabric::create([
            'kode_kain' => 'COT-TEST-2',
            'nama_kain' => 'Cotton Test 2',
            'category_id' => $category->id,
            'harga_per_meter' => 10000,
            'harga_per_rol' => 450000,
            'meter_per_rol' => 50.00,
            'stok_minimum' => 5,
            'status' => 'aktif',
        ]);

        $stock = Stock::create([
            'fabric_id' => $fabric->id,
            'stok_rol' => 1,
            'stok_meter' => 8.00, // 8 loose, 50 in roll = 58m total
        ]);

        $this->expectException(\Exception::class);
        // Ask for 60 meters (we only have 58 total meters in stock)
        $this->stockService->kurangiStok($fabric, 'meter', 60, 1);
    }
}
