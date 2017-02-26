<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Inbox extends Model
{
    protected $table = 'plh_inbox';

    use softDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'item_id',
        'winner_id',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'item_id',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    /**
     * get item data of the inbox
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    protected function item() {
        return $this->belongsTo('App\Model\Item');
    }
}
