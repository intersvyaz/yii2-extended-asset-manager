<?php
namespace tests;

use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;
use SplFileInfo;

class TestCase extends \PHPUnit\Framework\TestCase
{
    protected function invokeMethod($object, $method, $args = [])
    {
        $reflection = new \ReflectionObject($object);
        $method = $reflection->getMethod($method);
        $method->setAccessible(true);
        $result = $method->invokeArgs($object, $args);
        $method->setAccessible(false);
        return $result;
    }

    protected function mkDir($dir)
    {
        return @mkdir($dir, 0777, true);
    }

    protected function rmDir($dir)
    {
        if (!$dir = realpath($dir)) {
            return false;
        }

        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($dir),
            RecursiveIteratorIterator::CHILD_FIRST
        );

        /** @var SplFileInfo $path */
        foreach ($iterator as $path) {
            if ($path->isDir()) {
                if (!in_array($path->getBasename(), ['.', '..'])) {
                    @rmdir((string)$path);
                }
            } else {
                @unlink((string)$path);
            }
        }

        return @rmdir($dir);
    }
}
