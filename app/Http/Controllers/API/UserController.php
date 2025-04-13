<?php

namespace App\Http\Controllers\API;

use App\Actions\StoreUserAction;
use App\DTOs\Users\StoreUserDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Users\StoreUserRequest;
use App\Http\Resources\Api\Users\UserResource;
use App\Models\Balance;
use App\Models\User;

class UserController extends Controller
{
    public function __construct(protected User $user) {}

    public function store(StoreUserRequest $request, StoreUserAction $storeUserAction)
    {
        $storeUserResult = $storeUserAction->execute(StoreUserDTO::new()->buildFromRequest($request));

        if ($storeUserResult->isFail()) {
            return $this->operationResultFail($storeUserResult);
        }

        return $this->successResponse(['user' => UserResource::make($storeUserResult->getData('user'))]);
    }

    public function show(User $user, Balance $balance)
    {
        return $this->successResponse([
            'user' => UserResource::make($user),
            'balance' => $balance->getByUserId($user->id)
        ]);
    }
}
