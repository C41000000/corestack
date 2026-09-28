<?php

declare(strict_types=1);

namespace App\Actions\Tenant\Category;

use App\DTOs\Tenant\Category\StoreCategoryDTO;
use App\Models\Category;

final readonly class StoreCategoryAction
{
    public function execute(StoreCategoryDTO $dto): Category
    {
        $attributes = [
            'name' => $dto->name,
            'description' => $dto->description,
            'parent_id' => $dto->parentId,
            'position' => $dto->position,
            'is_active' => $dto->isActive,
            'image_path' => $dto->imagePath,
            'meta' => $dto->meta,
        ];

        if ($dto->slug !== null) {
            $attributes['slug'] = $dto->slug;
        }

        /** @var Category $category */
        $category = Category::create($attributes);

        return $category->load(['parent', 'children']);
    }
}
