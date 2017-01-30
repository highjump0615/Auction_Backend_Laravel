<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $table = 'plh_comment';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'comment',
        'parent_id',
        'user_id',
        'item_id',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'user',
    ];

    /**
     * Extended attributes
     *
     * @var array
     */
    protected $appends = [
        'username',
    ];

    /**
     * get user data of the comment
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    protected function user(){
        return $this->belongsTo('App\Model\User');
    }

    /**
     * username
     * @return mixed
     */
    public function getUsernameAttribute() {
        return $this->user->username;
    }
}
