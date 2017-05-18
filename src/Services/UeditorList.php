<?php
/**
 * Created by PhpStorm.
 * User: liurong
 * Date: 16/8/31
 * Time: 17:38
 */

namespace App\Http\Controllers\Vendor\src;


use Illuminate\Http\Request;
use App\Services\OSS;
use OSS\Core\OssException;
use Illuminate\Support\Facades\Input;

class UeditorList
{
    public static function getOSSImage()
    {
        try {
            $files = [];
            $start = Input::get('start', 0);
            $size = Input::get('size', 20);
            $objs = OSS::getOrgAllImg();
            foreach (arrayTake($objs, $start, $size) as $item) {
                $obj = new \stdClass();
                $obj->url = $item->getKey();
                $files[] = $obj;
            }
            $result = new \stdClass();
            $result->state = "SUCCESS";
            $result->list = $files;
            $result->start = $start;
            $result->total = count($objs);
            return $result;
        } catch (OssException $e) {
            $result = new \stdClass();
            $result->state = $e->getErrorMessage();
            return $result;
        }
    }

    public static function getOSSFile()
    {
        try {
            $files = [];
            $start = Input::get('start', 0);
            $size = Input::get('size', 20);
            $objs = OSS::getOrgAllImg();
            foreach (arrayTake($objs, $start, $size) as $item) {
                $obj = new \stdClass();
                $obj->url = $item->getKey();
                $files[] = $obj;
            }
            $result = new \stdClass();
            $result->state = "SUCCESS";
            $result->list = $files;
            $result->start = $start;
            $result->total = count($objs);
            return $result;
        } catch (OssException $e) {
            $result = new \stdClass();
            $result->state = $e->getErrorMessage();
            return $result;
        }
    }
}