<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PlacesController extends Controller
{
    public function search(Request $request, $type)
    {
        $places = \Algolia\AlgoliaSearch\PlacesClient::create('plFW1V0U6UIT', '73d34eae0793c3593db50fd0eb41ec35');
        $params = [
            'language' => 'ru',
            'countries' => 'ua',
            'type' => 'city'
        ];

        $result = $places->search($request->search, $params);

        if(!count($result['hits']))
            return response()->json([]);

        $items = [];

        foreach($result['hits'] as $hit) {
            foreach($hit[$type] as $item) {
                $items[$item] = $hit['_geoloc'];
            }
        }

        return response()->json($items);
    }
}
