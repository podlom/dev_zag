<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class Companies extends ResourceCollection
{
	 public $collects = 'App\Http\Resources\Company';
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    
    public function toArray($request)
    {
        //return parent::toArray($request);
        return [
	        'data' => $this->collection,
	        'total' => $this->total,
            'current_page' => $request->page,
            'per_page' => $request->per_page,
            'last_page' => $request->per_page? ceil($this->total / $request->per_page) : 0
        ];
    }
    
    public function __construct($resource)
    {
        $this->total = $resource->total();

        $resource = $resource->getCollection();

        parent::__construct($resource);
    }
}
