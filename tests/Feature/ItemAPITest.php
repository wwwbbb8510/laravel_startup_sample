<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use App\Models\User;
use App\Models\Item;
use Tests\TestCase;

class ItemAPITest extends TestCase
{
    use RefreshDatabase, WithFaker;
    /**
     * Item API base URI
     */
    private const API_URI_BASE = '/api/items';

    protected function setUp(): void
    {
        parent::setUp();
        //create user and set token for Sanctum
        Sanctum::actingAs(
            User::factory()->create()
        );
        //create items for tests
        Item::factory()->count(3)->create();
    }

    /**
     * Test item.index API.
     *
     * @return void
     */
    public function test_item_index()
    {
        $response = $this->get(self::API_URI_BASE);

        $response->assertStatus(200);
    }

    /**
     * Test item.store API
     */
    public function test_item_store()
    {
        $item_to_create = Item::factory()->make();
        $respone = $this->postJson(self::API_URI_BASE, $item_to_create->toArray());

        $respone
            ->assertStatus(201)
            ->assertJson(['data'=>$item_to_create->toArray()]);
    }

    /**
     * Test item.show API
     */
    public function test_item_show()
    {
        $item_created = Item::factory()->create();
        $respone = $this->getJson(self::API_URI_BASE . '/' . $item_created->id);

        $respone
            ->assertStatus(200)
            ->assertJson(['data' => $item_created->toArray()]);
    }

    /**
     * Test item.update API
     */
    public function test_item_update()
    {
        $item_created = Item::factory()->create();
        $item_created->stock = 10;
        $respone = $this->putJson(self::API_URI_BASE . '/' . $item_created->id, ['stock' => 10]);

        $respone
            ->assertStatus(200)
            ->assertJson(['data' => $item_created->toArray()]);
    }

    /**
     * Test item.destroy API
     */
    public function test_item_destroy()
    {
        $item_created = Item::factory()->create();
        $respone = $this->deleteJson(self::API_URI_BASE . '/' . $item_created->id);

        $respone->assertStatus(200);
    }
}
