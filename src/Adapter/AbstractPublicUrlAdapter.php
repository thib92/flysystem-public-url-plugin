<?php


namespace Thib\FlysystemPublicUrlPlugin\Adapter;


use League\Flysystem\Filesystem;

abstract class AbstractPublicUrlAdapter
{
    /** @var Filesystem */
    protected $filesystem;

    public abstract function getPublicUrl(string $path): string;

    public function setFilesystem(Filesystem $filesystem) {
        $this->filesystem = $filesystem;
    }
}