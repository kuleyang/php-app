<?php
/**
 *
 * Import.php
 *
 * 演示模块配置文件
 *
 * @package Demo
 */

namespace Demo;

use Cache\CachePool;

return array(
    /**
     * Redis缓存初始化回调配置
     */
    'redis' => function() {
        $storage = CachePool::factory(
            array(
                'storage'   => 'redis',
                'namespace' => 'demo:',
            )
        );
        CachePool::register($storage);
        CachePool::get()->setResource(
            array(
                'host' => '127.0.0.1',
                'port' => 6379,
            )
        );
        CachePool::get()->getResource();
        return CachePool::get();
    },
    'sql' => <<<EOT
    CREATE TABLE IF NOT EXISTS `demo` (
      `id` int(10) NOT NULL AUTO_INCREMENT,
      `name` char(50) DEFAULT NULL,
      `code` int(4) DEFAULT NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1
EOT
,
);
