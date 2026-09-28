<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Tenant;

use App\Actions\Tenant\Category\DeleteCategoryAction;
use App\Actions\Tenant\Category\ListCategoriesAction;
use App\Actions\Tenant\Category\ShowCategoryAction;
use App\Actions\Tenant\Category\StoreCategoryAction;
use App\Actions\Tenant\Category\UpdateCategoryAction;
use App\DTOs\Tenant\Category\StoreCategoryDTO;
use App\DTOs\Tenant\Category\UpdateCategoryDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\Category\StoreCategoryRequest;
use App\Http\Requests\Tenant\Category\UpdateCategoryRequest;
use App\Http\Resources\Tenant\CategoryResource;
use App\Models\Category;
use Dedoc\Scramble\Attributes\Endpoint;
use Dedoc\Scramble\Attributes\Group;
use Dedoc\Scramble\Attributes\QueryParameter;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Gate;

#[Group(name: 'Tenant / Categorias', description: 'Gerenciamento do Catálogo de Categorias do Tenant')]
final class CategoryController extends Controller
{
    #[Endpoint(title: 'Listar Categorias', description: 'Retorna a lista paginada de categorias do tenant.')]
    #[QueryParameter(name: 'search', description: 'Busca por nome, slug ou descrição', type: 'string')]
    #[QueryParameter(name: 'is_active', description: 'Filtra pelo status ativo (true/false)', type: 'boolean')]
    #[QueryParameter(name: 'parent_id', description: 'Filtra subcategorias de um pai específico', type: 'integer')]
    #[QueryParameter(name: 'per_page', description: 'Quantidade de registros por página (padrão: 15)', type: 'integer')]
    public function index(Request $request, ListCategoriesAction $action): AnonymousResourceCollection
    {
        Gate::authorize('viewAny', Category::class);

        $perPage = (int) $request->query('per_page', 15);
        $search = $request->query('search');
        $searchString = is_string($search) ? $search : null;
        $isActive = $request->has('is_active') ? $request->boolean('is_active') : null;
        $parentId = $request->has('parent_id') ? (int) $request->query('parent_id') : null;

        $categories = $action->execute($perPage, $searchString, $isActive, $parentId);

        return CategoryResource::collection($categories);
    }

    #[Endpoint(title: 'Criar Categoria', description: 'Cria uma nova categoria no catálogo do tenant.')]
    public function store(StoreCategoryRequest $request, StoreCategoryAction $action): CategoryResource
    {
        $category = $action->execute(StoreCategoryDTO::fromRequest($request));

        return new CategoryResource($category);
    }

    #[Endpoint(title: 'Exibir Categoria', description: 'Exibe os detalhes de uma categoria específica pelo seu UUID ou ID.')]
    public function show(Category $category, ShowCategoryAction $action): CategoryResource
    {
        Gate::authorize('view', $category);

        return new CategoryResource($action->execute($category));
    }

    #[Endpoint(title: 'Atualizar Categoria', description: 'Atualiza os dados de uma categoria existente.')]
    public function update(UpdateCategoryRequest $request, Category $category, UpdateCategoryAction $action): CategoryResource
    {
        $updatedCategory = $action->execute($category, UpdateCategoryDTO::fromRequest($request));

        return new CategoryResource($updatedCategory);
    }

    #[Endpoint(title: 'Remover Categoria', description: 'Remove uma categoria (soft delete).')]
    public function destroy(Category $category, DeleteCategoryAction $action): JsonResponse
    {
        Gate::authorize('delete', $category);

        $action->execute($category);

        return response()->json([
            'message' => 'Category deleted successfully.',
        ]);
    }
}
