<?php


namespace Thib\FlysystemPublicUrlPluginTest\Adapter;


use PHPUnit\Framework\TestCase;
use Thib\FlysystemPublicUrlPlugin\Adapter\LocalUrlAdapter;

class LocalUrlAdapterTest extends TestCase
{
    public function testCorrectConcatenation() {
        $adapter = new LocalUrlAdapter("/root/folder/");
        $this->assertEquals("/root/folder/test.png", $adapter->getPublicUrl("/test.png"));
        $this->assertEquals("/root/folder/test.png", $adapter->getPublicUrl("test.png"));

        $adapter = new LocalUrlAdapter("/root/folder");
        $this->assertEquals("/root/folder/test.png", $adapter->getPublicUrl("/test.png"));
        $this->assertEquals("/root/folder/test.png", $adapter->getPublicUrl("test.png"));
    }
}