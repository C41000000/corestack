<?php

declare(strict_types=1);

namespace App\Actions\Tenant\Category;

use App\Models\Category;

final readonly class ShowCategoryAction
{
    public function execute(Category $category): Category
    {
        return $category->load(['parent', 'children']);
    }
}
