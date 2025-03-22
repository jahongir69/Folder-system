<?php

namespace App\Http\Controllers;

use App\Http\Requests\FolderRequest;
use App\Http\Resources\FolderResource;
use App\Models\Folder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FolderController extends Controller

{
    protected function sendResponse($result, $message): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $result,
            'message' => $message
        ], 200);
    }

    protected function sendError($message, $code = 400): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message
        ], $code);
    }

    public function index()
    {
        $folders = Folder::where('user_id', Auth::id())
            ->whereNull('parent_id')
            ->with('children')
            ->paginate(10);
        
        return $this->sendResponse(FolderResource::collection($folders), 'Folders retrieved successfully.');
    }

    public function store(FolderRequest $request)
    {
        $data = $request->only('name', 'parent_id');
        $data['user_id'] = Auth::id();
        
        if ($request->hasFile('icon')) {
            $data['icon'] = $request->file('icon')->store('folder_icons', 'public');
        }

        $folder = Folder::create($data);
        return $this->sendResponse(new FolderResource($folder), 'Folder created successfully.');
    }

    public function show($id)
    {
        $folder = Folder::where('user_id', Auth::id())
            ->with('children')
            ->find($id);
        
        if (!$folder) {
            return $this->sendError('Folder not found.', 404);
        }
        
        return $this->sendResponse(new FolderResource($folder), 'Folder retrieved successfully.');
    }

    public function update(FolderRequest $request, $id)
    {
        $folder = Folder::where('user_id', Auth::id())->find($id);
        if (!$folder) {
            return $this->sendError('Folder not found.', 404);
        }

        $folder->update($request->only('name', 'parent_id'));

        if ($request->hasFile('icon')) {
            $folder->icon = $request->file('icon')->store('folder_icons', 'public');
            $folder->save();
        }

        return $this->sendResponse(new FolderResource($folder), 'Folder updated successfully.');
    }

    public function destroy($id)
    {
        $folder = Folder::where('user_id', Auth::id())->find($id);
        if (!$folder) {
            return $this->sendError('Folder not found.', 404);
        }

        $folder->delete();
        return $this->sendResponse([], 'Folder deleted successfully.');
    }

    public function search(Request $request)
    {
        $query = Folder::where('user_id', Auth::id());

        if ($request->has('name')) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }

        $folders = $query->paginate(10);
        return $this->sendResponse(FolderResource::collection($folders), 'Folders filtered successfully.');
    }
}
