<?php

namespace App\Http\Controllers;

use App\Model\Bid;
use App\Model\Item;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

use App\Http\Requests;
use Psy\Util\Json;

class UserController extends Controller
{
    /**
     * get current user
     * @param Request $request
     * @return mixed
     */
    public function getUser(Request $request) {
        return $this->getCurrentUser();
    }

    /**
     * get Auctions, Bids and other parameters of the user
     * @param Request $request
     * @return mixed
     */
    public function getUserInfo(Request $request) {
        $user = $this->getCurrentUser();

        // query items of the user
        $itemsMine = Item::where('user_id', $user->id)->get();

        // get items user has bid
        $itemIdsBid = Bid::where('user_id', $user->id)->distinct()->get(['item_id']);

        $aryId = array();
        foreach ($itemIdsBid as $bid) {
            $aryId[] = $bid->item_id;
        }

        // query items based on id
        $itemsBid = Item::whereIn('id', $aryId)->get();

        // put together
        $userInfo = array(
            'auctions' => $itemsMine,
            'bids' => $itemsBid,
        );

        return new JsonResponse($userInfo);
    }
}
