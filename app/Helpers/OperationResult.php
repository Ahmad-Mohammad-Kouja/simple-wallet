<?php

namespace App\Helpers;

class OperationResult
{
    private int $status = 200;

    private $data = [];

    private bool $isSuccess = true;

    private string $message = '';

    public function markAsFail(): self
    {
        $this->isSuccess = false;

        return $this;
    }

    public function setData(mixed $data, mixed $key = null): self
    {
        $key !== null
            ? $this->data[$key] = $data
            : $this->data = $data;

        return $this;
    }

    public function dataKeyExists(mixed $key): bool
    {
        return isset($this->data[$key]);
    }

    public function getData(mixed $key = null)
    {
        if ($key !== null && isset($this->data[$key])) {
            return $this->data[$key];
        }
        return $this->data;
    }

    public function isSuccess(): bool
    {
        return $this->isSuccess === true;
    }

    public function isFail(): bool
    {
        return $this->isSuccess === false;
    }

    public function setStatus(int $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getStatus(): int
    {
        return $this->status;
    }

    public function setMessage(string $message): self
    {
        $this->message = $message;

        return $this;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function isServerError(): bool
    {
        return $this->getStatus() >= 500 && $this->getStatus() < 600;
    }

    public function isClientError(): bool
    {
        return $this->getStatus() >= 400 && $this->getStatus() < 500;
    }

    public function markAsClientError(?string $message = null, mixed $data = null): self
    {
        $this->markAsFail()
            ->setStatus(400);

        if ($message !== null) {
            $this->setMessage($message);
        }

        if ($data !== null) {
            $this->setData($data);
        }

        return $this;
    }

    public function markAsInternalServerError(?string $message = null, mixed $data = null): self
    {
        $this->markAsFail()
            ->setStatus(500);

        if ($message !== null) {
            $this->setMessage($message);
        }

        if ($data !== null) {
            $this->setData($data);
        }

        return $this;
    }
}
