<?php
namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\LengthAwarePaginator;

abstract class Controller
{
    protected function success($data = [], string $message = 'Operation successful', int $status = 200): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'message' => $message,
            'data' => $data,
        ], $status);
    }

    protected function responsePagination($paginator, $data = [], string $message = 'Operation successful', int $status = 200): JsonResponse
    {
        $pagination = null;

        if ($paginator instanceof LengthAwarePaginator || $paginator instanceof Paginator) {
            $pagination = [
                'current_page' => $paginator->currentPage(),
                'per_page' => $paginator->perPage(),
                'has_more_pages' => $paginator->hasMorePages(),
                'links' => [
                    'prev' => $paginator->previousPageUrl(),
                    'next' => $paginator->nextPageUrl(),
                ],
            ];

            if ($paginator instanceof LengthAwarePaginator) {
                $pagination['total'] = $paginator->total();
                $pagination['total_pages'] = $paginator->lastPage();
                $pagination['links']['first'] = $paginator->url(1);
                $pagination['links']['last'] = $paginator->url($paginator->lastPage());
            }
        }

        return response()->json([
            'status' => 'success',
            'message' => $message,
            'data' => $data,
            'pagination' => $pagination,
        ], $status);
    }

    protected function error(string $message = 'An error occurred', int $status = 400): JsonResponse
    {
        return response()->json([
            'status' => 'error',
            'message' => $message,
        ], $status);
    }
    public function uploadPhoto($file, $path = "uploads"): mixed
    {
        $photoName = md5(time() . $file->getFilename()) . '.' . $file->getClientOriginalExtension();
        return $file->storeAs($path, $photoName, 'public');
    }
    
    public function deletePhoto($path): void
    {
        $fullpath = storage_path('app/public/' . $path);
        if (file_exists($fullpath)) {
            unlink($fullpath);
        }
    }
  
    
}
