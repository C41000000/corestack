<?php

declare(strict_types=1);

namespace App\Http\Resources\Tenant;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'parent_id' => $this->parent_id,
            'position' => (int) $this->position,
            'is_active' => (bool) $this->is_active,
            'image_path' => $this->image_path,
            'meta' => $this->meta,
            'parent' => $this->whenLoaded('parent', function () {
                return $this->parent ? new self($this->parent) : null;
            }),
            'children' => $this->whenLoaded('children', function () {
                return self::collection($this->children);
            }),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
