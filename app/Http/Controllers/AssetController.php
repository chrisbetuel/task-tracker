<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AssetController extends Controller
{
    public function storeImage(Request $request, Task $task)
    {
        $user = request()->user();

        if ($task->department_id !== $user->department_id) {
            abort(403);
        }

        $request->validate([
            'file' => ['required', 'file', 'mimes:jpg,jpeg,png,gif,webp', 'max:20480'],
        ]);

        $file = $request->file('file');
        $path = $file->store('task-assets/' . $task->id, 'public');

        Asset::create([
            'department_id' => $task->department_id,
            'task_id' => $task->id,
            'name' => $file->getClientOriginalName(),
            'type' => 'image',
            'file_path' => $path,
            'created_by' => $user->id,
        ]);

        return back()->with('success', 'Image uploaded successfully.');
    }

    public function storeVideo(Request $request, Task $task)
    {
        $user = request()->user();

        if ($task->department_id !== $user->department_id) {
            abort(403);
        }

        $request->validate([
            'file' => ['required', 'file', 'mimes:mp4,mov,avi,mkv,webm', 'max:2048000'],
        ]);

        $file = $request->file('file');
        $path = $file->store('task-assets/' . $task->id, 'public');

        Asset::create([
            'department_id' => $task->department_id,
            'task_id' => $task->id,
            'name' => $file->getClientOriginalName(),
            'type' => 'video',
            'file_path' => $path,
            'created_by' => $user->id,
        ]);

        return back()->with('success', 'Video uploaded successfully.');
    }

    public function destroy(Asset $asset)
    {
        $user = request()->user();

        if ($asset->department_id !== $user->department_id) {
            abort(403);
        }

        if ($asset->file_path) {
            Storage::disk('public')->delete($asset->file_path);
        }

        $asset->delete();

        return back()->with('success', 'File deleted successfully.');
    }
}
