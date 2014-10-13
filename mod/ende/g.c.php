<?php
/**
 * @copyright 2014
 * @description: global.config
 * @file: g.c.php
 * @charset: UTF-8
 * @create: 2014-10-13
 * @version 1.0
**/

!defined('CORE') && die('ERROR-MOD_ENDE');
$CUR_ROOT = get_cur_root(__FILE__);
include_once($CUR_ROOT.'cls_ende.php');
include_once($CUR_ROOT.'cls_encdec.php');
/*
get_mod('ende') -> set_conf(array(
    'ENDE_KEY' => 'mykey',
    'ENDE_KEYNAME' => 'mycode',
    'ENDE_NOCRYPT' => array()
));
*/
?>