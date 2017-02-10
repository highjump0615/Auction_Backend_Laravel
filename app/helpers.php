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

/**
 * subtract two date
 * @param $date1
 * @param $date2
 * @return int minutes
 */
function dateDiffMin($date1, $date2) {
    // subtract 2 times
    $diffInterval = $date1->diff($date2);

    // convert DateInterval to minutes
    $diffMin = $diffInterval->days * 1440 + $diffInterval->h * 60 + $diffInterval->i;

    if ($date2 > $date1) {
        $diffMin = -$diffMin;
    }

    return $diffMin;

}