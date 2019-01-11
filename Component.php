<?php
namespace Intersvyaz\AssetManager;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileInfo;
use Yii;
use yii\caching\Cache;

/**
 * Расширенный AssetManager, позволяющий рассчитывать хэш публикации ассетов по содержимому директории.
 * Необходимо для того, чтобы на всех нодах проекта ассеты находилить в одинаковых директориях.
 */
class Component extends \yii\web\AssetManager
{
    /**
     * Включение расчета хэша ассетов по содержимому директории.
     * @var bool
     */
    public $hashByContent = false;

    /**
     * Имя компонента, используемого для кеширования.
     * @var string
     */
    public $cacheComponent = 'cache';

    /**
     * @inheritdoc
     */
    public function hash($path)
    {
        if (!$this->hashByContent) {
            return parent::hash($path);
        }

        if (is_callable($this->hashCallback)) {
            return call_user_func($this->hashCallback, $path);
        }

        $path = (is_file($path) ? dirname($path) : $path);
        return $this->hashByContent($path);
    }

    /**
     * Расчет хэша ассетов по содержимому директории.
     * @param string $path
     * @return string
     */
    private function hashByContent($path)
    {
        $filemtime = @filemtime($path);
        $key = md5(__CLASS__ . $path . $filemtime);

        /** @var Cache $cacheComponent */
        $cacheComponent = Yii::$app->{$this->cacheComponent};
        $hash = $cacheComponent->getOrSet($key, function () use ($path, $filemtime) {
            $files = [];
            $it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path));

            /** @var SplFileInfo $file */
            foreach ($it as $file) {
                if ($file->isFile()) {
                    $files[] = md5_file($file);
                }
            }

            return md5($path . implode($files, '|'));
        }, 1);

        return sprintf('%x', crc32($hash . Yii::getVersion() . '|' . $this->linkAssets));
    }
}
