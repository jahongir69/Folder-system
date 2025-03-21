<?php
namespace App\Http\Controllers;

use App\Http\Requests\FolderRequest;
use App\Http\Resources\FolderResource;
use App\Models\Folder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FolderController extends ApiController
{
    public function index(Request $request)
    {
        $folders = Folder::where('user_id', auth()->id())
            ->when($request->search, function ($query) use ($request) {
                $query->where('name', 'like', "%{$request->search}%");
            })
            ->with('children')
            ->paginate(10)

        return $this->successResponse($folders, "Folders fetched successfully");
    }

    public function store(FolderRequest $request)
    {
        $data = $request->validated();
        $data['user_id'] = auth()->id();

        if ($request->hasFile('icon')) {
            $data['icon'] = $request->file('icon')->store('icons', 'public');
        }

        $folder = Folder::create($data);
        return $this->successResponse(new FolderResource($folder), "Folder created successfully");
    }

    public function update(FolderRequest $request, Folder $folder)
    {
        if ($folder->user_id !== auth()->id()) {
            return $this->errorResponse("Unauthorized", 403);
        }

        $data = $request->validated();

        if ($request->hasFile('icon')) {
            if ($folder->icon) {
                Storage::disk('public')->delete($folder->icon);
            }
            $data['icon'] = $request->file('icon')->store('icons', 'public');
        }

        $folder->update($data);
        return $this->successResponse(new FolderResource($folder), "Folder updated successfully");
    }

    public function destroy(Folder $folder)
    {
        if ($folder->user_id !== auth()->id()) {
            return $this->errorResponse("Unauthorized", 403);
        }

        if ($folder->icon) {
            Storage::disk('public')->delete($folder->icon);
        }

        $folder->delete();
        return $this->successResponse(null, "Folder deleted successfully");
    }
}
