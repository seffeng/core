<?php
/**
 * @copyright 2014
 * @description: global.config
 * @file: g.c.php
 * @charset: UTF-8
 * @create: 2014-10-13
 * @version 1.0
**/

!defined('CORE') && die('ERROR-MOD_FTP');
$CUR_ROOT = get_cur_root(__FILE__);
include_once($CUR_ROOT.'cls_ftp.php');
include_once($CUR_ROOT.'cls_ftps.php');
/*
get_mod('ftp') -> set_conf(array(
    'FTP_HOST' => 'localhost',
    'FTP_USER' => 'ftpuser',
    'FTP_PASS' => 'ftppass',
    'FTP_PORT' => 21
));
*/
?>