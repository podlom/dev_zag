<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class MetaController extends Controller
{
  public function compareMeta(Request $request)
  {
    $meta_title = $request->meta_title;
    $meta_desc = $request->meta_desc;
    $id = $request->id;

    if($meta_title) {
      $result = \DB::table('articles')->where('id', '!=', $id)->where('meta_title', $meta_title)->count();
      $result += \DB::table('products')->where('id', '!=', $id)->where('meta_title', $meta_title)->count();
      $result += \DB::table('categories')->where('id', '!=', $id)->where('meta_title', $meta_title)->count();
      $result += \DB::table('product_categories')->where('id', '!=', $id)->where('meta_title', $meta_title)->count();
      $result += \DB::table('brands')->where('id', '!=', $id)->where('extras->meta_title', $meta_title)->count();
      $result += \DB::table('brand_categories')->where('id', '!=', $id)->where('extras->meta_title', $meta_title)->count();
    } elseif($meta_desc) {
      $result = \DB::table('articles')->where('id', '!=', $id)->where('meta_desc', $meta_desc)->count();
      $result += \DB::table('products')->where('id', '!=', $id)->where('meta_description', $meta_desc)->count();
      $result += \DB::table('categories')->where('id', '!=', $id)->where('meta_desc', $meta_desc)->count();
      $result += \DB::table('product_categories')->where('id', '!=', $id)->where('meta_desc', $meta_desc)->count();
      $result += \DB::table('brands')->where('id', '!=', $id)->where('extras->meta_desc', $meta_desc)->count();
      $result += \DB::table('brand_categories')->where('id', '!=', $id)->where('extras->meta_desc', $meta_desc)->count();
    } else {
      $result = 0;
    }

    return response()->json($result);
  }
}