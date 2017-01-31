<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Auth\AuthController;
use App\Model\Bid;
use App\Model\Item;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

use Validator;
use File;
use Auth;

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

    /**
     * Get a validator for an incoming save profile request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validatorProfile(array $data)
    {
        $user = $this->getCurrentUser();

        //
        // add except current user
        //
        return Validator::make($data, [
            'name' => 'required|max:255',
            'username' => 'required|max:255|unique:' . \CreateUserTable::$tableName . ',username,' . $user->id,
        ]);
    }

    /**
     * Get a validator for an incoming save setting request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validatorEmail(array $data)
    {
        $user = $this->getCurrentUser();

        //
        // add except current user
        //
        return Validator::make($data, [
            'email' => 'required|max:255|unique:' . \CreateUserTable::$tableName . ',email,' . $user->id,
        ]);
    }

    /**
     * save photo image file
     * @param $file
     * @return string new saved file name
     */
    public function savePhotofile($file) {
        // create user photo directory, if not exist
        if (!file_exists(getUserPhotoPath())) {
            File::makeDirectory(getUserPhotoPath(), 0777, true);
        }

        // generate file name u**********.ext
        $strName = 'u' . time() . uniqid() . '.' . $file->getClientOriginalExtension();

        // move file to upload folder
        $file->move(getUserPhotoPath(), $strName);

        // return new file name
        return $strName;
    }

    /**
     * save profile
     * @param Request $request
     * @return JsonResponse|mixed
     */
    public function saveProfile(Request $request) {

        $validator = $this->validatorProfile($request->all());

        // failed validation
        if ($validator->fails()) {
            $error = json_decode($validator->errors());
            return response()->json($error, softFailStatus());
        }

        $user = $this->getCurrentUser();

        $user->name = $request->input('name');
        $user->username = $request->input('username');
        $user->gender = $request->input('gender');

        //
        // check existance
        //
        if ($request->has('birthday')) {
            $user->birthday = $request->input('birthday');
        }

        // if photo file exists, save file first
        if ($request->hasFile('photo')) {
            $filePhoto = $request->file('photo');

            // save file
            $user->photo = $this->savePhotofile($filePhoto);
        }

        $user->save();

        return $user;
    }

    /**
     * save email & password
     * @param Request $request
     * @return JsonResponse|mixed
     */
    public function saveSetting(Request $request) {

        $validator = $this->validatorEmail($request->all());

        // failed validation
        if ($validator->fails()) {
            $error = json_decode($validator->errors());
            return response()->json($error, softFailStatus());
        }

        $user = $this->getCurrentUser();

        $user->email = $request->input('email');

        //
        // check existance
        //
        if ($request->has('password')) {
            $oldpswd = $request->input('oldpassword');

            $credentials = array(
                'username' => $user->username,
                'password' => $oldpswd,
            );

            if (!Auth::guard('web')->attempt($credentials)) {
                // old password is not correct
                return new JsonResponse(array('message' => 'Old password does not match'), softFailStatus());
            }

            // update password
            $user->password = bcrypt($request->input('password'));
        }

        $user->save();

        return $user;
    }
}
