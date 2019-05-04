<?php


namespace Thib\FlysystemPublicUrlPluginTest\Support;


use Thib\FlysystemPublicUrlPlugin\Adapter\AbstractPublicUrlAdapter;

class DummyPublicUrlAdapter extends AbstractPublicUrlAdapter
{

    public function getPublicUrl(string $path): string
    {
        return $path;
    }
}