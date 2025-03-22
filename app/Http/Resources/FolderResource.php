<?php
namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FolderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'icon' => $this->icon ? asset('storage/' . $this->icon) : null,
            'parent' => new FolderResource($this->whenLoaded('parent')), 
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at->toDateTimeString(),
            'children' => FolderResource::collection($this->whenLoaded('children')) 
        ];
    }
}
