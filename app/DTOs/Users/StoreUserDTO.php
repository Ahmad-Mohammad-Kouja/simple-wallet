<?php

namespace App\DTOs\Users;

use App\Http\Requests\Api\Users\StoreUserRequest;

class StoreUserDTO
{
    private string $name;

    private string $email;

    private float $initialBalance;

    public function __construct() {}

    public static function new()
    {
        return new self();
    }

    /**
     * Get the value of name
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Get the value of email
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * Get the value of initialBalance
     */
    public function getInitialBalance(): float
    {
        return $this->initialBalance;
    }

    public function buildFromRequest(StoreUserRequest $request): self
    {
        $this->name = $request->get('name');
        $this->email = $request->get('email');
        $this->initialBalance = $request->float('initial_balance', 0);
        return $this;
    }

    public function buildFromArray(array $data): self
    {
        $this->name = $data['name'];
        $this->email = $data['email'];
        $this->initialBalance = $data['initial_balance'] ?? 0;
        return $this;
    }
}
