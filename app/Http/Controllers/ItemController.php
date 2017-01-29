<?php

namespace App\Http\Controllers;

use App\Model\Item;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

use DateTime;
use DateInterval;
use File;

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

        // get max id of item
        $nMaxId = Item::where('status', Item::STATUS_BID)->max('id');

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
            ->where('status', Item::STATUS_BID)
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
}
