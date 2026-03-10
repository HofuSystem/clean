<?php

namespace Core\MediaCenter\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Core\Users\Models\User;

class Media extends Model
{
    protected $appends = ['url','src'];

    protected $table='media_center';
    protected $primaryKey = 'id';
    protected $guarded=[];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function getUrlAttribute(){
        return url('storage/'.$this->file_name) ;
    }
    public function getSrcAttribute(){
        return url('storage/'.$this->file_name) ;
    }


    public function scopeUser($query, $id=null) {
        $id = $id ?? request()->user()->id ?? request()->user('sanctum')->id;
        if($id) return $query->where('user_id', $id);
        return $query;
    }
}
