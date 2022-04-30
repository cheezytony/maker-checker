<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Register\StoreRequest;
use App\Services\Admin\RegisterService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class RegisterController extends Controller
{
    /**
     * Registers a new admin.
     *
     * @param  StoreRequest $request
     * @return JsonResponse
     */
    public function store(StoreRequest $request, RegisterService $registerService): JsonResponse
    {
        return $this->jsonResponse(
            $registerService->createAccount($request->validated()),
            Response::HTTP_CREATED
        );
    }
}
