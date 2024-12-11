<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Symfony\Component\DomCrawler\Crawler;
use App\Jobs\StartProductsParsing;
use Aimix\Shop\app\Models\Product;
use Aimix\Shop\app\Models\Modification;
use App\Region;
use App\Area;
use App\City;
use App\Kyivdistrict;
use UploadImage;
use App\ParsingLog;
use Illuminate\Support\Facades\DB;
use Image;

class ParserController extends Controller
{
    public function index(Request $request)
    {

        $data = [];
        $data['title'] = 'Парсер';
        $data['logs'] = ParsingLog::latest()->get()->pluck('date', 'id');
        $data['log'] = ParsingLog::latest()->first();
        $data['jobs_left'] = DB::table('jobs')->where('queue', 'parsing')->count();


        if($request->isJson)
            return response()->json($data);

        return view(backpack_view('crud/parser'), $data);
    }

    public function getLog(Request $request)
    {
        return response()->json(ParsingLog::find($request->id));
    }

    public function parse()
    {
        // Check for existing parsing process
        if(DB::table('jobs')->where('queue', 'parsing')->count())
            return;

        StartProductsParsing::dispatch()->onQueue('parsing');
    }
}
