<?php


namespace Thib\FlysystemPublicUrlPluginTest\Adapter;


use Aws\S3\S3Client;
use League\Flysystem\AwsS3v3\AwsS3Adapter;
use League\Flysystem\Filesystem;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Thib\FlysystemPublicUrlPlugin\Adapter\AwsS3UrlAdapter;

class AwsS3UrlAdapterTest extends TestCase
{
    public function testGetPublicUrl() {
        $s3ClientProphecy = $this->prophesize(S3Client::class);
        $s3ClientProphecy->getObjectUrl(Argument::cetera())->will(function($args) {
            return $args[0] . $args[1];
        });

        $adapter = new AwsS3Adapter($s3ClientProphecy->reveal(), "BUCKET");
        $filesystem = new Filesystem($adapter);

        $urlAdapter = new AwsS3UrlAdapter();

        $urlAdapter->setFilesystem($filesystem);

        $this->assertEquals("BUCKET/my/path", $urlAdapter->getPublicUrl("/my/path"));
        $s3ClientProphecy->getObjectUrl("BUCKET", "/my/path")->shouldHaveBeenCalledOnce();
    }
}