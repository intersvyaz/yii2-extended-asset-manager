<?php
namespace Intersvyaz\AssetManager;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileInfo;
use Yii;

/**
 * Возможность получение хэша по объектам файловой системы.
 */
class FileSystemHash implements FileSystemHashInterface
{
    /**
     * @inheritdoc
     */
    public function hashPath($path)
    {
        $path = realpath($path);
        $hashes = [];

        if (is_dir($path)) {
            $it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path));

            /** @var SplFileInfo $file */
            foreach ($it as $file) {
                if ($file->isFile()) {
                    $hashes[] = md5_file($file) . '-' . $this->hashPathName((string)$file);
                }
            }

            if (empty($hashes)) {
                $hashes[] = 'null-' . $this->hashPathName($path);
            } else {
                sort($hashes);
            }
        } else {
            $hashes[] = md5_file($path) . '-' . $this->hashPathName((string)$path);
        }

        return md5(implode('|', $hashes));
    }

    /**
     * @inheritdoc
     */
    public function hashPathName($path)
    {
        $hashPath = str_replace(Yii::getAlias('@app'), '', $path);

        if (DIRECTORY_SEPARATOR === '\\') {
            $hashPath = str_replace(DIRECTORY_SEPARATOR, '/', $hashPath);
        }

        return md5($hashPath);
    }
}
