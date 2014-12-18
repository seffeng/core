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

/**
 * @name: get_psize
 * @description: 获取分页列表每页显示记录数量
 * @param: integer 当前设定每页
 * @return: array
**/
function get_psize($size){
    $s_allow = array(15, 30, 60, 99);
    $r_allow = array();
    foreach($s_allow as $val) $r_allow[$val] = 0;
    if(in_array($size, $s_allow)){
        $r_allow[$size] = 1;
        return array('s' => $size, 'l' => $r_allow);
    }
    if($size < $s_allow[0]){
        $r_allow[$s_allow[0]] = 1;
        return array('s' => $s_allow[0], 'l' => $r_allow);
    }
    $s_allow_leng = count($s_allow);
    if($size > $s_allow[$s_allow_leng-1]){
        $r_allow[$s_allow[$s_allow_leng-1]] = 1;
        return array('s' => $s_allow[$s_allow_leng-1], 'l' => $r_allow);
    }
    foreach($s_allow as $key => $val){
        $min = $val;
        $max_i = $key+1;
        if($max_i < $s_allow_leng){
            $max = $s_allow[$max_i];
        }else{
            $max = $min;
        }
        if($size <= $max && $size >= $min){
            $r_allow[$min] = 1;
            return array('s' => $min, 'l' => $r_allow);
        }
    }
    $r_allow[$s_allow[0]] = 1;
    return array('s' => $s_allow[0], 'l' => $r_allow);
}

/**
 * @name: get_page
 * @description: 计算分页数据
 * @param: integer 总记录数
 * @param: integer 当前页数
 * @param: integer 每页记录数
 * @param: integer 显示的前后页数
 * @param: array 其他数组数据
 * @return: array
**/
function get_page($num, $pgcur=1, $size=15, $pnum=3, $array=array()){
    $size = get_psize($size);
    $psize = $size['s'];
    $plist = $size['l'];
    $pgmax = $num < $psize ? 1 : ceil($num/$psize);
    $pgcur = min(max($pgcur, 1), $pgmax);
    $select = $list = array();
    while($pnum > 0){
        $pgtmp = min(max($pgcur-$pnum, 1), $pgmax);
        $list[$pgtmp] = 0;
        $pgtmp = min(max($pgcur+$pnum, 1), $pgmax);
        $list[$pgtmp] = 0;
        --$pnum;
    }
    $list[$pgcur] = 1;
    ksort($list, SORT_NUMERIC);
    if($pgmax <= 60){   //下拉框
        $pnum = $pgmax;
        while($pnum > 0){
            $select[$pnum] = 0;
            --$pnum;
        }
    }else{
        $last_min = 0;
        $last_max = $pgmax;
        $pnum = 9;
        while($pnum > 0){   //当前页左右
            $pgtmp = min(max($pgcur-$pnum, 1), $pgmax);
            $select[$pgtmp] = 0;
            $pgtmp = min(max($pgcur+$pnum, 1), $pgmax);
            $select[$pgtmp] = 0;
            --$pnum;
        }
        $pnum = 4;
        while($pnum >= 0){  //最后和最前
            $pgtmp = min(max($pgmax-$pnum, 1), $pgmax);
            $select[$pgtmp] = 0;
            $last_max = $pgtmp;
            $pgtmp = min(max($pnum+1, 1), $pgmax);
            $select[$pgtmp] = 0;
            if($last_min < 1) $last_min = $pgtmp;
            --$pnum;
        }
        $pnum = 4;
        while($pnum >= 0){  //最后和最前双倍
            $last_max -= 2;
            $pgtmp = min(max($last_max, 1), $pgmax);
            $select[$pgtmp] = 0;
            $last_min += 2;
            $pgtmp = min(max($last_min, 1), $pgmax);
            $select[$pgtmp] = 0;
            --$pnum;
        }
        $pnum = 10;
        $curnum = ceil($pgmax/2);
        while(count($select) < 60 && $pnum >= 0){   //中心点
            $pgtmp = min(max($curnum+$pnum+rand(2, 6), 1), $pgmax);
            $select[$pgtmp] = 0;
            $pgtmp = min(max($curnum-$pnum-rand(2, 6), 1), $pgmax);
            $select[$pgtmp] = 0;
            --$pnum;
        }
    }
    $select[$pgcur] = 1;
    ksort($select, SORT_NUMERIC);
    return array_merge($array, array(
        'num'   => $num,
        'psize' => $psize,
        'pgcur' => $pgcur,
        'pgmax' => $pgmax,
        'first' => 1,
        'prev'  => $pgcur <= 1 ? 0 : min(max($pgcur-1, 1), $pgmax),
        'next'  => $pgcur >= $pgmax ? 0 : min(max($pgcur+1, 1), $pgmax),
        'end'   => $pgmax,
        'plist' => $plist,
        'list'  => $list,
        'select' => $select,
    ));
}
?>