<?php


namespace Thib\FlysystemPublicUrlPlugin\Adapter;


class LocalUrlAdapter extends AbstractPublicUrlAdapter
{
    /** @var string */
    private $root;

    /**
     * LocalUrlAdapter constructor.
     * @param string $root
     */
    public function __construct(string $root)
    {
        $this->root = $root;
    }

    public function getPublicUrl(string $path): string
    {
        $root = $this->root;
        $trailingSlash = substr($root, -1) === '/';

        $leadingSlash = substr($path, 0, 1) === '/';

        // Remove one of the two slashes in "/root//path"
        if ($trailingSlash && $leadingSlash) {
            return substr($root, 0, -1) . $path;
        }

        // Add a slash in "/rootpath"
        if (!$trailingSlash && !$leadingSlash) {
            return $root . '/' . $path;
        }

        return $root . $path;
    }
}