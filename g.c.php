<?php
/**
 * @copyright 2014
 * @description: global.config
 * @file: g.c.php
 * @charset: UTF-8
 * @create: 2014-10-09
 * @version 1.0
**/

define('CORE',              'CORE');
define('CORE_VERSION',      '1.0');
define('CORE_ROOT',         preg_replace_callback('/[\/\\\\]+/', function($match){return '/';}, dirname(__FILE__)).'/');
define('CORE_DATA',         CORE_ROOT.'data/');
define('CORE_INC',          CORE_ROOT.'inc/');
define('CORE_MOD',          CORE_ROOT.'mod/');
header('Content-Type: text/html; charset=UTF-8');
include_once(CORE_INC.'c.p.php');
include_once(CORE_INC.'f.g.php');
include_once(CORE_INC.'c.d.php');
include_once(CORE_INC.'f.a.php');
?>