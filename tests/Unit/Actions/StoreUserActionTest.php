<?php

namespace Tests\Unit\Actions;

use App\Actions\StoreUserAction;
use App\DTOs\Users\StoreUserDTO;
use App\Models\Balance;
use App\Models\User;
use Illuminate\Support\Facades\App;
use Tests\TestCase;

class StoreUserActionTest extends TestCase
{
    protected StoreUserAction $storeUserAction;

    public function setUp(): void
    {
        parent::setUp();
        $this->storeUserAction = App::make(StoreUserAction::class);
    }

    public function test_can_store_user()
    {
        $storeUserResult = $this->storeUserAction->execute(
            StoreUserDTO::new()->buildFromArray([
                'name' => 'test name',
                'email' => 'test@email.com',
                'initial_balance' => -100,
            ])
        );

        $this->assertTrue($storeUserResult->isSuccess());

        $this->assertDatabaseHas(User::class, [
            'id' => $storeUserResult->getData('user')->id,
            'name' => 'test name',
            'email' => 'test@email.com',
        ])
            ->assertDatabaseHas(Balance::class, [
                'user_id' => $storeUserResult->getData('user')->id,
                'transaction_id' => null,
                'amount' => -100.00
            ]);
    }

    public function test_can_store_user_without_balance()
    {
        $storeUserResult = $this->storeUserAction->execute(
            StoreUserDTO::new()->buildFromArray([
                'name' => 'test name',
                'email' => 'main@email.com',
            ])
        );

        $this->assertTrue($storeUserResult->isSuccess());

        $this->assertDatabaseHas(User::class, [
            'id' => $storeUserResult->getData('user')->id,
            'name' => 'test name',
            'email' => 'main@email.com',
        ])
            ->assertDatabaseHas(Balance::class, [
                'user_id' => $storeUserResult->getData('user')->id,
                'transaction_id' => null,
                'amount' => 0
            ]);
    }

    public function test_can_not_store_user_with_duplicate_email()
    {
        User::factory()
            ->create(['email' => 'example@email.com']);

        $storeUserResult = $this->storeUserAction->execute(
            StoreUserDTO::new()->buildFromArray([
                'name' => 'test name',
                'email' => 'example@email.com',
            ])
        );

        $this->assertTrue($storeUserResult->isServerError());
    }
}
