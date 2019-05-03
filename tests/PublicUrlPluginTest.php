<?php

namespace Thib\FlysystemPublicUrlPluginTest;

use Aws\S3\S3Client;
use League\Flysystem\AwsS3v3\AwsS3Adapter;
use League\Flysystem\Filesystem;
use PHPUnit\Framework\TestCase;
use Thib\FlysystemPublicUrlPlugin\PublicUrlPlugin;
use Thib\FlysystemPublicUrlPluginTest\Support\UnsupportedAdapter;

class PublicUrlPluginTest extends TestCase
{

    public function testAttachesToFilesystemInstance() {
        $stubAdapter = $this->createStubAwsS3Adapter();
        $filesystem = new Filesystem($stubAdapter);

        $plugin = $this->createPlugin();
        $filesystem->addPlugin($plugin);

        $this->assertEquals($filesystem->getPublicUrl("/path/to/file"), "/path/to/file");
    }

    public function testCallsAwsS3Adapter() {
        $stubAdapter = $this->createStubAwsS3Adapter();
        $filesystem = new Filesystem($stubAdapter);

        $plugin = $this->createPlugin($filesystem);

        $this->assertEquals($plugin->handle("/path/to/file"), "/path/to/file");
    }

    public function testReturnsNullWithUnsupportedAdapter() {
        $filesystem = new Filesystem(new UnsupportedAdapter());
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

    private function createStubAwsS3Adapter(): AwsS3Adapter {
        $stubAdapter = $this->createMock(AwsS3Adapter::class);

        $stubS3Client = $this->createMock(S3Client::class);
        // The S3Client will return the path of the object
        $stubS3Client->method('getObjectUrl')->willReturnArgument(1);

        $stubAdapter->method('getClient')->willReturn($stubS3Client);
        $stubAdapter->method('getBucket')->willReturn("BUCKET");

        return $stubAdapter;
    }
}