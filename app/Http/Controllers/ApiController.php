<?php
namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;

class ApiController extends Controller
{
    protected function successResponse($data, $message = "Success", $status = 200): JsonResponse
    {
        return response()->json([
            'status' => true,
            'message' => $message,
            'data' => $data
        ], $status);
    }

    protected function errorResponse($message = "Error", $status = 400): JsonResponse
    {
        return response()->json([
            'status' => false,
            'message' => $message
        ], $status);
    }
}
