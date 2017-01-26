<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 1/25/17
 * Time: 10:42 AM
 */

/**
 * status for logic fail
 * @return int
 */
function softFailStatus() {
    return 400;
}

/**
 * get user photo image path
 * @return string
 */
function getUserPhotoPath() {
    return public_path('uploads/user/');
}

/**
 * get item photo image path
 * @return string
 */
function getItemImagePath() {
    return public_path('uploads/item/');
}