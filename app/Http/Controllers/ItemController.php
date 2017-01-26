<?php

namespace App\Http\Controllers;

use App\Model\Item;
use Illuminate\Http\Request;

use DateTime;
use DateInterval;
use File;

class ItemController extends Controller
{
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
}
