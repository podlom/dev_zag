<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Aimix\Shop\app\Models\Product;
use Aimix\Shop\app\Models\Category;

class ComparisonController extends Controller
{
    public function index(Request $request)
    {
        $catalog_link = Category::whereIn('id', [2,7])->first()->link . '/catalog';

        return view('comparison.index', compact('catalog_link'));
    }

    public function getItems(Request $request)
    {
        $ids = $request->ids;
        $items = Product::whereIn('id', $ids)->orWhereIn('original_id', $ids)->paginate(100);
        $items = new \App\Http\Resources\Products($items);

        return response()->json(['items' => $items]);
    }

    public function getRecent(Request $request)
    {
        $ids = $request->ids;
        $items = Product::whereIn('id', $ids)->paginate(12);
        $items = new \App\Http\Resources\Products($items);

        return response()->json(['items' => $items]);
    }
}
