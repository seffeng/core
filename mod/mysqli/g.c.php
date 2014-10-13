<?php
/**
 * @copyright 2014
 * @description: global.config
 * @file: g.c.php
 * @charset: UTF-8
 * @create: 2014-10-10
 * @version 1.0
**/

!defined('CORE') && die('ERROR-MOD_MYSQLI');
$CUR_ROOT = get_cur_root(__FILE__);
include_once($CUR_ROOT.'cls_mysqli.php');
include_once($CUR_ROOT.'cls_mysqlis.php');
/*
get_mod('mysqli') -> set_conf(array(
    'MYSQLI_HOST' => 'localhost',
    'MYSQLI_USER' => 'root',
    'MYSQLI_PASS' => 'root',
    'MYSQLI_DBNAME' => NULL,
    'MYSQLI_TABLE_PREFIX' => NULL,
    'MYSQLI_PORT' => 3306,
    'MYSQLI_CHARSET' => 'utf8'
));
*/
?>