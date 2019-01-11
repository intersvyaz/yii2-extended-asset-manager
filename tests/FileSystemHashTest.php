<?php
namespace tests;

use Intersvyaz\AssetManager\FileSystemHash;

class FileSystemHashTest extends TestCase
{
    /**
     * Проверяем работоспособность получения хэша по пустой директории.
     * @covers \Intersvyaz\AssetManager\FileSystemHash::hashPath()
     */
    public function testCalculatePathHashEmptyDirectory()
    {
        $assetDir = __DIR__ . '/runtime/assets/test';
        $this->rmDir($assetDir);
        $this->mkDir($assetDir);

        $manager = new FileSystemHash();

        $this->assertEquals(
            '4c18c2f682825ed743afb28038f4925e',
            $this->invokeMethod($manager, 'hashPath', [$assetDir])
        );

        $this->rmDir($assetDir);
    }

    /**
     * Проверяем работоспособность получения хэша по непустой директории.
     * @covers \Intersvyaz\AssetManager\FileSystemHash::hashPath()
     */
    public function testCalculatePathHashNotEmptyDirectory()
    {
        $manager = new FileSystemHash();

        $assetDir = __DIR__ . '/runtime/assets/test';
        $this->rmDir($assetDir);
        $this->mkDir($assetDir);

        file_put_contents($assetDir . '/test.txt', 'testfile');
        $this->assertEquals(
            '2a6de41f2d847263fd06cce75bd053a4',
            $this->invokeMethod($manager, 'hashPath', [$assetDir]),
            'Test 1 failed'
        );

        @unlink($assetDir . '/test.txt');
        file_put_contents($assetDir . '/test.txt', 'testfile');
        $this->assertEquals(
            '2a6de41f2d847263fd06cce75bd053a4',
            $this->invokeMethod($manager, 'hashPath', [$assetDir]),
            'Test 2 failed'
        );

        file_put_contents($assetDir . '/test.txt', 'testfile1');
        $this->assertEquals(
            '6612e2ed59d70dea91d373919209adc0',
            $this->invokeMethod($manager, 'hashPath', [$assetDir]),
            'Test 3 failed'
        );

        $this->mkDir($assetDir . '/subdirectory');
        $this->assertEquals(
            '6612e2ed59d70dea91d373919209adc0',
            $this->invokeMethod($manager, 'hashPath', [$assetDir]),
            'Test 4 failed'
        );

        file_put_contents($assetDir . '/subdirectory/test.txt', 'testfile');
        $this->assertEquals(
            'c54f8657e1e9841b9d3baf8c2eccd1f5',
            $this->invokeMethod($manager, 'hashPath', [$assetDir]),
            'Test 5 failed'
        );

        $this->rmDir($assetDir);

        // Проверка на то, что хэш должен зависеть не только md5sum файлов, но и от их расположения.
        $assetDir = __DIR__ . '/runtime/assets/test2';
        $this->rmDir($assetDir);
        $this->mkDir($assetDir);

        file_put_contents($assetDir . '/test.txt', 'testfile');
        $this->assertEquals(
            'e3639e0ffce12d1ee3977a38909594a0',
            $this->invokeMethod($manager, 'hashPath', [$assetDir]),
            'Test 6 failed'
        );

        $this->rmDir($assetDir);
    }

    /**
     * Проверяем, что работает получешие хэша по файлу.
     * @covers \Intersvyaz\AssetManager\FileSystemHash::hashPath()
     */
    public function testCalculatePathHashFile()
    {
        $assetDir = __DIR__ . '/runtime/assets/test';
        $this->rmDir($assetDir);
        $this->mkDir($assetDir);

        $manager = new FileSystemHash();

        file_put_contents($assetDir . '/test.txt', 'testfile');
        $this->assertEquals(
            '2a6de41f2d847263fd06cce75bd053a4',
            $this->invokeMethod($manager, 'hashPath', [$assetDir . '/test.txt'])
        );

        $this->rmDir($assetDir);
    }
}
