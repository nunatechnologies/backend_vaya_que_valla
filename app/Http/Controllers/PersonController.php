<?php

namespace App\Http\Controllers;

use App\Enums\SeveritySystemLog;
use App\Http\Messages\SuccessMessages;
use App\Http\Requests\PaginationRequest;
use App\Http\Resources\Person\PersonResource;
use App\Http\Resources\PaginacionResource;
use App\Http\Responses\ApiResponse;
use App\Services\Person\PersonService;
use App\Services\SystemLogService;
use App\Http\Requests\Person\PersonRequest;
use App\Http\Requests\Person\PatchPersonRequest;

class PersonController extends Controller
{
    protected $personService;
    protected $systemLogService;

    public function __construct(PersonService $personService, SystemLogService $systemLogService)
    {
        $this->personService = $personService;
        $this->systemLogService = $systemLogService;
    }
    
    /**
     * @OA\Post(
     *     path="/api/people",
     *     summary="Register person",
     *     tags={"People"},
     *     @OA\RequestBody(
     *         required=true,
     *          @OA\JsonContent(
	 *             required={"user_id", "ci"},
	 *                 @OA\Property(property="user_id", type="number", maxLength=20),
	 *                 @OA\Property(property="ci", type="string", maxLength=10),
     *         )
     *     ),
     *     @OA\Response(response=201, description="Person registered successfully"),
     *     @OA\Response(response=400, description="Invalid request")
     * )
     */

    public function register(PersonRequest $PersonRequest)
    {
        try {
            $data = $this->personService->createPerson($PersonRequest->all());
            $this->systemLogService->logActivity('person','Person registrado',
                SeveritySystemLog::info->name,
                $data
            );
            return ApiResponse::success(SuccessMessages::CREATE_SUCCESS, new PersonResource($data), [], 201);
        } catch (\Exception $e) {
            $this->systemLogService->logActivity(
                'person',
                'Registro de Person Fallido',
                SeveritySystemLog::error->name,
            );
            return ApiResponse::error($e->getMessage(), $e, [], 500);
        }
    }

    /**
     * @OA\Put(
     *     path="/api/people/{id}",
     *     summary="Update person",
     *     tags={"People"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the person to update",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         request="PatchPersonRequest",
     *         required=true,
     *         description="Updated person data",
     *         @OA\JsonContent(
	 *             required={"user_id", "ci"},
	 *                 @OA\Property(property="user_id", type="number", maxLength=20),
	 *                 @OA\Property(property="ci", type="string", maxLength=10),
     *         )
     *     ),
     *     @OA\Response(response=200, description="Person updated successfully"),
     *     @OA\Response(response=500, description="Internal server error")
     * )
     */

    public function update_person(PatchPersonRequest $personRequest, $id)
    {
        try {
            $person = $this->personService->updatePerson($id, $personRequest->validated());
            $this->systemLogService->logActivity(
                'person',
                'Person actualizado',
                SeveritySystemLog::info->name,
                $person
            );
            return ApiResponse::success(SuccessMessages::UPDATE_SUCCESS, new PersonResource($person), [], 200);
        } catch (\Exception $e) {
            $this->systemLogService->logActivity(
                'person',
                'Actualización de person Fallida',
                SeveritySystemLog::error->name,
            );
            return ApiResponse::error($e->getMessage(), null, [], $e->getCode());
        }
    }

    /**
     * @OA\Get(
     *     path="/api/people/{id}",
     *     summary="Get person by ID",
     *     tags={"People"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the person",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int64"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Person found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Person found"),
     *             @OA\Property(property="organization", type="object")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Person not found"),
     *     @OA\Response(response=500, description="Internal server error"),
     * )
     */

    public function get_person($id)
    {
        try {
            $person = $this->personService->getPersonByid($id);
            return ApiResponse::success(SuccessMessages::SUCCESSFUL, new PersonResource($person), [], 200);
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), null, [], $e->getCode());
        }
    }

    /**
     * @OA\Get(
     *     path="/api/people",
     *     summary="List people with pagination",
     *     tags={"People"},
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
    public function list_person_pagination(PaginationRequest $pagerequest)
    {
        try {
            $objects= $this->personService->getAllPersonPagination($pagerequest);
            $objects->data = PersonResource::collection($objects->getCollection());
            return ApiResponse::success(SuccessMessages::SUCCESSFUL,  new PaginacionResource($objects), [], 200);
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), $e, [], $e->getCode());
        }
    }
}
