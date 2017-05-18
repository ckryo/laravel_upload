<?php
namespace Ckryo\Laravel\Upload\Services;

use JohnLui\AliyunOSS;
use Exception;
use DateTime;

class OSS {

    private $config = [];
    private $ossClient;
    /**
     * 私有初始化 API，非 API，不用关注
     * @param boolean 是否使用内网
     */
    public function __construct($isInternal = false)
    {
        $this->config = config('filesystems.disks.oss');
        if ($this->config['networkType'] == 'VPC' && !$isInternal) {
            throw new Exception("VPC 网络下不提供外网上传、下载等功能");
        }

        $this->ossClient = AliyunOSS::boot(
            $this->config['city'],
            $this->config['networkType'],
            $isInternal,
            $this->config['accessKeyId'],
            $this->config['accessKeySecret']
        );
    }


    /**
     * 文件上传
     *
     * @param $bucketName
     * @param $ossKey
     * @param $filePath
     * @param array $options
     * @return string url
     */
    public static function upload($bucketName, $ossKey, $filePath, $options = []) {
        $oss = new OSS();

        $bucketName = strtolower($bucketName);
        $bucket = $oss->config['buckets'][$bucketName];
        $oss->ossClient->setBucket($bucket['name']);

        $oss->ossClient->uploadFile($ossKey, $filePath, $options);

        return '//'.$bucket['domain'].'/'.$ossKey;
    }

    public static function uploadContent($bucketName, $ossKey, $content, $options = []) {
        $oss = new OSS();

        $bucketName = strtolower($bucketName);
        $bucket = $oss->config['buckets'][$bucketName];
        $oss->ossClient->setBucket($bucket['name']);

        $oss->ossClient->uploadContent($ossKey, $content, $options);

        return '//'.$bucket['domain'].'/'.$ossKey;
    }

    public static function listAll ($bucketName, $path) {
        $oss = new OSS();
        $bucket = $oss->config['buckets'][$bucketName];

        return array_map(function ($item) use ($bucket) {
            $url = '//'.$bucket['domain'].'/'.$item;
            return [
                'url' => $url,
                'name' => $url,
                'preview' => $url.'/100x100'
            ];
        }, $oss->ossClient->getAllObjectKeyWithPrefix($bucket['name'], $path));
    }

    /**
     * 使用阿里云内网删除文件
     * @param  string bucket名称
     * @param  string 目标 OSS object 名称
     * @return boolean 删除是否成功
     */
    public static function privateDeleteObject($bucketName, $ossKey)
    {
        $oss = new OSS(true);
        $oss->ossClient->setBucket($bucketName);
        return $oss->ossClient->deleteObject($bucketName, $ossKey);
    }
    /**
     * -------------------------------------------------
     *
     *
     *  下面不再分公网内网出 API，也不注释了，大家自行体会吧。。。
     *
     *
     * -------------------------------------------------
     */
    public function copyObject($sourceBuckt, $sourceKey, $destBucket, $destKey)
    {
        $oss = new OSS();
        return $oss->ossClient->copyObject($sourceBuckt, $sourceKey, $destBucket, $destKey);
    }
    public function moveObject($sourceBuckt, $sourceKey, $destBucket, $destKey)
    {
        $oss = new OSS();
        return $oss->ossClient->moveObject($sourceBuckt, $sourceKey, $destBucket, $destKey);
    }
    // 获取公开文件的 URL
    public static function getPublicObjectURL($bucketName, $ossKey)
    {
        $oss = new OSS();
        $oss->ossClient->setBucket($bucketName);
        return $oss->ossClient->getPublicUrl($ossKey);
    }
    // 获取私有文件的URL，并设定过期时间，如 \DateTime('+1 day')
    public static function getPrivateObjectURLWithExpireTime($bucketName, $ossKey, DateTime $expire_time)
    {
        $oss = new OSS();
        $oss->ossClient->setBucket($bucketName);
        return $oss->ossClient->getUrl($ossKey, $expire_time);
    }
    public static function createBucket($bucketName)
    {
        $oss = new OSS();
        return $oss->ossClient->createBucket($bucketName);
    }
    public static function getAllObjectKey($bucketName)
    {
        $oss = new OSS();
        return $oss->ossClient->getAllObjectKey($bucketName);
    }
    public static function getObjectMeta($bucketName, $ossKey)
    {
        $oss = new OSS();
        return $oss->ossClient->getObjectMeta($bucketName, $ossKey);
    }
}