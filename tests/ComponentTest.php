<?php
namespace tests;

use Intersvyaz\AssetManager;

class ComponentTest extends TestCase
{
    /**
     * Проверка на работоспособность нативного кеширования по дате изменения.
     * @covers \Intersvyaz\AssetManager\Component::hash()
     */
    public function testNativeMTime()
    {
        $assetDir = __DIR__ . '/runtime/assets';
        $manager = new AssetManager\Component();

        $hash1 = $this->invokeMethod($manager, 'hash', [$assetDir]);
        $this->touch($assetDir);
        $hash2 = $this->invokeMethod($manager, 'hash', [$assetDir]);

        $this->assertNotEquals($hash1, $hash2);
    }

    /**
     * Проверка работоспособности нативной возможности задания hashCallback, не смотря на hashByContent=true.
     * @covers \Intersvyaz\AssetManager\Component::hash()
     */
    public function testNativeCallback()
    {
        $assetDir = __DIR__ . '/runtime/assets';
        $manager = new AssetManager\Component([
            'hashByContent' => true,
            'fileSystemHash' => 'tests\FakeFileSystemHash',
            'hashCallback' => function () {
                return 'test';
            },
        ]);

        $this->assertEquals('test', $this->invokeMethod($manager, 'hash', [$assetDir]));
    }

    /**
     * @covers \Intersvyaz\AssetManager\Component::hash()
     */
    public function testHashByContent()
    {
        $assetDir = __DIR__ . '/runtime/assets/' . uniqid();
        $this->mkDir($assetDir);


        $manager = new AssetManager\Component([
            'hashByContent' => true,
            'fileSystemHash' => 'tests\FakeFileSystemHash',
        ]);

        $hash = $this->invokeMethod($manager, 'hash', [$assetDir]);
        $this->assertEquals('6230f8d5', $hash, 'Test 1 failed');
        $this->assertEquals($hash, $this->invokeMethod($manager, 'hash', [$assetDir . '/.gitignore']), 'Test 2 failed');

        $this->touch($assetDir);
        $this->assertEquals($hash, $this->invokeMethod($manager, 'hash', [$assetDir]), 'Test 3 failed');

        $this->rmDir($assetDir);
    }

    /**
     * @covers \Intersvyaz\AssetManager\Component::hashByContent()
     */
    public function testHashByContentCaching()
    {
        $assetDir = __DIR__ . '/runtime/assets/' . uniqid();
        $this->mkDir($assetDir);

        $fakeHash = new FakeFileSystemHash();

        $manager = new AssetManager\Component([
            'hashByContent' => true,
            'fileSystemHash' => $fakeHash,
        ]);

        $this->assertEquals('6230f8d5', $this->invokeMethod($manager, 'hashByContent', [$assetDir]), 'Test 1 failed');

        // Если добавить файл, но не тачнуть директорию, то хэш останется такой же
        file_put_contents($assetDir . '/test.txt', 'testfile');
        $fakeHash->hashPath = 'test';
        $this->assertEquals('6230f8d5', $this->invokeMethod($manager, 'hashByContent', [$assetDir]), 'Test 2 failed');


        // Если тачнуть, то ключ кеширования должен устареть и будет повторный пересчет md5 сумм всех файлов.
        $this->touch($assetDir);
        $this->assertEquals('90815524', $this->invokeMethod($manager, 'hashByContent', [$assetDir]), 'Test 3 failed');

        $this->rmDir($assetDir);
    }

    /**
     * Изменение даты модификации файла/директории.
     * Намеренно делается sleep(1), т.к. при выполнении метода несколько раз в секунду дата модицикации у файла
     * не будет меняться, т.к. точность на ФС до секунд.
     * @param string $path
     */
    private function touch($path)
    {
        sleep(1);
        touch($path);
        clearstatcache();
    }
}
