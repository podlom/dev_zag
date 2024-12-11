<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Aimix\Shop\app\Models\Product;

class ContactsImport implements ToCollection
{
    /**
    * @param Collection $collection
    */
    public function collection(Collection $collection)
    {
        unset($collection[0]);

        foreach($collection as $row) {
            (new Product)->clearGlobalScopes();
            Product::whereIn('category_id', [2,7])->where('old_id', $row[0])->each(function($item) use ($row) {
                $extras = $item->extras;
                $extras['phone'] = $row[9];
                $extras['site'] = $row[11];
                $extras['email'] = $row[10];
                $item->update(['extras' => $extras]);
            });
        }
    }
}
