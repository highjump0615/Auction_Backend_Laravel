<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

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
}
