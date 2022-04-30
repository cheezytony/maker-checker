<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Login\StoreRequest;
use App\Services\Admin\LoginService;
use Illuminate\Http\JsonResponse;

class LoginController extends Controller
{
    /**
     * @var LoginService
     */
    protected LoginService $loginService;

    /**
     * Initialize service class.
     *
     * @param LoginService $loginService
     */
    public function __construct(LoginService $loginService)
    {
        $this->loginService = $loginService;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  StoreRequest $request
     * @return JsonResponse
     */
    public function store(StoreRequest $request): JsonResponse
    {
        return $this->jsonResponse(
            $this->loginService->login(
                $request->validated('email'),
                $request->validated('password'),
            ),
        );
    }
}
