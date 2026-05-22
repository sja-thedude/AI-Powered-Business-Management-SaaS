<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Models\Inventory\Product;
use App\Models\Inventory\StockMovement;
use App\Models\Inventory\Supplier;
use App\Models\Inventory\Warehouse;
use Inertia\Inertia;

class InventoryController extends Controller
{
    public function index()
    {
        return Inertia::render('Modules/Scaffold', [
            'module' => [
                'name' => 'Inventory',
                'icon' => 'cube',
                'description' => 'Stock, warehouses, suppliers & inventory analytics.',
                'status' => 'scaffold',
            ],
            'stats' => [
                ['label' => 'Products', 'value' => Product::count(), 'icon' => 'cube', 'accent' => 'brand'],
                ['label' => 'Warehouses', 'value' => Warehouse::count(), 'icon' => 'building', 'accent' => 'violet'],
                ['label' => 'Suppliers', 'value' => Supplier::count(), 'icon' => 'id-badge', 'accent' => 'emerald'],
                ['label' => 'Stock Movements', 'value' => StockMovement::count(), 'icon' => 'cube', 'accent' => 'amber'],
            ],
            'tables' => ['warehouses', 'suppliers', 'products', 'stock_movements'],
        ]);
    }
}
