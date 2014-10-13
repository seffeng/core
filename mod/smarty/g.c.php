<?php
/**
 * @copyright 2014
 * @description: global.config
 * @file: g.c.php
 * @charset: UTF-8
 * @create: 2014-10-13
 * @version 1.0
**/

!defined('CORE') && die('ERROR-MOD_SMARTY');
$CUR_ROOT = get_cur_root(__FILE__);
include_once($CUR_ROOT.'cls_smarty.php');
include_once($CUR_ROOT.'smarty.class.php');
/*
get_mod('smarty') -> set_conf(array(
    'SMARTY_COMPILE_CHECK' => TRUE,
    'SMARTY_DEBUG' => FALSE,
    'SMARTY_CACHE' => FALSE,
    'SMARTY_USE_SUB_DIRS' => TRUE,
    'SMARTY_CACHE_LIFE_TIME' => 300,
    'SMARTY_LEFT_DELI' => '<{',
    'SMARTY_RIGHT_DELI' => '}>',
    'SMARTY_DATA_CONFIG' => CORE_DATA.'smarty/config/',
    'SMARTY_DATA_CACHE' => CORE_DATA.'smarty/cache/',
    'SMARTY_TPL' => CORE_DATA.'smarty/tpl/',
    'SMARTY_DATA_TPL_C' => CORE_DATA.'smarty/tpl_c/'
));
*/
?>