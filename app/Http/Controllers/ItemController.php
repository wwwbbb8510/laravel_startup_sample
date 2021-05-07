<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Http\Resources\ItemResource;
use App\Http\Resources\ItemCollection;
use Illuminate\Http\Request;

class ItemController extends Controller
{
    /**
     * default number of items per page
     */
    private const DEFAULT_PAGINATION_NUM = 15;
    /**
     * default value for stock
     */
    private const DEFAULT_STOCK_NUM = 0;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return new ItemCollection(Item::paginate(self::DEFAULT_PAGINATION_NUM));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Validate the request
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'string|max:1024',
            'stock' => 'integer|gte:0',
        ]);

        //create a new item
        $item = new Item();
        $item->name = $request->name;
        $item->description = $request->description;
        $item->stock = (int)($request->stock ?? self::DEFAULT_STOCK_NUM);
        $item->save();

        return new ItemResource($item);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Item  $item
     * @return \Illuminate\Http\Response
     */
    public function show(Item $item)
    {
        return new ItemResource($item);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Item  $item
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Item $item)
    {
        // Validate the request
        $request->validate([
            'name' => 'string|max:255',
            'description' => 'string|max:1024',
            'stock' => 'integer|gte:0',
        ]);

        // update an item
        $item->name = $request->name ?? $item->name;
        $item->description = $request->description ?? $item->description;
        $item->stock = $request->stock ?? $item->stock;
        $item->save();

        return new ItemResource($item);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Item  $item
     * @return \Illuminate\Http\Response
     */
    public function destroy(Item $item)
    {
        $item->delete();
        return response()->json(null);
    }
}
