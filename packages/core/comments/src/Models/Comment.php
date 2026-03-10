<?php

namespace Core\Comments\Models;

use App\Observers\GlobalModelObserver;
use Core\Comments\Observers\CommentObserver;
use Core\Users\Models\User;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;

use Illuminate\Database\Eloquent\Model;
#[ObservedBy([CommentObserver::class])]
#[ObservedBy([GlobalModelObserver::class])]
class Comment extends Model
{
    protected $table             = 'comments';
    protected $fillable          = ['comment_for_type', 'comment_for_id', 'content', 'user_id','parent_id'];

    public function user(){
        return $this->belongsTo(User::class,'user_id','id');
    } 
    public function parent(){
        return $this->belongsTo(Comment::class,'parent_id','id');
    } 
    public function subComments(){
        return $this->hasMany(Comment::class,'parent_id','id');
    } 
   
}
