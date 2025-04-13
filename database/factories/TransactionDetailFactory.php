<?php

namespace Database\Factories;

use App\Enums\TransactionDetailTypeEnum;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TransactionDetail>
 */
class TransactionDetailFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'transaction_id' => Transaction::factory(),
            'user_id' => User::factory(),
            'type' => TransactionDetailTypeEnum::DEPOSIT,
            'amount' => fake()->randomNumber(2),
        ];
    }
}
