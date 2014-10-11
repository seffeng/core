<?php
/**
 * @copyright 2014
 * @description: config.define
 * @file: c.d.php
 * @charset: UTF-8
 * @create: 2014-10-09
 * @version 1.0
**/

!defined('CORE') && die('ERROR-C_D');
define('CORE_THIS_TIME',            time());                    //当前时间戳
define('CORE_THIS_IPS',             get_ip());                  //客户端IP
define('CORE_THIS_IP',              ip_long(CORE_THIS_IPS));    //客户端IP_INT
?>