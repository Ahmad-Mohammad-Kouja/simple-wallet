<?php

namespace App\Traits;

use App\Helpers\OperationResult;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

trait ApiResponseHelper
{
    public function successResponse(
        mixed $data = [],
        ?string $message = null,
        array $headers = []
    ): JsonResponse {
        return $this->jsonResponse(
            [
                'message' => $message ?? __('global.response_messages.success'),
                'data' => $data,
            ],
            Response::HTTP_OK,
            $headers
        );
    }

    public function createdResponse(
        mixed $data = [],
        ?string $message = null,
        array $headers = []
    ): JsonResponse {
        return $this->jsonResponse(
            [
                'message' => $message,
                'data' => $data,
            ],
            Response::HTTP_CREATED,
            $headers
        );
    }

    public function deletedResponse(
        ?string $message = null,
        array $headers = []
    ): JsonResponse {
        return $this->jsonResponse(
            [
                'message' => $message,
            ],
            Response::HTTP_NO_CONTENT,
            $headers
        );
    }

    public function failedResponse(
        ?string $message = null,
        int $statusCode = Response::HTTP_BAD_REQUEST,
        array $headers = []
    ): JsonResponse {
        return $this->jsonResponse(
            ['message' => $message],
            $statusCode,
            $headers
        );
    }

    public function unprocessableResponse(
        mixed $errors = [],
        ?string $message = null,
        int $statusCode = Response::HTTP_UNPROCESSABLE_ENTITY,
        array $headers = []
    ): JsonResponse {
        return $this->jsonResponse(
            [
                'message' => $message,
                'errors' => $errors,
            ],
            $statusCode,
            $headers
        );
    }

    public function notFoundResponse(
        ?string $message = null,
        array $headers = []
    ): JsonResponse {
        return $this->jsonResponse(
            ['message' => $message],
            Response::HTTP_NOT_FOUND,
            $headers
        );
    }

    public function unauthorizedResponse(
        ?string $message = null,
        array $headers = []
    ): JsonResponse {
        return $this->jsonResponse(
            ['message' => $message],
            Response::HTTP_UNAUTHORIZED,
            $headers
        );
    }

    public function forbiddenResponse(
        ?string $message = null,
        array $headers = []
    ): JsonResponse {
        return $this->jsonResponse(
            ['message' => $message],
            Response::HTTP_FORBIDDEN,
            $headers
        );
    }

    public function serverErrorResponse(
        ?string $message = null,
        array $headers = []
    ): JsonResponse {
        return $this->jsonResponse(
            ['message' => $message],
            Response::HTTP_INTERNAL_SERVER_ERROR,
            $headers
        );
    }

    public function notAcceptAbleResponse(
        ?string $message = null,
        array $headers = []
    ): JsonResponse {
        return $this->jsonResponse(
            ['message' => $message],
            Response::HTTP_NOT_ACCEPTABLE,
            $headers
        );
    }

    public function operationResultFail(
        OperationResult $operationResult,
        array $headers = []
    ): JsonResponse {
        $message = config('app.debug', false)
            ? $operationResult->getMessage()
            : ($operationResult->isClientError()
                ? $operationResult->getMessage()
                : __('global.response_messages.server_error'));

        return $this->jsonResponse(
            [
                'message' => $message,
                'data' => $operationResult->getData(),
            ],
            $operationResult->getStatus(),
            $headers
        );
    }

    private function jsonResponse(array $data, int $code = Response::HTTP_OK, $headers = []): JsonResponse
    {
        return response()->json($data, $code, $headers);
    }
}
