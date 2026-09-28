<?php

declare(strict_types=1);

namespace App\DTOs\Tenant\Category;

use App\Http\Requests\Tenant\Category\UpdateCategoryRequest;

final readonly class UpdateCategoryDTO
{
    public function __construct(
        public ?string $name = null,
        public ?string $slug = null,
        public ?string $description = null,
        public ?int $parentId = null,
        public ?int $position = null,
        public ?bool $isActive = null,
        public ?string $imagePath = null,
        public ?array $meta = null,
    ) {}

    /**
     * @param  UpdateCategoryRequest|array<string, mixed>  $data
     */
    public static function fromRequest(UpdateCategoryRequest|array $data): self
    {
        $validated = $data instanceof UpdateCategoryRequest ? $data->validated() : $data;

        return new self(
            name: isset($validated['name']) ? (string) $validated['name'] : null,
            slug: isset($validated['slug']) ? (string) $validated['slug'] : null,
            description: isset($validated['description']) ? (string) $validated['description'] : null,
            parentId: array_key_exists('parent_id', $validated) ? ($validated['parent_id'] !== null ? (int) $validated['parent_id'] : null) : null,
            position: isset($validated['position']) ? (int) $validated['position'] : null,
            isActive: isset($validated['is_active']) ? (bool) $validated['is_active'] : null,
            imagePath: isset($validated['image_path']) ? (string) $validated['image_path'] : null,
            meta: isset($validated['meta']) && is_array($validated['meta']) ? $validated['meta'] : null,
        );
    }
}
