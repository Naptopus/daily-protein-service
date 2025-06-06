<?php

namespace Tests\Unit\Services;

use App\Data\ItemData;
use App\Models\Item;
use App\Models\Record;
use App\Models\User;
use App\Services\ItemService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ItemServiceTest extends TestCase
{
    use RefreshDatabase;

    protected ItemService $itemService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->itemService = app(ItemService::class);
    }

    /** @test */
    public function it_creates_item_and_record_if_record_not_exists()
    {
        $user     = User::factory()->create(['default_target' => 100]);
        $date     = Carbon::parse('2025-06-06');
        $itemData = new ItemData(
            name: 'Chicken',
            protein: 30.5,
            date: $date
        );

        $item = $this->itemService->createWithRecord($itemData, $user);

        $this->assertDatabaseHas('records', [
            'user_id' => $user->id,
            'date'    => $date->toDateString(),
            'target'  => 100,
        ]);

        $this->assertDatabaseHas('items', [
            'name'      => 'Chicken',
            'protein'   => 30.5,
            'record_id' => Record::where('user_id', $user->id)->where('date', $date->toDateString())->first()->id,
        ]);

        $this->assertInstanceOf(Item::class, $item);
        $this->assertEquals('Chicken', $item->name);
    }

    /** @test */
    public function it_creates_item_with_existing_record()
    {
        $user = User::factory()->create(['default_target' => 150]);
        $date = Carbon::parse('2025-06-06');

        $record = Record::factory()->create([
            'user_id' => $user->id,
            'date'    => $date->toDateString(),
            'target'  => 150,
        ]);

        $itemData = new ItemData(
            name: 'Egg',
            protein: 12.0,
            date: $date
        );

        $item = $this->itemService->createWithRecord($itemData, $user);

        // 應該只會有一個 record
        $this->assertEquals(1, Record::where('user_id', $user->id)->where('date', $date->toDateString())->count());

        $this->assertDatabaseHas('items', [
            'name'      => 'Egg',
            'protein'   => 12.0,
            'record_id' => $record->id,
        ]);
    }

    /** @test */
    public function it_uses_today_if_date_not_specified()
    {
        $user  = User::factory()->create();
        $today = Carbon::today();

        $itemData = new ItemData(
            name: 'Tofu',
            protein: 8.0,
            date: $today
        );

        $item = $this->itemService->createWithRecord($itemData, $user);

        $this->assertDatabaseHas('items', [
            'name'    => 'Tofu',
            'protein' => 8.0,
        ]);
        $this->assertEquals($today->toDateString(), $item->record->date->toDateString());
    }
}
