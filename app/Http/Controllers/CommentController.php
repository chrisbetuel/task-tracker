<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Project;
use App\Notifications\CommentAdded;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;

class CommentController extends Controller
{
    public function store(Request $request, Project $project)
    {
        $data = $request->validate([
            'body' => ['required', 'string', 'max:5000'],
        ]);

        $comment = Comment::create([
            'project_id' => $project->id,
            'user_id' => $request->user()->id,
            'body' => $data['body'],
        ]);

        $members = $project->tasks()
            ->whereNotNull('assigned_to')
            ->with('assignee')
            ->get()
            ->pluck('assignee')
            ->unique('id')
            ->reject(fn ($user) => $user->id === $request->user()->id);

        Notification::send($members, new CommentAdded($comment));

        return redirect()->back()->with('success', 'Comment added successfully.');
    }
}
