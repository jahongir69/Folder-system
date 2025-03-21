<?php
namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FolderResource extends JsonResource
{
    public function toArray(Request $request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'icon' => $this->icon ? asset("storage/{$this->icon}") : null,
            'parent_id' => $this->parent_id,
            'children' => FolderResource::collection($this->whenLoaded('children'))
        ];
    }
}
