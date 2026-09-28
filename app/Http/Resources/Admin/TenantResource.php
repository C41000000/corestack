<?php

declare(strict_types=1);

namespace App\Http\Resources\Admin;

use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TenantResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $customColumns = Tenant::getCustomColumns();
        $virtualData = array_filter(
            $this->resource->getAttributes(),
            fn ($key) => ! in_array($key, $customColumns, true),
            ARRAY_FILTER_USE_KEY
        );

        return [
            'id' => $this->id,
            'is_active' => (bool) $this->is_active,
            'data' => ! empty($virtualData) ? $virtualData : null,
            'domains' => $this->whenLoaded('domains', function () {
                return $this->domains->pluck('domain');
            }),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
