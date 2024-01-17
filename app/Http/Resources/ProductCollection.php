<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ProductCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  Request  $request
     * @return array<int|string, mixed>
     */
    public function toArray($request)
    {            
        // dd($this);
        return [

            'code' => 0,
            'message' => [],
            'response' => [
                'current_page' => $this->currentPage(),
                'data' => ProductResource::collection($this->collection),
                // 'first_page_url'=>$this->firstpageurl(),
                'per_page' => $this->perPage(),
                'total' => $this->total(),
            ],
        ];
    }
}
