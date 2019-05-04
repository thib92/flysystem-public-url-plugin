<?php

namespace Thib\FlysystemPublicUrlPlugin;

use Exception;
use League\Flysystem\Adapter\Local;
use League\Flysystem\AdapterInterface;
use League\Flysystem\AwsS3v3\AwsS3Adapter;
use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemInterface;
use League\Flysystem\PluginInterface;
use Thib\FlysystemPublicUrlPlugin\Adapter\AbstractPublicUrlAdapter;
use Thib\FlysystemPublicUrlPlugin\Adapter\AwsS3UrlAdapter;
use Thib\FlysystemPublicUrlPlugin\Adapter\LocalUrlAdapter;

class PublicUrlPlugin implements PluginInterface
{

    /** @var FilesystemInterface */
    private $filesystem;

    /**
     * @var array Map from a Flysystem Adapter to a PublicUrlAdapter
     * @see AdapterInterface
     * @see AbstractPublicUrlAdapter
     */
    private $adapters = [
        AwsS3Adapter::class => AwsS3UrlAdapter::class,
        Local::class => LocalUrlAdapter::class
    ];

    /**
     * Constructor parameters for PublicUrlAdapter
     * Map from the PublicUrlAdapter to a sequential array of constructor parameters
     * @var array
     * @see PublicUrlPlugin::setParam()
     */
    private $params;

    /**
     * PublicUrlPlugin constructor.
     * @param array $params
     * @see $params
     */
    public function __construct(array $params = [])
    {
        $this->params = $params;
    }

    /**
     * @param string $adapterClass
     * @param string $publicUrlAdapterClass
     * @param array $params
     * @throws Exception
     */
    public function addAdapter(string $adapterClass, string $publicUrlAdapterClass, $params = []) {
        if (!class_exists($adapterClass)) {
            throw new Exception("$adapterClass class does not exist");
        }

        if (!class_exists($publicUrlAdapterClass)) {
            throw new Exception("$publicUrlAdapterClass class does not exist");
        }

        // Set the params first to have the Exception thrown if $params is invalid *before* adding the adapter
        $this->setParam($publicUrlAdapterClass, $params);
        $this->adapters[$adapterClass] = $publicUrlAdapterClass;
    }

    /**
     * Set the constructor params for a given PublicUrlAdapter
     * @param string $publicUrlAdapterClass The class of the PublicUrlAdapter for which to set the constructor params
     * @param mixed[] $params Sequential array of arguments for the constructor of the PublicUrlAdapter
     * @throws Exception if $publicUrlAdapterClass is not a class, or if $params is not a sequential array
     */
    public function setParam(string $publicUrlAdapterClass, array $params) {
        if (!class_exists($publicUrlAdapterClass)) {
            throw new Exception("Class $publicUrlAdapterClass does not exist");
        }

        // $params is not a sequential array
        if (count($params) > 0 && array_keys($params) !== range(0, count($params) - 1)) {
            throw new Exception("Parameters for the PublicUrlAdapter must be a sequential array");
        }

        $this->params[$publicUrlAdapterClass] = $params;
    }


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

    /**
     * @param string $path
     * @return string|null
     * @throws Exception
     */
    public function handle(string $path) {
        if (!$this->filesystem instanceof Filesystem) {
            throw new Exception("PublicUrlPlugin can only be used on a Filesystem instance");
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
     * @return AbstractPublicUrlAdapter|null
     * @throws Exception
     */
    private function resolveUrlAdapter(AdapterInterface $adapter): ?AbstractPublicUrlAdapter {
        if (!$this->filesystem instanceof Filesystem) {
            throw new Exception("PublicUrlPlugin can only be used on a Filesystem instance");
        }

        $adapterClass = get_class($adapter);

        if (!array_key_exists($adapterClass, $this->adapters)) {
            return null;
        }

        $publicUrlAdapterClass = $this->adapters[$adapterClass];
        $params = $this->params[$publicUrlAdapterClass] ?? [];

        /** @var AbstractPublicUrlAdapter $publicUrlAdapter */
        $publicUrlAdapter= new $publicUrlAdapterClass(...$params);
        $publicUrlAdapter->setFilesystem($this->filesystem);

        return $publicUrlAdapter;
    }
}