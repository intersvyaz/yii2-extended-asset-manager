#intersvyaz/yii2-extended-asset-manager

[![Build Status](https://travis-ci.org/intersvyaz/yii2-extended-asset-manager.svg)](https://travis-ci.org/intersvyaz/yii2-extended-asset-manager)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/intersvyaz/yii2-extended-asset-manager/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/intersvyaz/yii2-extended-asset-manager/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/intersvyaz/yii2-extended-asset-manager/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/intersvyaz/yii2-extended-asset-manager/?branch=master)


Extend Yii2 AssetManager for support to produce hash for asset directory generation by md5sum of all file on directory.

Configuring config/web.php:
```
'assetManager' => [
    'class' => \Intersvyaz\AssetManager\Component::class,
    'hashByContent' => true,
],
```
