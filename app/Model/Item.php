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
     * Extended attributes
     *
     * @var array
     */
    protected $appends = [
        'username',
        'minute_remain',
        'maxbid'
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
        return $this->user->username;
    }

    /**
     * calculate remaining hours
     * @return mixed
     */
    public function getMinuteRemainAttribute() {
        $dateCurrent = new DateTime("now");
        $dateEnd = new DateTime($this->end_at);

        return dateDiffMin($dateEnd, $dateCurrent);
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
     * get user id of the max bid
     * @return int
     */
    public function getMaxbidAttribute() {
        $bids = $this->hasMany('App\Model\Bid')->withTrashed()->orderBy('price', 'desc')->limit(3)->get();

        return $bids;
    }

    /**
     * get bid of the selected user
     * @param User $user
     * @return mixed
     */
    public function getBidForUser(User $user)
    {
        return $this->hasMany('App\Model\Bid')->where('user_id', $user->id)->first();
    }

    /**
     * get winner user id
     * @return int
     */
    public function getWinnerId() {
        $winnerId = 0;

        // get max bid
        foreach ($this->maxbid as $bid) {
            // skip givenup bids
            if ($bid->giveup_at) {
                continue;
            }

            // skip deleted bids
            if ($bid->trashed()) {
                continue;
            }

            $winnerId = $bid->user_id;
            break;
        }

        return $winnerId;
    }
}
