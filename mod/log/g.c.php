<?php
/**
 * @copyright 2014
 * @description: global.config
 * @file: g.c.php
 * @charset: UTF-8
 * @create: 2014-10-11
 * @version 1.0
**/

!defined('CORE') && die('ERROR-MOD_LOG');
$CUR_ROOT = get_cur_root(__FILE__);
include_once($CUR_ROOT.'cls_log.php');
include_once($CUR_ROOT.'cls_logs.php');
get_init('log') -> set_conf(array(
    'LOG_DATAURL' => CORE_DATA.'log/'
));
?>