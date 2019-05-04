<?php

namespace Thib\FlysystemPublicUrlPluginTest;

use Aws\S3\S3Client;
use Exception;
use League\Flysystem\Adapter\Local;
use League\Flysystem\AwsS3v3\AwsS3Adapter;
use League\Flysystem\Filesystem;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Thib\FlysystemPublicUrlPlugin\Adapter\LocalUrlAdapter;
use Thib\FlysystemPublicUrlPlugin\PublicUrlPlugin;
use Thib\FlysystemPublicUrlPluginTest\Support\DummyAdapter;
use Thib\FlysystemPublicUrlPluginTest\Support\DummyPublicUrlAdapter;

class PublicUrlPluginTest extends TestCase
{

    /**
     * @throws Exception
     */
    public function testAttachesToFilesystemInstance() {
        $adapter = new DummyAdapter();

        $filesystem = new Filesystem($adapter);
        $plugin = $this->createPlugin($filesystem);
        $filesystem->addPlugin($plugin);
        $plugin->addAdapter(DummyAdapter::class, DummyPublicUrlAdapter::class);

        $this->assertEquals($filesystem->getPublicUrl("/path/to/file"), "/path/to/file");
    }

    public function testCallsAwsS3Adapter() {
        $s3ClientProphecy = $this->prophesize(S3Client::class);
        // Method `getObjectUrl` on S3Client will return bucket and path concatenated
        $s3ClientProphecy->getObjectUrl(Argument::cetera())->will(function($args) {
            return $args[0] . $args[1];
        });

        $adapter = new AwsS3Adapter($s3ClientProphecy->reveal(), 'BUCKET');
        $filesystem = new Filesystem($adapter);
        $plugin = $this->createPlugin($filesystem);

        $this->assertEquals($plugin->handle("/path/to/file"), "BUCKET/path/to/file");
    }

    public function testCallsLocalUrlAdapter() {
        // Create a simple empty mock
        $adapter = $this->createMock(Local::class);
        $filesystem = new Filesystem($adapter);
        $plugin = $this->createPlugin($filesystem);

        $plugin->addAdapter(get_class($adapter), LocalUrlAdapter::class, ["/root/folder"]);

        $this->assertEquals($plugin->handle("/path/to/file"), "/root/folder/path/to/file");
    }

    public function testReturnsNullWithUnsupportedAdapter() {
        $filesystem = new Filesystem(new DummyAdapter());
        $plugin = $this->createPlugin($filesystem);

        $this->assertNull($plugin->handle("/path/to/file"));
    }

    private function createPlugin(?Filesystem $filesystem = null) {
        $plugin = new PublicUrlPlugin();
        if ($filesystem) {
            $plugin->setFilesystem($filesystem);
        }
        return $plugin;
    }
}