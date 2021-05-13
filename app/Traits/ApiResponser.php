<?php


namespace App\Traits;


use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

trait ApiResponser
{

    protected function success(array $data, int $statusCode = Response::HTTP_OK): JsonResponse
    {
        return  response()->json([
            'success' => true,
            'status_code' => $statusCode,
            'data' => $data
        ]);

    }

    protected function error(array $data, int $statusCode = Response::HTTP_BAD_REQUEST): JsonResponse
    {
        return response()->json([
            'success' => false,
            'status_code' => $statusCode,
            'errors' => $data
        ]);
    }

}
