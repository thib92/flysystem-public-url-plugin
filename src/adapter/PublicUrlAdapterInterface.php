<?php


namespace Thib\FlysystemPublicUrlPlugin\adapter;


interface PublicUrlAdapterInterface
{
    public function getPublicUrl(string $path): string;
}