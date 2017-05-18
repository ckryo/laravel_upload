<?php
namespace App\Http\Controllers\Vendor\src;

use App\Services\OSS;
use OSS\Core\OssException;

/**
 * Created by PhpStorm.
 * User: liurong
 * Date: 16/8/30
 * Time: 17:28
 */
class UeditorUploadCatchOSS extends UeditorUpload
{
    public function doUpload()
    {

        $imgUrl = strtolower(str_replace("&amp;", "&", $this->config['imgUrl']));
        //http开头验证
        if (strpos($imgUrl, "http") !== 0) {
            $this->stateInfo = $this->getStateInfo("ERROR_HTTP_LINK");
            return false;
        }
        //获取请求头并检测死链
        $heads = get_headers($imgUrl);

        if (!(stristr($heads[0], "200") && stristr($heads[0], "OK"))) {
            $this->stateInfo = $this->getStateInfo("ERROR_DEAD_LINK");
            return false;
        }

        //格式验证(扩展名验证和Content-Type验证)
        $fileType = strtolower(strrchr($imgUrl, '.'));
        if (!in_array($fileType, $this->config['allowFiles'])) {
            $this->stateInfo = $this->getStateInfo("ERROR_HTTP_CONTENTTYPE");
            return false;
        }

        //打开输出缓冲区并获取远程图片
        ob_start();
        $context = stream_context_create(
            array('http' => array(
                'follow_location' => false // don't follow redirects
            ))
        );
        readfile($imgUrl, false, $context);
        $img = ob_get_contents();

        ob_end_clean();

        preg_match("/[\/]([^\/]*)[\.]?[^\.\/]*$/", $imgUrl, $m);


        $this->oriName = $m ? $m[1] : "";
        $this->fileSize = strlen($img);
        $this->fileType = $this->getFileExt();
        $this->fullName = $this->getFullName();
        $this->filePath = $this->getFilePath();
        $this->fileName = basename($this->filePath);

        //检查文件大小是否超出限制
        if (!$this->checkSize()) {
            $this->stateInfo = $this->getStateInfo("ERROR_SIZE_EXCEED");
            return false;
        }

        try {
            OSS::uploadContent(ltrim($this->fullName, '/'), $img);
            $this->stateInfo = 'SUCCESS';
        } catch (OssException $e) {
            $this->stateInfo = $this->getStateInfo($e->getMessage());
        }
    }

    /**
     * 获取文件扩展名
     * @return string
     */
    protected function getFileExt()
    {
        return strtolower(strrchr($this->oriName, '.'));
    }
}