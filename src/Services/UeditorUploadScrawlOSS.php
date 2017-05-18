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
class UeditorUploadScrawlOSS extends UeditorUpload
{
    public function doUpload()
    {

        $base64Data = $this->request->get($this->fileField);
        $img = base64_decode($base64Data);
        if (!$img) {
            $this->stateInfo = $this->getStateInfo("ERROR_FILE_NOT_FOUND");
            return false;
        }

        $this->oriName = $this->config['oriName'];

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