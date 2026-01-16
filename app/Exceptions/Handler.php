<?php

namespace App\Exceptions;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler
{
    public function handleException(Throwable $e): JsonResponse
    {
        return match (true) {
            $e instanceof ServiceException => $this->businessException($e),
            $e instanceof ValidationException => $this->validationException($e),
            $e instanceof ModelNotFoundException => $this->modelNotFound($e),
            $e instanceof NotFoundHttpException => $this->notFound(),
            $e instanceof QueryException => $this->queryException($e),
            default => $this->genericException($e),
        };
    }

    protected function businessException(ServiceException $e): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $e->getMessage(),
            'errors' => $e->getErrors(),
        ], $e->getCode());
    }

    protected function validationException(ValidationException $e): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => 'Doğrulama hatası.',
            'errors' => $e->errors(),
        ], 422);
    }

    protected function modelNotFound(ModelNotFoundException $e): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => class_basename($e->getModel()) . ' bulunamadı.',
            'errors' => [],
        ], 404);
    }

    protected function notFound(): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => 'Endpoint bulunamadı.',
            'errors' => [],
        ], 404);
    }

    protected function queryException(QueryException $e): JsonResponse
    {
        $message = $e->getMessage();

        if (str_contains($message, 'invalid input syntax for type uuid')) {
            return response()->json([
                'success' => false,
                'message' => 'Geçersiz ID formatı.',
                'errors' => [],
            ], 400);
        }

        if (str_contains($message, 'violates foreign key constraint')) {
            return response()->json([
                'success' => false,
                'message' => 'İlişkili kayıt bulunamadı.',
                'errors' => [],
            ], 400);
        }

        if (str_contains($message, 'duplicate key value')) {
            return response()->json([
                'success' => false,
                'message' => 'Bu kayıt zaten mevcut.',
                'errors' => [],
            ], 409);
        }

        return response()->json([
            'success' => false,
            'message' => 'Veritabanı hatası.',
            'errors' => app()->isLocal() ? [$message] : [],
        ], 500);
    }

    protected function genericException(Throwable $e): JsonResponse
    {
        $code = method_exists($e, 'getStatusCode') ? $e->getStatusCode() : 500;

        return response()->json([
            'success' => false,
            'message' => app()->isLocal() ? $e->getMessage() : 'Bir hata oluştu.',
            'errors' => app()->isLocal() ? [
                'exception' => get_class($e),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ] : [],
        ], $code);
    }
}
