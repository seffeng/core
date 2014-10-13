<?php
/**
 * @copyright 2014
 * @description: global.config
 * @file: g.c.php
 * @charset: UTF-8
 * @create: 2014-10-13
 * @version 1.0
**/

!defined('CORE') && die('ERROR-MOD_SESS');
$CUR_ROOT = get_cur_root(__FILE__);
include_once($CUR_ROOT.'cls_sess.php');
include_once($CUR_ROOT.'cls_session_mysqli.php');
include_once($CUR_ROOT.'cls_session_cache.php');
/*
//mysql-table
CREATE TABLE IF NOT EXISTS `se_session` (
  `sess_id` char(32) COLLATE utf8_unicode_ci NOT NULL COMMENT 'SESSION_ID',
  `sess_data` varchar(2500) COLLATE utf8_unicode_ci NOT NULL COMMENT 'SESSION_DATA',
  `sess_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'SESSION_TIME',
  `sess_ip` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'SESSION_IP',
  `sess_u_id` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT 'SESSION_U_ID',
  KEY `sess_id` (`sess_id`)
) ENGINE=MEMORY DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='SESSION';
*/
/*
get_mod('sess') -> set_conf(array(
    'SESS_TYPE' => 'MYSQLI',
    'SESS_PATH' => NULL,
    'SESS_NAME' => 'PHPSESSID',
    'SESS_TABLE_NAME' => 'se_session',
    'SESS_ID_NAME' => 'sess_id',
    'SESS_DATA_NAME' => 'sess_data',
    'SESS_TIME_NAME' => 'sess_time',
    'SESS_IP_NAME' => 'sess_ip',
    'SESS_U_ID_NAME' => 'sess_u_id',
    'SESS_U_NAME' => 'u_id',
    'MAX_LIFE_TIME' => 1440,
    'SESS_MYSQLI_MARK' => 'session_mysqli',
    'SESS_MYSQLI_CONF' => array(
        'MYSQLI_HOST' => '127.0.0.1',
        'MYSQLI_USER' => 'root',
        'MYSQLI_PASS' => 'root',
        'MYSQLI_DBNAME' => 'test',
        'MYSQLI_TABLE_PREFIX' => 'se_'
    )
));
get_mod('sess') -> set_conf(array(
    'SESS_TYPE' => 'CACHE',
    'SESS_COOKIE' => TRUE,
    'SESS_PATH' => NULL,
    'SESS_NAME' => 'PHPSESSID',
    'MAX_LIFE_TIME' => 1440,
    'SESS_CACHE_MARK' => 'session_cache',
    'SESS_CACHE_CONF' => array(
        'CACHE_TYPE' => 'MEMCACHED',
        'MEMCACHED_DATA' => array(
            'MEMCACHE_HOST' => 'localhost',
            'MEMCACHE_PORT' => 11211,
            'MEMCACHE_TIME_OUT' => 5,
            'MEMCACHE_EXPIRE' => 0,
            'MEMCACHE_FLAG' => 0
        )
    )
));
*/
?>