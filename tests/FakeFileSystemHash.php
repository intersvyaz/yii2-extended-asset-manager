<?php
namespace tests;

class FakeFileSystemHash implements \Intersvyaz\AssetManager\FileSystemHashInterface
{
    public $hashPath = 'hashPath';
    public $hashPathName = 'hashPathName';

    /**
     * @inheritdoc
     */
    public function hashPath($path)
    {
        return $this->hashPath;
    }

    /**
     * @inheritdoc
     */
    public function hashPathName($path)
    {
        return $this->hashPathName;
    }
}
