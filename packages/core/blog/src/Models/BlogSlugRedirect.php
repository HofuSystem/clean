<?php

namespace Core\Blog\Models;

use Illuminate\Database\Eloquent\Model;

class BlogSlugRedirect extends Model
{
    protected $table = 'blog_slug_redirects';

    protected $fillable = ['old_slug', 'new_slug'];
}
