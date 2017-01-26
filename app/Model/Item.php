<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Item extends Model
{
    protected $table = 'plh_item';

    use softDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title',
        'desc',
        'category',
        'price',
        'condition',
        'status',
        'end_at',
        'user_id',
        'image0',
        'image1',
        'image2',
        'image3',
    ];

    /**
     * Txtended attributes
     *
     * @var array
     */
    protected $appends = [];

    /**
     * status
     */
    const STATUS_BID = 0;
    const STATUS_AUCTION = 1;
    const STATUS_CLOSED = 2;

    const MAX_IMAGE_NUM = 3;
}
