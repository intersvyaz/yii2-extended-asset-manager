<?php
namespace Intersvyaz\AssetManager;

/**
 * Интерфейс, предоставляющий возможность получение хэша по объектам файловой системы.
 */
interface FileSystemHashInterface
{
    /**
     * Расчет md5 хэша директории или файла.
     * @param string $path
     * @return string
     */
    public function hashPath($path);

    /**
     * Хэширование пути без учета расположения проекта.
     * @param string $path
     * @return mixed
     */
    public function hashPathName($path);
}
