<?php


namespace Thib\FlysystemPublicUrlPlugin\Adapter;

use Exception;
use League\Flysystem\AwsS3v3\AwsS3Adapter;

class AwsS3UrlAdapter extends AbstractPublicUrlAdapter
{
    /**
     * @param string $path
     * @return string
     * @throws Exception
     */
    public function getPublicUrl(string $path): string
    {
        $adapter = $this->filesystem->getAdapter();
        if (!$adapter instanceof AwsS3Adapter) {
            throw new Exception("Filesystem adapter is not an instance of AwsS3Adapter");
        }

        $s3Client = $adapter->getClient();
        $bucket = $adapter->getBucket();

        return $s3Client->getObjectUrl($bucket, $path);
    }
}