<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Aimix\Shop\app\Models\Brand;
use Illuminate\Support\Facades\Redis;

class CompanyController extends Controller
{
	public function companiesByArea(Request $request, $region, $area, $city){
        $key = 'companies';

		if($city != 'null')
            $key .= ".city=$city";
        elseif($area != 'null')
            $key .= ".area=$area";
        elseif($region != 'null')
            $key .= ".region=$region";

        if($companies = Redis::get($key)){
          return response()->json(json_decode($companies));
        }

		$companies = Brand::orderBy('is_popular')->orderBy('images->business_card');
		
		if($region != 'null')
			$companies = $companies->where('address->region', $region);
			
		if($area != 'null')
			$companies = $companies->where('address->area', $area);
			
		if($city != 'null')
			$companies = $companies->where('address->city', $city);
		
		$companies = $companies->paginate(4);
		$companies = new \App\Http\Resources\Companies($companies);

        Redis::set($key, json_encode($companies), 'EX', 108000);
					
		return response()->json($companies);
	}
	
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
    
    
}
