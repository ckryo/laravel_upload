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
class UeditorUploadOSS extends UeditorUpload
{
    public function doUpload()
    {
        $file = $this->request->file($this->fileField);
        if (empty($file)) {
            $this->stateInfo = $this->getStateInfo("ERROR_FILE_NOT_FOUND");
            return false;
        }
        if (!$file->isValid()) {
            $this->stateInfo = $this->getStateInfo($file->getError());
            return false;

        }

        $this->file = $file;

        $this->oriName = $this->file->getClientOriginalName();

        $this->fileSize = $this->file->getSize();
        $this->fileType = $this->getFileExt();

        $this->fullName = $this->getFullName();


        $this->filePath = $this->getFilePath();

        $this->fileName = basename($this->filePath);

        try {
            OSS::upload(ltrim($this->fullName, '/'), $this->file->getPathname());
            $this->stateInfo = 'SUCCESS';
        } catch (OssException $e) {
            $this->stateInfo = $this->getStateInfo($e->getMessage());
        }
    }
}