<?php

namespace Ckryo\Laravel\Admin\Services;

class OSSi
{

    private $OSSiClient;
    private $bucket;

    public function __construct($isInternal = false)
    {
        $serverAddress = Config::get('APP_DEBUG', false) ? Config::get('filesystems.disks.OSSi.OSSiServerInternal') : Config::get('filesystems.disks.OSSi.OSSiServer');
        $this->OSSiClient = new AliyunOSS(
            $serverAddress,
            Config::get('filesystems.disks.OSSi.AccessKeyId'),
            Config::get('filesystems.disks.OSSi.AccessKeySecret')
        );
        $this->bucket = Config::get('filesystems.disks.OSSi.bucket');
        $this->OSSiClient->setBucket($this->bucket);
    }

    public static function upload($OSSiKey, $filePath)
    {
        $OSSi = new OSSi(); // 上传文件使用内网，免流量费
        return $OSSi->OSSiClient->uploadFile($OSSiKey, $filePath);
    }

    /**
     * 直接把变量内容上传到OSSi
     * @param $OSSikey
     * @param $content
     */
    public static function uploadContent($OSSikey, $content)
    {
        $OSSi = new OSSi(); // 上传文件使用内网，免流量费
        return $OSSi->OSSiClient->uploadContent($OSSikey, $content);
    }

    /**
     * 删除存储在OSSi中的文件
     *
     * @param string $OSSiKey 存储的key（文件路径和文件名）
     * @return
     */
    public static function deleteObject($OSSiKey)
    {
        $OSSi = new OSSi(); // 上传文件使用内网，免流量费
        return $OSSi->OSSiClient->deleteObject($OSSi->bucket, $OSSiKey);
    }

    /**
     * 复制存储在阿里云OSSi中的Object
     *
     * @param string $sourceBuckt 复制的源Bucket
     * @param string $sourceKey - 复制的的源Object的Key
     * @param string $destBucket - 复制的目的Bucket
     * @param string $destKey - 复制的目的Object的Key
     * @return Models\CopyObjectResult
     */
    public function copyObject($sourceBuckt, $sourceKey, $destBucket, $destKey)
    {
        $OSSi = new OSSi(); // 上传文件使用内网，免流量费

        return $OSSi->OSSiClient->copyObject($sourceBuckt, $sourceKey, $destBucket, $destKey);
    }

    /**
     * 移动存储在阿里云OSSi中的Object
     *
     * @param string $sourceBuckt 复制的源Bucket
     * @param string $sourceKey - 复制的的源Object的Key
     * @param string $destBucket - 复制的目的Bucket
     * @param string $destKey - 复制的目的Object的Key
     * @return Models\CopyObjectResult
     */
    public function moveObject($sourceBuckt, $sourceKey, $destBucket, $destKey)
    {
        $OSSi = new OSSi(); // 上传文件使用内网，免流量费

        return $OSSi->OSSiClient->moveObject($sourceBuckt, $sourceKey, $destBucket, $destKey);
    }

    public static function getUrl($OSSiKey)
    {
        $OSSi = new OSSi();
        return $OSSi->OSSiClient->getUrl($OSSiKey, new \DateTime("+1 day"));
    }

    public static function createBucket($bucketName)
    {
        $OSSi = new OSSi();
        return $OSSi->OSSiClient->createBucket($bucketName);
    }

    public static function getAllObjectKey()
    {
        $OSSi = new OSSi();
        return $OSSi->OSSiClient->getAllObjectKey($OSSi->bucket);
    }

    /**
     * 获取指定Object的元信息
     *
     * @param  string $bucketName 源Bucket名称
     * @param  string $key 存储的key（文件路径和文件名）
     * @return object 元信息
     */
    public static function getObjectMeta($bucketName, $OSSikey)
    {
        $OSSi = new OSSi();
        return $OSSi->OSSiClient->getObjectMeta($bucketName, $OSSikey);
    }

    public static function getAllObjectKeyWithUserOrg()
    {
        $userPath = 'org_' . (Auth::check() ? Auth::user()->id : 'custom');
        return self::getAllObjectKeyWithPrefix($userPath);
    }

    public static function getAllObjectKeyWithPrefix($folder_name)
    {
        $OSSi = new OSSi();
        return $OSSi->OSSiClient->listAllObjects($folder_name);
    }

    public static function getOrgAll()
    {
        $OSSi = new OSSi();
        $userPath = 'org_' . (Auth::check() ? Auth::user()->id : 'custom');
        return $OSSi->OSSiClient->listAllObjects($userPath);
    }

    public static function getOrgAllImg()
    {
        $OSSi = new OSSi();
        $userPath = 'org_' . (Auth::check() ? Auth::user()->id : 'custom') . '/image';
        return $OSSi->OSSiClient->listAllObjects($userPath);
    }

    public static function setUploadPath($ext, $is_user = 0)
    {
        $imgExt = [".png", ".jpg", ".jpeg", ".gif", ".bmp"];
        $fileExt = [".doc", ".docx", ".pdf"];
        $videoExt = [".flv", ".swf", ".mkv", ".avi", ".rm", ".rmvb", ".mpeg", ".mpg", ".ogg", ".ogv", ".mov", ".wmv", ".mp4", ".webm", ".mp3", ".wav", ".mid"];

        if (in_array($ext, $imgExt)) {
            $type = '/image/';
        } elseif (in_array($ext, $fileExt)) {
            $type = '/file/';
        } else {
            $type = '/video/';
        }

        $userPath = null;
        $orgPath = 'org_' . (Auth::check() ? Auth::user()->orgID() : 'custom');
        if ($is_user) {
            $userPath = 'user_' . (Auth::check() ? Auth::user()->id : 'custom');
        }
        $path = ($userPath ? $userPath : $orgPath) . $type . date('Ymd', time()) . '/' . time() . rand(100000, 999999) . $ext;

        return $path;
    }

}