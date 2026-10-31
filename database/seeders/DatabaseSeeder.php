<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Role;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database with realistic Coffee Shop data.
     */
    public function run(): void
    {
        $now = Carbon::now();

        // 1. Roles
        $adminRole = Role::firstOrCreate(
            ['slug' => 'admin'],
            ['name' => 'Administrator', 'description' => 'System administrator', 'is_active' => true]
        );

        $cashierRole = Role::firstOrCreate(
            ['slug' => 'cashier'],
            ['name' => 'Cashier', 'description' => 'Coffee shop cashier', 'is_active' => true]
        );

        // 2. Admin User
        $admin = User::firstOrCreate(
            ['email' => 'admin@coffee.test'],
            [
                'name' => 'System Admin',
                'first_name' => 'System',
                'last_name' => 'Admin',
                'phone' => '012345678',
                'gender' => 'other',
                'role_id' => $adminRole->id,
                'password' => Hash::make('admin12345'),
                'email_verified_at' => $now,
            ]
        );

        // 3. Cashier Users
        $cashier1 = User::firstOrCreate(
            ['email' => 'dara.sok@coffee.test'],
            [
                'name' => 'Dara Sok',
                'first_name' => 'Dara',
                'last_name' => 'Sok',
                'phone' => '089112233',
                'gender' => 'male',
                'role_id' => $cashierRole->id,
                'password' => Hash::make('password123'),
                'email_verified_at' => $now,
                'created_by' => $admin->id,
            ]
        );

        $cashier2 = User::firstOrCreate(
            ['email' => 'sreypov.meas@coffee.test'],
            [
                'name' => 'Sreypov Meas',
                'first_name' => 'Sreypov',
                'last_name' => 'Meas',
                'phone' => '098445566',
                'gender' => 'female',
                'role_id' => $cashierRole->id,
                'password' => Hash::make('password123'),
                'email_verified_at' => $now,
                'created_by' => $admin->id,
            ]
        );

        // 4. Today's Attendance (Unlocks POS for today!)
        DB::table('cashier_attendances')->updateOrInsert(
            [
                'cashier_id' => $cashier1->id,
                'attended_on' => $now->toDateString(),
            ],
            [
                'checked_in_at' => $now->copy()->subHours(2),
                'created_at' => $now,
                'updated_at' => $now,
            ]
        );

        // 5. Categories
        $categoriesData = [
            [
                'name' => 'Iced Coffee & Cold Brew',
                'slug' => 'iced-coffee-cold-brew',
                'description' => 'Refreshing iced espressos, cold brews, and cold coffee specialties.',
            ],
            [
                'name' => 'Hot Coffee & Espresso',
                'slug' => 'hot-coffee-espresso',
                'description' => 'Rich, aromatic hot coffee brewed from freshly roasted beans.',
            ],
            [
                'name' => 'Tea & Matcha Latte',
                'slug' => 'tea-matcha-latte',
                'description' => 'Artisanal ceremonial matcha and organic leaf teas.',
            ],
            [
                'name' => 'Frappes & Milk Teas',
                'slug' => 'frappes-milk-teas',
                'description' => 'Sweet, blended iced frappes and signature milk teas with boba.',
            ],
            [
                'name' => 'Bakery & Pastries',
                'slug' => 'bakery-pastries',
                'description' => 'Freshly baked pastries and croissants to complement your brew.',
            ],
        ];

        $categories = [];
        foreach ($categoriesData as $cData) {
            $categories[$cData['slug']] = Category::firstOrCreate(
                ['slug' => $cData['slug']],
                [
                    'name' => $cData['name'],
                    'description' => $cData['description'],
                    'is_active' => true,
                    'created_by' => $admin->id,
                ]
            );
        }

        // 6. Products with realistic prices, sizes, and images
        $productsData = [
            [
                'name' => 'Iced Caramel Macchiato',
                'category_slug' => 'iced-coffee-cold-brew',
                'description' => 'Rich espresso layered with fresh creamy milk and vanilla syrup, crowned with caramel drizzle.',
                'price_small' => 2.75,
                'price_medium' => 3.25,
                'price_large' => 3.75,
                'discount_percent' => 10.0,
                'image_path' => 'https://images.unsplash.com/photo-1517701550927-30cf4ba1dba5?w=500&auto=format&fit=crop&q=80',
            ],
            [
                'name' => 'Spanish Latte (Iced)',
                'category_slug' => 'iced-coffee-cold-brew',
                'description' => 'Signature double espresso combined with condensed milk and whole fresh milk over ice cubes.',
                'price_small' => 2.50,
                'price_medium' => 3.00,
                'price_large' => 3.50,
                'discount_percent' => 0.0,
                'image_path' => 'https://images.unsplash.com/photo-1541167760496-1628856ab772?w=500&auto=format&fit=crop&q=80',
            ],
            [
                'name' => 'Cold Brew Nitro Reserve',
                'category_slug' => 'iced-coffee-cold-brew',
                'description' => 'Steeped for 20 hours for velvety smoothness and ultra-low acidity.',
                'price_small' => 3.00,
                'price_medium' => 3.50,
                'price_large' => 4.00,
                'discount_percent' => 0.0,
                'image_path' => 'https://images.unsplash.com/photo-1517256064527-09c73fc73e38?w=500&auto=format&fit=crop&q=80',
            ],
            [
                'name' => 'Classic Americano (Hot)',
                'category_slug' => 'hot-coffee-espresso',
                'description' => 'Double shot of Arabica espresso gently stretched with hot water for a crisp, intense profile.',
                'price_small' => 1.75,
                'price_medium' => 2.25,
                'price_large' => 2.75,
                'discount_percent' => 0.0,
                'image_path' => 'https://images.unsplash.com/photo-1514432324607-a09d9b4aefdd?w=500&auto=format&fit=crop&q=80',
            ],
            [
                'name' => 'Creamy Cappuccino',
                'category_slug' => 'hot-coffee-espresso',
                'description' => 'Harmonious blend of rich espresso, silky steamed milk, and dense velvety micro-foam dusted with cinnamon.',
                'price_small' => 2.25,
                'price_medium' => 2.85,
                'price_large' => 3.40,
                'discount_percent' => 15.0,
                'image_path' => 'https://images.unsplash.com/photo-1572442388796-11668a67e53d?w=500&auto=format&fit=crop&q=80',
            ],
            [
                'name' => 'Kyoto Uji Matcha Latte',
                'category_slug' => 'tea-matcha-latte',
                'description' => 'First-harvest stone-ground green tea whisked traditionally with creamy oat milk and natural honey.',
                'price_small' => 3.00,
                'price_medium' => 3.60,
                'price_large' => 4.20,
                'discount_percent' => 10.0,
                'image_path' => 'https://images.unsplash.com/photo-1536256263959-770b48d82b0a?w=500&auto=format&fit=crop&q=80',
            ],
            [
                'name' => 'Brown Sugar Boba Milk Tea',
                'category_slug' => 'frappes-milk-teas',
                'description' => 'Warm caramelized brown sugar tapioca pearls paired with organic fresh milk and Ceylon black tea.',
                'price_small' => 2.80,
                'price_medium' => 3.40,
                'price_large' => 3.90,
                'discount_percent' => 0.0,
                'image_path' => 'https://images.unsplash.com/photo-1558857563-b37cfb83984d?w=500&auto=format&fit=crop&q=80',
            ],
            [
                'name' => 'Dark Chocolate Mocha Frappe',
                'category_slug' => 'frappes-milk-teas',
                'description' => 'Ice-blended espresso with Belgian dark chocolate ganache, topped with whipped cream and chocolate curls.',
                'price_small' => 3.25,
                'price_medium' => 3.85,
                'price_large' => 4.50,
                'discount_percent' => 20.0,
                'image_path' => 'https://images.unsplash.com/photo-1578314670146-24b335349e5d?w=500&auto=format&fit=crop&q=80',
            ],
            [
                'name' => 'French Butter Croissant',
                'category_slug' => 'bakery-pastries',
                'description' => 'Flaky, buttery layers baked golden to crisp perfection each morning.',
                'price_small' => 1.80,
                'price_medium' => 1.80,
                'price_large' => 1.80,
                'discount_percent' => 0.0,
                'image_path' => 'https://images.unsplash.com/photo-1555507036-ab1f4038808a?w=500&auto=format&fit=crop&q=80',
            ],
        ];

        $seededProducts = [];
        foreach ($productsData as $pData) {
            $cat = $categories[$pData['category_slug']] ?? null;
            $slug = Str::slug($pData['name']);

            $isSingleSize = $pData['category_slug'] === 'bakery-pastries';

            $seededProducts[] = Product::updateOrCreate(
                ['slug' => $slug],
                [
                    'name' => $pData['name'],
                    'category_id' => $cat?->id,
                    'created_by' => $admin->id,
                    'description' => $pData['description'],
                    'image_path' => $pData['image_path'],
                    'price' => $pData['price_small'],
                    'price_small' => $pData['price_small'],
                    'price_medium' => $pData['price_medium'],
                    'price_large' => $pData['price_large'],
                    'is_small_active' => true,
                    'is_medium_active' => !$isSingleSize,
                    'is_large_active' => !$isSingleSize,
                    'discount_percent' => $pData['discount_percent'],
                    'is_active' => true,
                ]
            );
        }

        // 7. Seed 2 Sample Orders for Order History preview
        if (Order::count() === 0 && count($seededProducts) >= 2) {
            $p1 = $seededProducts[0]; // Iced Caramel Macchiato
            $p2 = $seededProducts[4]; // Creamy Cappuccino

            $p1Price = $p1->sizePrice('medium');
            $p2Price = $p2->sizePrice('small');
            $orderTotal = $p1Price + $p2Price;

            $sampleOrder1 = Order::create([
                'order_number' => 'ORD-' . strtoupper(Str::random(6)),
                'cashier_id' => $cashier1->id,
                'subtotal' => $p1->sizeBasePrice('medium') + $p2->sizeBasePrice('small'),
                'discount' => ($p1->sizeBasePrice('medium') - $p1Price) + ($p2->sizeBasePrice('small') - $p2Price),
                'total' => $orderTotal,
                'payment_method' => 'cash',
                'payment_status' => 'paid',
                'amount_received' => 10.00,
                'change_amount' => 10.00 - $orderTotal,
                'status' => 'completed',
                'placed_at' => $now->copy()->subMinutes(35),
            ]);

            OrderItem::create([
                'order_id' => $sampleOrder1->id,
                'product_id' => $p1->id,
                'product_name' => $p1->name,
                'size' => 'medium',
                'sugar' => 50,
                'qty' => 1,
                'unit_price' => $p1Price,
                'line_total' => $p1Price,
            ]);

            OrderItem::create([
                'order_id' => $sampleOrder1->id,
                'product_id' => $p2->id,
                'product_name' => $p2->name,
                'size' => 'small',
                'sugar' => 25,
                'qty' => 1,
                'unit_price' => $p2Price,
                'line_total' => $p2Price,
            ]);

            // Sample Order 2 (KHQR)
            $p3 = $seededProducts[1]; // Spanish Latte
            $p3Price = $p3->sizePrice('large');

            $sampleOrder2 = Order::create([
                'order_number' => 'ORD-' . strtoupper(Str::random(6)),
                'cashier_id' => $cashier1->id,
                'subtotal' => $p3->sizeBasePrice('large'),
                'discount' => 0,
                'total' => $p3Price,
                'payment_method' => 'qr',
                'payment_status' => 'paid',
                'amount_received' => $p3Price,
                'change_amount' => 0,
                'status' => 'completed',
                'placed_at' => $now->copy()->subMinutes(12),
            ]);

            OrderItem::create([
                'order_id' => $sampleOrder2->id,
                'product_id' => $p3->id,
                'product_name' => $p3->name,
                'size' => 'large',
                'sugar' => 50,
                'qty' => 1,
                'unit_price' => $p3Price,
                'line_total' => $p3Price,
            ]);
        }
    }
}
