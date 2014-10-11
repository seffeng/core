<?php
/**
 * @copyright 2014
 * @description: function.application
 * @file: f.a.php
 * @charset: UTF-8
 * @create: 2014-10-09
 * @version 1.0
**/

!defined('CORE') && die('ERROR-F_A');

/**
 * @name: set_init
 * @description: 设置并获取Include地址
 * @param: string Include地址
 * @return: array
 * @create: 2014-10-11
**/
function set_init($url=''){
    static $APP_OBJECT_URL = array();   //存放include路径
    if(!is_empty($url)){
        $is_add = TRUE;
        if(is_foreach($APP_OBJECT_URL)) foreach($APP_OBJECT_URL as $val) if($val == $url){$is_add = FALSE; break;}
    }else{
        $is_add = FALSE;
    }
    if($is_add) array_unshift($APP_OBJECT_URL, $url);
    return $APP_OBJECT_URL;
}

/**
 * @name: get_init
 * @description: 获取对象
 * @param: string 类名称
 * @param: string 类名对应标记 [default - NULL]
 * @param: boolean 是否new对象 [default - FALSE]
 * @param: boolean 是否自定义class [default - FALSE]
 * @return: object
 * @create: 2014-10-11
**/
function get_init($class_name, $mark_name=NULL, $is_new=FALSE, $is_zdy=FALSE){
    static $APP_OBJECT_DATA = array();    //存放对象,用于单例模式
    $return = FALSE;
    if(!$is_zdy) $class_name = (substr($class_name, 0, 4) == 'cls_') ? $class_name : 'cls_'.$class_name;
    if(!is_exists($class_name, 'class')){
        $object_class_url = '';
        $find_url = set_init();
        if(is_foreach($find_url)) foreach($find_url as $cur_inc){
            $object_class_tmp = $cur_inc.$class_name.'.php';
            if(is_exists($object_class_tmp, 'file')){
                $object_class_url = $object_class_tmp;
                break;
            }
        }
        if($object_class_url != ''){
            include_once($object_class_url);
            if(!is_exists($class_name, 'class')){
                debug('Class ['.$class_name.'] Not Found.', 'CORE_', TRUE);
            }
        }else{
            debug('Class ['.$class_name.'] File Not Found.', 'CORE_', TRUE);
        }
    }
    if(isset($APP_OBJECT_DATA[$class_name.$mark_name]) && !$is_new){
        $return = $APP_OBJECT_DATA[$class_name.$mark_name];
    }else{
        $APP_OBJECT_DATA[$class_name.$mark_name] = $return = new $class_name($mark_name);
    }
    return $return;
}

/**
 * @name: get_mod
 * @description: 获取模块对象
 * @param: string 模块名称
 * @return: object
 * @create: 2014-10-11
**/
function get_mod($mod_name, $mark_name=NULL, $is_new=FALSE){
    $mod_url = CORE_MOD.$mod_name.'/g.c.php';
    if(!is_exists($mod_url, 'file')){
        debug('File ['.$mod_url.'] Not Found.', 'CORE_', TRUE);
    }
    include_once($mod_url);
    return get_init($mod_name, $mark_name, $is_new);
}

/**
 * @name: get_cur_root
 * @description: 获取路径路径
 * @param: string 指定的路径
 * @return: string
 * @create: 2014-10-11
**/
function get_cur_root($cur_file){
    return preg_replace_callback("/[\/\\\\]{1,}/", function($match){return '/';}, dirname($cur_file)).'/';
}

/**
 * @name: debug
 * @description: debug错误日志写入
 * @param: string 被写入的错误信息
 * @param: string 被写入的错误类型 default[NULL]
 * @param: string 被写入的错误键值 default[NULL]
 * @param: string 是否终止程序 default[FALSE]
 * @return: void
 * @create: 2014-10-11
**/
function debug($er_string, $er_type=NULL, $er_key=NULL, $is_exit=FALSE){
    get_mod('log') -> write_string($er_string, $er_type, $er_key);
    if($is_exit) exit;
}
?>