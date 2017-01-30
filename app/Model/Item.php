<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use DateTime;

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
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'user',
    ];

    /**
     * Txtended attributes
     *
     * @var array
     */
    protected $appends = [
        'username',
        'minute_remain'
    ];

    /**
     * status
     */
    const STATUS_BID = 0;
    const STATUS_AUCTION = 1;
    const STATUS_CLOSED = 2;

    const MAX_IMAGE_NUM = 3;

    /**
     * get user data of the item
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
        return $this->user->name;
    }

    /**
     * calculate remaining hours
     * @return mixed
     */
    public function getMinuteRemainAttribute() {
        $dateCurrent = new DateTime("now");
        $dateEnd = new DateTime($this->end_at);

        // subtract 2 times
        $diffInterval = $dateEnd->diff($dateCurrent);

        // convert DateInterval to minutes
        $diffMin = $diffInterval->days * 1440 + $diffInterval->h * 60 + $diffInterval->i;

        return $diffMin;
    }

    /**
     * get all bids to this item
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function bids()
    {
        return $this->hasMany('App\Model\Bid');
    }

    /**
     * get max bid price to this item
     * @return mixed
     */
    public function getMaxBid() {
        return (int)$this->bids->max('price');
    }
}
