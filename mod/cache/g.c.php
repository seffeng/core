<?php
/**
 * @copyright 2014
 * @description: global.config
 * @file: g.c.php
 * @charset: UTF-8
 * @create: 2014-10-13
 * @version 1.0
**/

!defined('CORE') && die('ERROR-MOD_CACHE');
$CUR_ROOT = get_cur_root(__FILE__);
include_once($CUR_ROOT.'cls_cache.php');
include_once($CUR_ROOT.'cls_memcached.php');
/*
get_mod('cache') -> set_conf(array(
    'CACHE_TYPE' => 'MEMCACHED',
    'MEMCACHED_DATA' => array(
        'MEMCACHE_HOST' => 'localhost',
        'MEMCACHE_PORT' => 11211,
        'MEMCACHE_TIME_OUT' => 5,
        'MEMCACHE_EXPIRE' => 0,
        'MEMCACHE_FLAG' => 0
    )
));
*/
?>