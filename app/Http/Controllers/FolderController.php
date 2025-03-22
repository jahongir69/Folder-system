<?php

namespace App\Http\Controllers;

use App\Http\Requests\FolderRequest;
use App\Http\Resources\FolderResource;
use App\Models\Folder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FolderController extends Controller
{
    
    public function index()
    {
        $folders = Folder::with('children')
            ->where('user_id', auth()->id()) 
            ->whereNull('parent_id')
            ->paginate(10);

        return $this->responsePagination($folders, FolderResource::collection($folders), 'Folders retrieved successfully');
    }

    
    public function store(FolderRequest $request)
    {
        $data = $request->validated();
        $data['user_id'] = auth()->id(); 

        
        if ($request->hasFile('icon')) {
            $data['icon'] = $request->file('icon')->store('folder_icons', 'public');
        }

        $folder = Folder::create($data);
        return $this->success(new FolderResource($folder), 'Folder created successfully', 201);
    }

    
    public function update(FolderRequest $request, $id)
    {
        $folder = Folder::where('id', $id)->where('user_id', auth()->id())->first();
        if (!$folder) {
            return $this->error('Folder not found or access denied', 403);
        }

        $data = $request->validated();

        
        if ($request->hasFile('icon')) {
            if ($folder->icon) {
                Storage::disk('public')->delete($folder->icon); 
            }
            $data['icon'] = $request->file('icon')->store('folder_icons', 'public');
        }

        $folder->update($data);
        return $this->success(new FolderResource($folder), 'Folder updated successfully');
    }

    
    public function destroy($id)
    {
        $folder = Folder::where('id', $id)->where('user_id', auth()->id())->first();
        if (!$folder) {
            return $this->error('Folder not found or access denied', 403);
        }

        if ($folder->children()->exists()) {
            return $this->error('Cannot delete folder with subfolders', 400);
        }

        if ($folder->icon) {
            Storage::disk('public')->delete($folder->icon); 
        }

        $folder->delete();
        return $this->success([], 'Folder deleted successfully', 204);
    }


    public function search(Request $request)
    {
        $query = Folder::where('user_id', auth()->id());

        if ($request->has('name')) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }

        $folders = $query->paginate(10);
        return $this->responsePagination($folders, FolderResource::collection($folders), 'Search results');
    }
}
