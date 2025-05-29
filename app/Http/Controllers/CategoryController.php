<?php

namespace App\Http\Controllers;

use App\Enums\SeveritySystemLog;
use App\Http\Messages\SuccessMessages;
use App\Http\Requests\PaginationRequest;
use App\Http\Resources\Category\CategoryResource;
use App\Http\Resources\PaginacionResource;
use App\Http\Responses\ApiResponse;
use App\Services\Category\CategoryService;
use App\Services\SystemLogService;
use App\Http\Requests\Category\CategoryRequest;
use App\Http\Requests\Category\PatchCategoryRequest;

class CategoryController extends Controller
{
    protected $categoryService;
    protected $systemLogService;

    public function __construct(CategoryService $categoryService, SystemLogService $systemLogService)
    {
        $this->categoryService = $categoryService;
        $this->systemLogService = $systemLogService;
    }
    
    /**
     * @OA\Post(
     *     path="/api/categories",
     *     summary="Register category",
     *     tags={"Categories"},
     *     @OA\RequestBody(
     *         required=true,
     *          @OA\JsonContent(
	 *             required={"name"},
	 *                 @OA\Property(property="name", type="string", maxLength=50),
     *         )
     *     ),
     *     @OA\Response(response=201, description="Category registered successfully"),
     *     @OA\Response(response=400, description="Invalid request")
     * )
     */

    public function register(CategoryRequest $CategoryRequest)
    {
        try {
            $data = $this->categoryService->createCategory($CategoryRequest->all());
            $this->systemLogService->logActivity('category','Category registrado',
                SeveritySystemLog::info->name,
                $data
            );
            return ApiResponse::success(SuccessMessages::CREATE_SUCCESS, new CategoryResource($data), [], 201);
        } catch (\Exception $e) {
            $this->systemLogService->logActivity(
                'category',
                'Registro de Category Fallido',
                SeveritySystemLog::error->name,
            );
            return ApiResponse::error($e->getMessage(), $e, [], 500);
        }
    }

    /**
     * @OA\Put(
     *     path="/api/categories/{id}",
     *     summary="Update category",
     *     tags={"Categories"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the category to update",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         request="PatchCategoryRequest",
     *         required=true,
     *         description="Updated category data",
     *         @OA\JsonContent(
	 *             required={"name"},
	 *                 @OA\Property(property="name", type="string", maxLength=50),
     *         )
     *     ),
     *     @OA\Response(response=200, description="Category updated successfully"),
     *     @OA\Response(response=500, description="Internal server error")
     * )
     */

    public function update_category(PatchCategoryRequest $categoryRequest, $id)
    {
        try {
            $category = $this->categoryService->updateCategory($id, $categoryRequest->validated());
            $this->systemLogService->logActivity(
                'category',
                'Category actualizado',
                SeveritySystemLog::info->name,
                $category
            );
            return ApiResponse::success(SuccessMessages::UPDATE_SUCCESS, new CategoryResource($category), [], 200);
        } catch (\Exception $e) {
            $this->systemLogService->logActivity(
                'category',
                'Actualización de category Fallida',
                SeveritySystemLog::error->name,
            );
            return ApiResponse::error($e->getMessage(), null, [], $e->getCode());
        }
    }

    /**
     * @OA\Get(
     *     path="/api/categories/{id}",
     *     summary="Get category by ID",
     *     tags={"Categories"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the category",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int64"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Category found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Category found"),
     *             @OA\Property(property="organization", type="object")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Category not found"),
     *     @OA\Response(response=500, description="Internal server error"),
     * )
     */

    public function get_category($id)
    {
        try {
            $category = $this->categoryService->getCategoryByid($id);
            return ApiResponse::success(SuccessMessages::SUCCESSFUL, new CategoryResource($category), [], 200);
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), null, [], $e->getCode());
        }
    }

    /**
     * @OA\Get(
     *     path="/api/categories",
     *     summary="List categories with pagination",
     *     tags={"Categories"},
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Search query",
     *         required=false,
     *         @OA\Schema(type="string", maxLength=255)
     *     ),
     *     @OA\Parameter(
     *         name="itemsPerPage",
     *         in="query",
     *         description="Items per page",
     *         required=true,
     *         @OA\Schema(type="integer", minimum=1)
     *     ),
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page number",
     *         required=true,
     *         @OA\Schema(type="integer", minimum=1)
     *     ),
     *     @OA\Parameter(
     *         name="sortBy",
     *         in="query",
     *         description="Sort by field",
     *         required=false,
     *         @OA\Schema(type="string", maxLength=255)
     *     ),
     *     @OA\Parameter(
     *         name="orderBy",
     *         in="query",
     *         description="Sort order",
     *         required=false,
     *         @OA\Schema(type="string", enum={"asc", "desc"})
     *     ),
     *     @OA\Response(response=200, description="Successful operation"),
     *     @OA\Response(response=500, description="Internal server error")
     * )
     */
    public function list_category_pagination(PaginationRequest $pagerequest)
    {
        try {
            $objects= $this->categoryService->getAllCategoryPagination($pagerequest);
            $objects->data = CategoryResource::collection($objects->getCollection());
            return ApiResponse::success(SuccessMessages::SUCCESSFUL,  new PaginacionResource($objects), [], 200);
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), $e, [], $e->getCode());
        }
    }
}
