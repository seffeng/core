<?php
/**
 * @copyright 2014
 * @description: global.config
 * @file: g.c.php
 * @charset: UTF-8
 * @create: 2014-10-13
 * @version 1.0
**/

!defined('CORE') && die('ERROR-MOD_UA');
$CUR_ROOT = get_cur_root(__FILE__);
include_once($CUR_ROOT.'cls_ua.php');
include_once($CUR_ROOT.'cls_uasparser.php');
get_mod('ua') -> set_conf(array(
    'UA_UPDATE_TIME' => 172800,
    'UA_IS_DOWNLOAD' => FALSE,
    'UA_CACHE_DIR' => $CUR_ROOT.'uagent/'
));
?>