<?php

declare(strict_types=1);

namespace App\Actions\Admin\Tenant;

use App\Models\Tenant;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

final readonly class ListTenantsAction
{
    public function execute(int $perPage = 15, ?string $search = null, ?bool $isActive = null): LengthAwarePaginator
    {
        $query = Tenant::with('domains');

        if ($isActive !== null) {
            $query->where('is_active', $isActive);
        }

        if ($search !== null && $search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('id', 'like', "%{$search}%")
                    ->orWhereHas('domains', function ($dq) use ($search) {
                        $dq->where('domain', 'like', "%{$search}%");
                    });
            });
        }

        return $query->latest()->paginate($perPage);
    }
}
