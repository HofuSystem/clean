<?php

namespace Core\Comments\Observers;

use Core\Comments\Models\Comment;

class CommentObserver
{
    /**
     * Handle the Comment "creating" event.
     *
     * @param  \Core\Comments\Models\Comment  $comment
     * @return void
     */
    public function creating(Comment $comment)
    {
    
    }
    /**
     * Handle the Comment "created" event.
     *
     * @param  \Core\Comments\Models\Comment  $comment
     * @return void
     */
    public function created(Comment $comment)
    {
    
    }

    /**
     * Handle the Comment "updating" event.
     *
     * @param  \Core\Comments\Models\Comment  $comment
     * @return void
     */
    public function updating(Comment $comment)
    {

    }
    /**
     * Handle the Comment "updated" event.
     *
     * @param  \Core\Comments\Models\Comment  $comment
     * @return void
     */
    public function updated(Comment $comment)
    {

    }
    /**
     * Handle the Comment "saving" event.
     *
     * @param  \Core\Comments\Models\Comment  $comment
     * @return void
     */
    public function saving(Comment $comment)
    {

    }
    /**
     * Handle the Comment "saved" event.
     *
     * @param  \Core\Comments\Models\Comment  $comment
     * @return void
     */
    public function saved(Comment $comment)
    {

    }

    /**
     * Handle the Comment "deleted" event.
     *
     * @param  \Core\Comments\Models\Comment  $comment
     * @return void
     */
    public function deleted(Comment $comment)
    {
      
    }

    /**
     * Handle the Comment "restored" event.
     *
     * @param  \Core\Comments\Models\Comment  $comment
     * @return void
     */
    public function restored(Comment $comment)
    {
        //
    }

    /**
     * Handle the Comment "force deleted" event.
     *
     * @param  \Core\Comments\Models\Comment  $comment
     * @return void
     */
    public function forceDeleted(Comment $comment)
    {
        //
    }
}
