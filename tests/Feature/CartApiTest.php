<?php

namespace Tests\Feature;

use App\Models\Cart;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class CartApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_add_product_to_cart(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $product = $this->createProduct(['price' => 10, 'offer_price' => 8.5]);

        $response = $this->postJson('/api/cart', [
            'product_id' => $product->id,
            'quantity' => 2,
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('status', 'success')
            ->assertJsonPath('data.quantity', 2)
            ->assertJsonPath('data.unit_price', 8.5)
            ->assertJsonPath('data.sub_total', 17.0);

        $this->assertDatabaseHas('carts', [
            'user_id' => $user->id,
            'product_id' => $product->id,
            'quantity' => 2,
        ]);
    }

    public function test_user_can_update_quantity(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $product = $this->createProduct(['price' => 12, 'offer_price' => 10]);

        $cart = Cart::create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'quantity' => 1,
        ]);

        $response = $this->patchJson("/api/cart/{$cart->id}", [
            'product_id' => $product->id,
            'quantity' => 3,
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('status', 'success')
            ->assertJsonPath('data.quantity', 3)
            ->assertJsonPath('data.sub_total', 30.0);

        $this->assertDatabaseHas('carts', [
            'id' => $cart->id,
            'quantity' => 3,
        ]);
    }

    public function test_user_can_remove_item(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $product = $this->createProduct();

        $cart = Cart::create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'quantity' => 1,
        ]);

        $response = $this->deleteJson("/api/cart/{$cart->id}");

        $response->assertStatus(200)
            ->assertJsonPath('status', 'success');

        $this->assertDatabaseMissing('carts', ['id' => $cart->id]);
    }

    public function test_user_can_get_cart_items_with_totals(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $productA = $this->createProduct(['price' => 20, 'offer_price' => 15]);
        $productB = $this->createProduct(['price' => 5, 'offer_price' => null]);

        Cart::create(['user_id' => $user->id, 'product_id' => $productA->id, 'quantity' => 2]);
        Cart::create(['user_id' => $user->id, 'product_id' => $productB->id, 'quantity' => 3]);

        $response = $this->getJson('/api/cart');

        $response->assertStatus(200)
            ->assertJsonPath('totals.total_items', 2)
            ->assertJsonPath('totals.total_quantity', 5)
            ->assertJsonPath('totals.total_price', 15 * 2 + 5 * 3);
    }

    private function createProduct(array $overrides = []): Product
    {
        $category = Category::create([
            'name' => 'General',
            'slug' => 'general',
            'image' => '',
            'description' => 'General category',
        ]);

        return Product::create(array_merge([
            'category_id' => $category->id,
            'name' => 'Product name',
            'slug' => 'product-name',
            'description' => 'Product description',
            'price' => 30,
            'offer_price' => null,
            'image' => '',
            'stock' => 100,
            'is_featured' => false,
        ], $overrides));
    }
}
