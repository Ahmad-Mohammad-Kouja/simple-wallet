<?php


namespace App\Actions;

use App\DTOs\Users\StoreUserDTO;
use App\Helpers\OperationResult;
use App\Models\Balance;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class StoreUserAction
{
    public function __construct(
        protected User $user,
        protected Balance $balance,
        protected OperationResult $operationResult
    ) {}

    public function execute(StoreUserDTO $storeUserDTO)
    {
        try {
            DB::beginTransaction();
            $user = $this->user->create([
                'name' => $storeUserDTO->getName(),
                'email' => $storeUserDTO->getEmail(),
            ]);

            $this->balance->create([
                'user_id' => $user->id,
                'amount' => $storeUserDTO->getInitialBalance(),
            ]);

            $this->operationResult->setData($user, 'user');
            DB::commit();
        } catch (Exception $exception) {
            DB::rollBack();
            Log::warning("error on create user, exception: {$exception->getMessage()}");
            $this->operationResult->markAsInternalServerError($exception->getMessage());
        }

        return $this->operationResult;
    }
}
