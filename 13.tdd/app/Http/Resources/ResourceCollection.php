<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection as BaseResourceCollection;

class ResourceCollection extends BaseResourceCollection
{
    public function toArray(Request $request): array
    {
        return [
            static::$wrap => $this->collection,
            'total' => $this->total(),
            'count' => $this->count(),
            'per_page' => $this->perPage(),
            'current_page' => $this->currentPage(),
            'total_pages' => $this->lastPage(),
        ];
    }
}
