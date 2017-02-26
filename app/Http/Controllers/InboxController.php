<?php

namespace App\Http\Controllers;

use App\Model\Inbox;
use App\Model\Item;
use Illuminate\Http\Request;

use App\Http\Requests;

class InboxController extends Controller
{
    /**
     * get all inbox of the user
     * @param Request $request
     * @return mixed
     */
    public function getInbox(Request $request) {
        $user = $this->getCurrentUser();

        // query inbox
        $inboxes = Inbox::where('deleted_by', '!=', $user->id)
            ->where(function ($query) use ($user) {
                $query->where('user_id', $user->id);
                $query->orWhere('winner_id', $user->id);
            })
            ->get();

        // add item data to inbox
        $aryInbox = array();

        foreach ($inboxes as $inbox) {
            $inboxData = $inbox;
            $inboxData['item'] = $inbox->item;

            $aryInbox[] = $inboxData;
        }

        return $aryInbox;
    }

    /**
     * delete inbox item
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteInbox(Request $request) {
        $user = $this->getCurrentUser();
        $inboxId = $request->input('inbox');

        //
        // update target inbox
        //
        $inbox = Inbox::find($inboxId);

        // delete it, if already removed by other
        if ($inbox->deleted_by > 0) {
            $inbox->delete();
        }
        // first delete
        else {
            $inbox->deleted_by = $user->id;
            $inbox->save();
        }

        return response()->json();
    }
}
