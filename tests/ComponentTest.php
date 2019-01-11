<?php
namespace tests;

use Intersvyaz\AssetManager;

class ComponentTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Нативное кеширование по дате изменения директории работает.
     */
    public function testNativeMTime()
    {
        $assetDir = __DIR__ . '/runtime/assets';
        $manager = new AssetManager\Component();

        $hash1 = $manager->hash($assetDir);
        $this->touch($assetDir);
        $hash2 = $manager->hash($assetDir);

        $this->assertNotEquals($hash1, $hash2);
    }

    /**
     * Нативная возможность задания hashCallback работает не смотря на hashByContent=true.
     */
    public function testNativeCallback()
    {
        $assetDir = __DIR__ . '/runtime/assets';
        $manager = new AssetManager\Component();
        $manager->hashByContent = true;
        $manager->hashCallback = function () {
            return 'test';
        };

        $this->assertEquals('test', $manager->hash($assetDir));
    }

    /**
     * Тестирование кеширования по контенту.
     * Если происходит touch директории, но содержимое директории не меняется, то хэш не должен поменяться.
     */
    public function testContentTouch()
    {
        $assetDir = __DIR__ . '/runtime/assets';
        $manager = new AssetManager\Component();
        $manager->hashByContent = true;

        $hash1 = $manager->hash($assetDir);
        $this->touch($assetDir);
        $hash2 = $manager->hash($assetDir);

        $this->assertEquals($hash1, $hash2);
    }

    /**
     * Тестирование кеширования по контенту.
     * Проверяем, что работает кеширование расчета хэша.
     * Если не тачить директорию, что хэш не будет пересчитываться.
     */
    public function testContentNoTouch()
    {
        $assetDir = __DIR__ . '/runtime/assets';
        $manager = new AssetManager\Component();
        $manager->hashByContent = true;

        $hash1 = $manager->hash($assetDir);
        file_put_contents($assetDir . '/1.txt', rand(1, 999999999999));
        $hash2 = $manager->hash($assetDir);

        $this->assertEquals($hash1, $hash2);
    }

    public function testContentChangeHash()
    {
        $assetDir = __DIR__ . '/runtime/assets';
        $manager = new AssetManager\Component();
        $manager->hashByContent = true;

        $hash1 = $manager->hash($assetDir);
        $rand1 = rand(1, 999999999999);
        $rand2 = rand(1, 999999999999);

        // Если изменяем файл, то хэш должен поменяться.
        file_put_contents($assetDir . '/1.txt', $rand1);
        $this->touch($assetDir);
        $hash2 = $manager->hash($assetDir);
        $this->assertNotEquals($hash1, $hash2, 'testContentChangeHash fail 1');

        // Если ещё раз изменяем файл, то кэш опять должен поменяться.
        file_put_contents($assetDir . '/1.txt', $rand2);
        $this->touch($assetDir);
        $hash3 = $manager->hash($assetDir);
        $this->assertNotEquals($hash2, $hash3, 'testContentChangeHash fail 2');

        // Если восстанавливаем содержимое файла до состояния $rand1, то хэш должен стать тот же, который был до этого.
        file_put_contents($assetDir . '/1.txt', $rand1);
        $this->touch($assetDir);
        $hash3 = $manager->hash($assetDir);
        $this->assertEquals($hash2, $hash3, 'testContentChangeHash fail 3');
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
