<?php

namespace Thib\FlysystemPublicUrlPlugin;

use League\Flysystem\AdapterInterface;
use League\Flysystem\AwsS3v3\AwsS3Adapter;
use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemInterface;
use League\Flysystem\PluginInterface;
use Thib\FlysystemPublicUrlPlugin\Adapter\AwsS3UrlAdapter;
use Thib\FlysystemPublicUrlPlugin\Adapter\PublicUrlAdapterInterface;

class PublicUrlPlugin implements PluginInterface
{

    /** @var FilesystemInterface */
    private $filesystem;

    /**
     * Get the method name.
     *
     * @return string
     */
    public function getMethod()
    {
        return 'getPublicUrl';
    }

    /**
     * Set the Filesystem object.
     *
     * @param FilesystemInterface $filesystem
     */
    public function setFilesystem(FilesystemInterface $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    public function handle(string $path) {
        if (!$this->filesystem instanceof Filesystem) {
            return null;
        }

        $adapter = $this->filesystem->getAdapter();

        $urlAdapter = $this->resolveUrlAdapter($adapter);

        if ($urlAdapter === null) {
            return null;
        }

        return $urlAdapter->getPublicUrl($path);
    }

    /**
     * @param AdapterInterface $adapter
     * @return PublicUrlAdapterInterface|null
     */
    private function resolveUrlAdapter(AdapterInterface $adapter): ?PublicUrlAdapterInterface {

        if ($adapter instanceof AwsS3Adapter) {
            return new AwsS3UrlAdapter($adapter->getClient(), $adapter->getBucket());
        }

        return null;
    }
}