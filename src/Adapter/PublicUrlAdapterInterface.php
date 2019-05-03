<?php


namespace Thib\FlysystemPublicUrlPlugin\Adapter;


interface PublicUrlAdapterInterface
{
    public function getPublicUrl(string $path): string;
}