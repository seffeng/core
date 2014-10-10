<?php
/**
 * @copyright 2014
 * @description: function.global
 * @file: f.g.php
 * @charset: UTF-8
 * @create: 2014-10-09
 * @version 1.0
**/

!defined('CORE') && die('ERROR-F_G');

/**
 * @name: is_empty
 * @description: �������Ƿ�Ϊ��
 * @param: mixed ��Ҫ�жϱ���
 * @return: boolean
 * @create: 2014-10-09
**/
function is_empty($var_name){
    $return = FALSE;
    !isset($var_name) && $return = TRUE;
    if(!$return){
        switch(strtolower(gettype($var_name))){
            case 'null'     : {$return = TRUE; break;}
            case 'integer'  : {$return = FALSE; break;}
            case 'double'   : {$return = FALSE; break;}
            case 'boolean'  : {$return = FALSE; break;}
            case 'string'   : {$return = $var_name === '' ? TRUE : FALSE; break;}
            case 'array'    : {$return = count($var_name) > 0 ? FALSE : TRUE; break;}
            case 'object'   : {$return = $var_name === NULL ? TRUE : FALSE; break;}
            case 'resource' : {$return = $var_name === NULL ? TRUE : FALSE; break;}
            default : {$return = TRUE;}
        }
    }
    return $return;
}

/**
 * @name: is_type
 * @description: �����������Ƿ�Ϊָ��
 * @param: mixed ��Ҫ�жϱ���
 * @param: string|array �жϵ����
 * @return: boolean
 * @create: 2014-10-09
**/
function is_type($var_name, $var_type){
    $return = FALSE;
    $var_name_resource_type = NULL;
    $var_name_type = strtolower(gettype($var_name));
    $var_name_type == 'resource' && $var_name_resource_type = strtolower(get_resource_type($var_name));
    $var_type_type = strtolower(gettype($var_type));
    if($var_type_type == 'array'){
        if(count($var_type) > 0){
            foreach($var_type as $key => $val){
                $var_type[$key] = strtolower($val);
            }
        }
        $return = in_array($var_name_type, $var_type, TRUE) ? TRUE : FALSE;
        (!$return && !is_empty($var_name_resource_type)) && $return = in_array($var_name_type.'-'.$var_name_resource_type, $var_type, TRUE) ? TRUE : FALSE;
    }
    $var_type_type == 'string' && $return = ($var_name_type == strtolower($var_type) || $var_name_type.'-'.$var_name_resource_type == strtolower($var_type)) ? TRUE : FALSE;
    return $return;
}

/**
 * @name: is_exists
 * @description: �ж��Ƿ����[�������ࡢ�ӿڡ��෽�����������ļ���·��]
 * @param: mixed ��Ҫ�жϱ���
 * @param: string ������� default[var]
 * @param: object ����� default[NULL]
 * @return: boolean
 * @create: 2014-10-09
**/
function is_exists($var_name, $var_type='var', $object=NULL){
    $return = FALSE;
    switch(strtolower(trim($var_type))){
        case 'var'      : {$return = isset($var_name) ? TRUE : FALSE; break;}
        case 'file'     : {$return = file_exists($var_name) ? TRUE : FALSE; break;}
        case 'function' : {$return = function_exists($val_name) ? TRUE : FALSE; break;}
        case 'class'    : {$return = class_exists($var_name) ? TRUE : FALSE; break;}
        case 'interface': {$return = interface_exists($var_name) ? TRUE : FALSE; break;}
        case 'method'   : {$return = method_exists($object, $var_name) ? TRUE : FALSE; break;}
        case 'dir'      : {
                            $return = !is_file($var_name) ? TRUE : FALSE;
                            $return && $return = is_exists($var_name, 'file', $object);
                            BREAK;
                        }
    }
    return $return;
}

/**
 * @name: is_include
 * @description: �ļ��Ƿ�����
 * @param: string ������ļ�ȫ·��
 * @return: boolean
 * @create: 2014-10-09
**/
function is_include($include_file){
    $include_file = preg_replace_callback('/[\/\\\\]+/', function($match){return DIRECTORY_SEPARATOR;}, $include_file);
    return in_array($include_file, get_included_files(), TRUE) ? TRUE : FALSE;
}

/**
 * @name: get_cur_time
 * @description: ��ȡ��ǰʱ��
 * @param: boolean �����Ƿ��ַ���[FALSE]
 * @return: array
 * @create: 2014-10-09
**/
function get_cur_time($is_string=FALSE){
    $cur_time = microtime();
    return $is_string ? $cur_time : array(doubleval(substr($cur_time, 0, 10)), intval(substr($cur_time, 11, 10)));
}

/**
 * @name: time_array
 * @description: ʱ���ַ���ת����ʱ������
 * @param: string ʱ���ַ���[microtime]
 * @return: array
 * @create: 2014-10-09
**/
function time_array($string){
    return array(doubleval(substr($string, 0, 10)), intval(substr($string, 11, 10)));
}

/**
 * @name: time_diff
 * @description: ����ʱ���
 * @param: array ��ʼʱ��
 * @param: array ����ʱ�䰴
 * @param: integer ȡС����λ
 * @return: double
 * @create: 2014-10-09
**/
function time_diff($time_form, $time_to=NULL, $point=10){
    $return = 0.0;
    is_empty($time_to) && $time_to = get_cur_time();
    is_type($time_form, 'string') && $time_form = time_array($time_form);
    if(is_empty($time_form) || !is_type($time_form, 'array')) return FALSE;
    $return = ($time_to[0]-$time_form[0])+($time_to[1]-$time_form[1]);
    return sprintf("%.".$point."f", $return);
}
?>