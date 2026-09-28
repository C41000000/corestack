<?php

declare(strict_types=1);

namespace App\Actions\Tenant\Category;

use App\DTOs\Tenant\Category\UpdateCategoryDTO;
use App\Models\Category;

final readonly class UpdateCategoryAction
{
    public function execute(Category $category, UpdateCategoryDTO $dto): Category
    {
        $attributes = [];

        if ($dto->name !== null) {
            $attributes['name'] = $dto->name;
        }

        if ($dto->slug !== null) {
            $attributes['slug'] = $dto->slug;
        }

        if ($dto->description !== null) {
            $attributes['description'] = $dto->description;
        }

        if ($dto->parentId !== null || $dto->parentId === null) {
            $attributes['parent_id'] = $dto->parentId;
        }

        if ($dto->position !== null) {
            $attributes['position'] = $dto->position;
        }

        if ($dto->isActive !== null) {
            $attributes['is_active'] = $dto->isActive;
        }

        if ($dto->imagePath !== null) {
            $attributes['image_path'] = $dto->imagePath;
        }

        if ($dto->meta !== null) {
            $attributes['meta'] = array_merge($category->meta ?? [], $dto->meta);
        }

        if (! empty($attributes)) {
            $category->update($attributes);
        }

        return $category->refresh()->load(['parent', 'children']);
    }
}
