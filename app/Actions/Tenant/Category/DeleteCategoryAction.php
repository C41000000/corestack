<?php

declare(strict_types=1);

namespace App\Actions\Tenant\Category;

use App\Models\Category;

final readonly class DeleteCategoryAction
{
    public function execute(Category $category): bool
    {
        return (bool) $category->delete();
    }
}
