<?php

namespace App\Http\Controllers;

use App\Model\Comment;
use App\Model\Item;
use App\Model\Bid;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

use DateTime;
use DateInterval;
use File;
use Psy\Util\Json;

class ItemController extends Controller
{
    /**
     * upload a new item
     * @param Request $request
     * @return item
     */
    public function upload(Request $request) {

        $user = $this->getCurrentUser();

        // calculate the end time from period
        $dateEnd = new DateTime();
        $dateEnd->add(new DateInterval('P' . $request->input('period') . 'D'));
        $strDate = $dateEnd->format('Y-m-d H:i:s');

        $aryParam = [
            'title'     => $request->input('title'),
            'desc'      => $request->input('desc'),
            'category'  => $request->input('category'),
            'price'     => $request->input('price'),
            'condition' => $request->input('condition'),
            'status'    => Item::STATUS_BID,
            'end_at'    => $strDate,
            'user_id'   => $user->id,
        ];

        // add images
        for ($i = 0; $i <= Item::MAX_IMAGE_NUM; $i++) {
            $strParam = 'image' . $i;

            if ($request->hasFile($strParam)) {
                $fileImage = $request->file($strParam);
                if ($fileImage->isValid()) {

                    // create user photo directory, if not exist
                    if (!file_exists(getItemImagePath())) {
                        File::makeDirectory(getItemImagePath(), 0777, true);
                    }

                    // generate file name i**********.ext
                    $strName = 'i' . time() . uniqid() . '.' . $fileImage->getClientOriginalExtension();

                    // move file to upload folder
                    $fileImage->move(getItemImagePath(), $strName);

                    // add new file name to param
                    $aryParam[$strParam] = $strName;
                }
            }
        }

        // create new item
        $itemNew = Item::create($aryParam);

        return $itemNew;
    }

    /**
     * explore api; get random 10 items
     *
     * @param Request $request
     * @return mixed
     */
    public function getExplore(Request $request) {
        $nMaxCount = 10;

        $dateCurrent = new DateTime();

        // get max id of item
        $nMaxId = Item::where('end_at', '>', $dateCurrent)->max('id');

        // if no data
        if (empty($nMaxId)) {
            return new JsonResponse();
        }

        // sort randomly
        $aryId = array();
        for ($i = 1; $i <= $nMaxId; $i++) {
            array_push($aryId, $i);
        }
        shuffle($aryId);

        // order by condition
        $strIdList = '';
        $nCount = min(count($aryId), $nMaxCount);

        for ($i = 0; $i < $nCount; $i++) {
            $strIdList .= $aryId[$i];
            if ($i < $nCount - 1) {
                $strIdList .= ', ';
            }
        }

        // query first 10 item with id array above
        $items = Item::whereIn('id', $aryId)
            ->where('end_at', '>', $dateCurrent)
            ->orderByRaw('FIELD(id, ' . $strIdList . ')')
            ->limit($nMaxCount)
            ->get();

        return $items;
    }

    /**
     * category api; get items in the category
     * @param Request $request
     * @param $id
     * @return mixed
     */
    public function getCategory(Request $request, $id) {
        // query all items of category
        $items = Item::where('category', $id)->get();

        return $items;
    }

    /**
     * get items with keyword in title
     * @param Request $request
     * @param $keyword
     * @return mixed
     */
    public function getSearch(Request $request, $keyword) {
        // query items with keyword in title
        $items = Item::where('title', 'like', '%' . $keyword . '%')->get();

        return $items;
    }

    /**
     * place a new bid to the item
     * @param Request $request
     * @return mixed
     */
    public function placeBid(Request $request) {
        $user = $this->getCurrentUser();

        // todo: check bid possibility of the item using remaintime

        $aryParam = [
            'price'     => $request->input('price'),
            'item_id'   => $request->input('item'),
            'user_id'   => $user->id,
        ];

        // create new bid
        $bidNew = Bid::create($aryParam);

        return $bidNew;
    }

    /**
     * get max bid price of the item
     * @param Request $request
     * @param $id
     * @return mixed
     */
    public function getMaxBidPrice(Request $request, $id) {
        return Item::find($id)->maxbid;
    }

    /**
     * add new comment
     * @param Request $request
     * @return Comment
     */
    public function addComment(Request $request) {
        $user = $this->getCurrentUser();

        $aryParam = [
            'comment'   => $request->input('comment'),
            'parent_id' => $request->input('parent'),
            'user_id'   => $user->id,
            'item_id'   => $request->input('item'),
        ];

        // create new bid
        $commentNew = Comment::create($aryParam);

        return $commentNew;
    }

    /**
     * get all comments of the item
     * @param Request $request
     * @return mixed
     */
    public function getComment(Request $request) {
        $itemId = $request->input('item');

        // query comments
        $comments = Comment::where('item_id', $itemId)->get();

        // sort according to parent relation
        $aryComment = array();

        foreach ($comments as $cmt) {
            if ($cmt->parent_id > 0) {
                for ($i = 0; $i < count($aryComment); $i++) {
                    $cmtP = $aryComment[$i];

                    // insert element next to the parent comment
                    if ($cmt->parent_id == $cmtP->id) {
                        array_splice($aryComment, $i + 1, 0, array($cmt));
                        break;
                    }
                }
            }
            else {
                $aryComment[] = $cmt;
            }
        }


        return $aryComment;
    }

    /**
     * contact for item
     * @param Request $request
     * @return json
     */
    public function contact(Request $request) {
        $user = $this->getCurrentUser();
        $itemId = $request->input('item');

        //
        // update target item
        //
        $item = Item::find($itemId);

        // accepting contact
        if ($item->contact > 0) {
            $item->contact = -1;

            // todo: add inbox record
        }
        // first contact
        else if ($item->contact == 0) {
            $item->contact = $user->id;
        }

        $item->save();

        return response()->json([
            'contact' => $item->contact,
        ]);
    }

    /**
     * give up bid for item
     * @param Request $request
     * @return JsonResponse
     */
    public function giveupBid(Request $request) {
        $user = $this->getCurrentUser();

        $itemId = $request->input('item');
        $item = Item::find($itemId);

        // get current time
        $dateNow = new DateTime();
        $strDate = $dateNow->format('Y-m-d H:i:s');

        //
        // update bid data
        //
        $bid = $item->getBidForUser($user);
        if ($bid) {
            $bid->giveup_at = $strDate;
            $bid->save();
        }

        return response()->json();
    }

    /**
     * delete bid for item
     * @param Request $request
     * @return JsonResponse
     */
    public function deleteBid(Request $request) {
        $user = $this->getCurrentUser();
        $itemId = $request->input('item');
        $item = Item::find($itemId);

        //
        // delete bid data
        //
        $bid = $item->getBidForUser($user);
        $bid->delete();

        return response()->json();
    }
}
