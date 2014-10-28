Yii2 Backup and Restore Database
===================
Database Backup and Restore functionality

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist spanjeta/yii2-backup "*"
```

or add

```
"spanjeta/yii2-backup": "*"
```

to the require section of your `composer.json` file.


Usage
-----

Once the extension is installed, simply add it in your config by  :

Basic ```config/web.php```

Advanced ```[backend|frontend|common]/config/main.php```

>
        'modules'    => [
            'backup' => [
                'class' => 'spanjeta\modules\backup\Module',
            ],
            ...
            ...
        ],

make sure you create a writable directory named _backup on app root directory.

Usage
-----

Pretty Url's ```/backup```

No pretty Url's ```index.php?r=backup```
