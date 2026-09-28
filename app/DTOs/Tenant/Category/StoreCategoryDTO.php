<?php

declare(strict_types=1);

namespace App\DTOs\Tenant\Category;

use App\Http\Requests\Tenant\Category\StoreCategoryRequest;

final readonly class StoreCategoryDTO
{
    public function __construct(
        public string $name,
        public ?string $slug = null,
        public ?string $description = null,
        public ?int $parentId = null,
        public int $position = 0,
        public bool $isActive = true,
        public ?string $imagePath = null,
        public ?array $meta = null,
    ) {}

    /**
     * @param  StoreCategoryRequest|array<string, mixed>  $data
     */
    public static function fromRequest(StoreCategoryRequest|array $data): self
    {
        $validated = $data instanceof StoreCategoryRequest ? $data->validated() : $data;

        return new self(
            name: (string) $validated['name'],
            slug: isset($validated['slug']) ? (string) $validated['slug'] : null,
            description: isset($validated['description']) ? (string) $validated['description'] : null,
            parentId: isset($validated['parent_id']) ? (int) $validated['parent_id'] : null,
            position: isset($validated['position']) ? (int) $validated['position'] : 0,
            isActive: isset($validated['is_active']) ? (bool) $validated['is_active'] : true,
            imagePath: isset($validated['image_path']) ? (string) $validated['image_path'] : null,
            meta: isset($validated['meta']) && is_array($validated['meta']) ? $validated['meta'] : null,
        );
    }
}
