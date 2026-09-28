<?php

declare(strict_types=1);

namespace App\Actions\Tenant\Category;

use App\Models\Category;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

final readonly class ListCategoriesAction
{
    public function execute(int $perPage = 15, ?string $search = null, ?bool $isActive = null, ?int $parentId = null): LengthAwarePaginator
    {
        $query = Category::with(['parent', 'children']);

        if ($isActive !== null) {
            $query->where('is_active', $isActive);
        }

        if ($parentId !== null) {
            $query->where('parent_id', $parentId);
        }

        if ($search !== null && $search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('slug', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        return $query->orderBy('position', 'asc')->orderBy('id', 'desc')->paginate($perPage);
    }
}
