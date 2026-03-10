<?php

namespace Core\Comments\Services;

use Core\Comments\Models\Comment;



class CommentingService
{
    public function __construct(){}

    public function comment(string $class,int $id,string $content,int $user_id,int | null $parent_id){
        return Comment::create([
         'comment_for_type' => $class,
         'comment_for_id'   => $id,
         'content'          => $content,
         'parent_id'        => $parent_id,
         'user_id'          => $user_id,
        ]);
     }
}
