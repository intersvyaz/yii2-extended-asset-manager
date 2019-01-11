<?php
namespace Intersvyaz\AssetManager;

use Yii;
use yii\caching\Cache;
use yii\caching\CacheInterface;
use yii\di\Instance;

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
     * Класс, реализующий возможность вычисления хэша по ФС.
     * @var FileSystemHashInterface
     */
    public $fileSystemHash = '\Intersvyaz\AssetManager\FileSystemHash';

    /**
     * Имя компонента, используемого для кеширования.
     * @var CacheInterface|array|string
     */
    public $cacheComponent = 'cache';

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        $this->fileSystemHash = Instance::ensure(
            $this->fileSystemHash,
            'Intersvyaz\AssetManager\FileSystemHashInterface'
        );
    }

    /**
     * @inheritdoc
     */
    protected function hash($path)
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
    protected function hashByContent($path)
    {
        $filemtime = @filemtime($path);
        $key = md5(__CLASS__ . $path . $filemtime);

        /** @var Cache $cacheComponent */
        $cacheComponent = Yii::$app->{$this->cacheComponent};

        $hash = $cacheComponent->getOrSet($key, function () use ($path) {
            return $this->fileSystemHash->hashPath($path);
        });

        return sprintf('%x', crc32(
                $this->fileSystemHash->hashPathName($path) . '|' .
                $hash . Yii::getVersion() . '|' .
                $this->linkAssets
            )
        );
    }
}
