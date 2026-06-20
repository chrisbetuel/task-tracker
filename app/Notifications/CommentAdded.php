<?php

namespace App\Notifications;

use App\Models\Comment;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class CommentAdded extends Notification
{
    use Queueable;

    public Comment $comment;

    public function __construct(Comment $comment)
    {
        $this->comment = $comment;
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $user = $this->comment->user;
        $project = $this->comment->project;

        return [
            'comment_id' => $this->comment->id,
            'comment_body' => str($this->comment->body)->limit(200)->toString(),
            'user_id' => $user->id,
            'user_name' => $user->name,
            'project_id' => $project->id,
            'project_name' => $project->name,
        ];
    }
}
