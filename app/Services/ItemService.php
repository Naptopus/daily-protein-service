<?php

namespace App\Services;

use App\Data\ItemData;
use App\Models\Item;
use App\Models\Record;
use App\Models\User;
use Illuminate\Support\Carbon;

class ItemService
{
    public function createWithRecord(ItemData $itemData, User $user): Item
    {
        $record = Record::firstOrCreate(
            ['date' => $itemData->date->toDateString(), 'user_id' => $user->id],
            ['target' => $user->default_target]
        );

        return $record->items()->create([
            'name'    => $itemData->name,
            'protein' => $itemData->protein
        ]);
    }
}
