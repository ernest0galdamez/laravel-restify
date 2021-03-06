<?php

namespace Binaryk\LaravelRestify\Tests\Fixtures\Post;

use Binaryk\LaravelRestify\Contracts\RestifySearchable;
use Binaryk\LaravelRestify\Tests\Fixtures\User\User;
use Binaryk\LaravelRestify\Traits\InteractWithSearch;
use Illuminate\Database\Eloquent\Model;

/**
 * @author Eduard Lupacescu <eduard.lupacescu@binarcode.com>
 */
class Post extends Model implements RestifySearchable
{
    use InteractWithSearch;

    protected $fillable = [
        'id',
        'user_id',
        'image',
        'title',
        'description',
        'category',
        'is_active',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
