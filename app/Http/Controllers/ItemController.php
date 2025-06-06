<?php

namespace App\Http\Controllers;

use App\Data\ItemData;
use App\Http\Requests\StoreItemRequest;
use App\Services\ItemService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ItemController extends Controller
{
    public function __construct(
        private ItemService $itemService
    ) {
    }

    public function store(StoreItemRequest $request)
    {
        $date = $request->input('date') ? Carbon::parse($request->input('date'))->startOfDay() : Carbon::today();

        $itemData = new ItemData(
            $request->input('name'),
            $request->input('protein'),
            $date
        );

        $this->itemService->createWithRecord($itemData, auth()->user());

        return response()->noContent();
    }
}
