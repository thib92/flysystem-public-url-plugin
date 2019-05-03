<?php


namespace Thib\FlysystemPublicUrlPluginTest\Adapter;


use Aws\S3\S3Client;
use PHPUnit\Framework\TestCase;
use Thib\FlysystemPublicUrlPlugin\Adapter\AwsS3UrlAdapter;

class AwsS3UrlAdapterTest extends TestCase
{
    public function testGetPublicUrl() {
        $stubS3Client = $this->createMock(S3Client::class);
        $stubS3Client->method('getObjectUrl')->willReturn("BUCKET/my/path");

        $stubS3Client
            ->expects($this->once())
            ->method("getObjectUrl")
            ->with(
                $this->equalTo("BUCKET"),
                $this->equalTo("/my/path")
            );

        $adapter = new AwsS3UrlAdapter($stubS3Client, "BUCKET");

        $this->assertEquals("BUCKET/my/path", $adapter->getPublicUrl("/my/path"));
    }
}