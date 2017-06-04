<?php

namespace Ckryo\Laravel\Upload\Controllers;

use Ckryo\Laravel\App\Http\Controllers\Controller;
use Illuminate\Http\Request;

// 用于文件、附件上传
class UeditorController extends Controller
{

    function index(Request $request)
    {
        $configPath = __DIR__ . '/../../config/ueditor.json';
        $config = json_decode(preg_replace("/\/\*[\s\S]+?\*\//", "", file_get_contents($configPath)), true);

        $action = $request->action;

        switch ($action) {
            case 'config':
                $result = $config;
                break;
//            case 'uploadimage':
//                $upConfig = array(
//                    "pathFormat" => $config['imagePathFormat'],
//                    "maxSize" => $config['imageMaxSize'],
//                    "allowFiles" => $config['imageAllowFiles'],
//                    'fieldName' => $config['imageFieldName'],
//                    'prefixUrl' => $config['imageUrlPrefix'],
//                );
//                $result = with(new UeditorUploadOSS($upConfig, $request))->upload();
//                break;
//            case 'uploadscrawl':
//                $upConfig = array(
//                    "pathFormat" => $config['scrawlPathFormat'],
//                    "maxSize" => $config['scrawlMaxSize'],
//                    //   "allowFiles" => $config['scrawlAllowFiles'],
//                    "oriName" => "scrawl.png",
//                    'fieldName' => $config['scrawlFieldName'],
//                    'prefixUrl' => $config['scrawlUrlPrefix'],
//                );
//                $result = with(new UeditorUploadScrawlOSS($upConfig, $request, 'base64'))->upload();
//
//                break;
//            case 'uploadvideo':
//                $upConfig = array(
//                    "pathFormat" => $config['videoPathFormat'],
//                    "maxSize" => $config['videoMaxSize'],
//                    "allowFiles" => $config['videoAllowFiles'],
//                    'fieldName' => $config['videoFieldName'],
//                    'prefixUrl' => $config['videoUrlPrefix'],
//                );
//                $result = with(new UeditorUploadOSS($upConfig, $request))->upload();
//
//                break;
//            case 'uploadfile':
//                $upConfig = array(
//                    "pathFormat" => $config['filePathFormat'],
//                    "maxSize" => $config['fileMaxSize'],
//                    "allowFiles" => $config['fileAllowFiles'],
//                    'fieldName' => $config['fileFieldName'],
//                    'prefixUrl' => $config['fileUrlPrefix'],
//                );
//                $result = with(new UeditorUploadOSS($upConfig, $request))->upload();
//                break;
//
//            /* 列出图片 */
//            case 'listimage':
//                $result = UeditorList::getOSSImage();
//                break;
//            /* 列出文件 */
//            case 'listfile':
//                $result = UeditorList::getOSSFile();
//                break;
//
//            /* 抓取远程文件 */
//            case 'catchimage':
//
//                $upConfig = array(
//                    "pathFormat" => $config['catcherPathFormat'],
//                    "maxSize" => $config['catcherMaxSize'],
//                    "allowFiles" => $config['catcherAllowFiles'],
//                    "oriName" => "remote.png",
//                    'fieldName' => $config['catcherFieldName'],
//                );
//                $sources = $request->get($upConfig['fieldName']);
//                $list = [];
//                foreach ($sources as $imgUrl) {
//                    $upConfig['imgUrl'] = $imgUrl;
//                    $info = with(new UeditorUploadCatchOSS($upConfig, $request))->upload();
//                    array_push($list, array(
//                        "state" => $info["state"],
//                        "url" => $info["url"],
//                        "size" => $info["size"],
//                        "title" => htmlspecialchars($info["title"]),
//                        "original" => htmlspecialchars($info["original"]),
//                        "source" => htmlspecialchars($imgUrl)
//                    ));
//                }
//                $result = [
//                    'state' => count($list) ? 'SUCCESS' : 'ERROR',
//                    'list' => $list
//                ];
//                break;
            default:
                $result = [
                    'state' => 'ERROR',
                ];
                break;
        }

        return response()->json($result, 200, [], JSON_UNESCAPED_UNICODE);

    }
}