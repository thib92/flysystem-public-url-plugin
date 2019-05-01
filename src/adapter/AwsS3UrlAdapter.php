<?php


namespace Thib\FlysystemPublicUrlPlugin\adapter;


use Aws\S3\S3Client;

class AwsS3UrlAdapter implements PublicUrlAdapterInterface
{
    /** @var S3Client */
    private $s3Client;

    /** @var string */
    private $bucket;

    /**
     * AwsS3UrlAdapter constructor.
     * @param S3Client $s3Client
     * @param string $bucket
     */
    public function __construct(S3Client $s3Client, string $bucket)
    {
        $this->s3Client = $s3Client;
        $this->bucket = $bucket;
    }


    public function getPublicUrl(string $path): string
    {
        return $this->s3Client->getObjectUrl($this->bucket, $path);
    }
}