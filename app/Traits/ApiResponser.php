<?php


namespace App\Traits;


use Exception;
use Illuminate\Http\Response;

trait ApiResponser
{

    protected function successResponse(array $data, int $statusCode = Response::HTTP_OK): array
    {
        return [
            'sucesso' => true,
            'code' => $statusCode,
            'data' => $data
        ];
    }

    protected function errorResponse(Exception $e, int $statusCode = Response::HTTP_BAD_REQUEST):array
    {
        return [
            'sucesso' => false,
            'code' => $statusCode,
            'error_description' => $e->getMessage()
        ];
    }



}
