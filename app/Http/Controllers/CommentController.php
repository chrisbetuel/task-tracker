<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Project;
use Illuminate\Http\Request;

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

        return redirect()->back()->with('success', 'Comment added successfully.');
    }
}
