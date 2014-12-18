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
 * @description: 检测变量是否为空
 * @param: mixed 需要判断变量
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
 * @description: 检测变量类型是否为指定
 * @param: mixed 需要判断变量
 * @param: string|array 判断的类别
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
 * @description: 判断是否存在[变量、类、接口、类方法、函数、文件、路径]
 * @param: mixed 需要判断变量
 * @param: string 检测类型 default[var]
 * @param: object 类对象 default[NULL]
 * @return: boolean
 * @create: 2014-10-09
**/
function is_exists($var_name, $var_type='var', $object=NULL){
    $return = FALSE;
    switch(strtolower(trim($var_type))){
        case 'var'      : {$return = isset($var_name) ? TRUE : FALSE; break;}
        case 'file'     : {$return = file_exists($var_name) ? TRUE : FALSE; break;}
        case 'function' : {$return = function_exists($var_name) ? TRUE : FALSE; break;}
        case 'class'    : {$return = class_exists($var_name) ? TRUE : FALSE; break;}
        case 'interface': {$return = interface_exists($var_name) ? TRUE : FALSE; break;}
        case 'method'   : {$return = method_exists($object, $var_name) ? TRUE : FALSE; break;}
        case 'dir'      : {
                            $return = !is_file($var_name) ? TRUE : FALSE;
                            $return && $return = is_exists($var_name, 'file', $object);
                            break;
                        }
    }
    return $return;
}

/**
 * @name: is_include
 * @description: 文件是否被引入
 * @param: string 引入的文件全路径
 * @return: boolean
 * @create: 2014-10-09
**/
function is_include($include_file){
    $include_file = preg_replace_callback('/[\/\\\\]+/', function($match){return DIRECTORY_SEPARATOR;}, $include_file);
    return in_array($include_file, get_included_files(), TRUE) ? TRUE : FALSE;
}

/**
 * @name: is_foreach
 * @description: 检测是否允许foreach
 * @param: mixed 需要判断变量
 * @return: boolean
 * @create: 2014-10-09
**/
function is_foreach($var_name){
    if(is_array($var_name) && count($var_name) > 0) return TRUE;
    if(is_object($var_name)) return TRUE;
    return FALSE;
}

/**
 * @name: get_cur_time
 * @description: 获取当前时间
 * @param: boolean 返回是否字符串[FALSE]
 * @return: array
 * @create: 2014-10-09
**/
function get_cur_time($is_string=FALSE){
    $cur_time = microtime();
    return $is_string ? $cur_time : array(doubleval(substr($cur_time, 0, 10)), intval(substr($cur_time, 11, 10)));
}

/**
 * @name: time_array
 * @description: 时间字符串转换成时间数组
 * @param: string 时间字符串[microtime]
 * @return: array
 * @create: 2014-10-09
**/
function time_array($string){
    return array(doubleval(substr($string, 0, 10)), intval(substr($string, 11, 10)));
}

/**
 * @name: time_diff
 * @description: 计算时间差
 * @param: array 开始时间
 * @param: array 结束时间按
 * @param: integer 取小数点位
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

/**
 * @name: var_string
 * @description: 将变量转换成字符串
 * @param: mixed 变量
 * @return: string
 * @create: 2014-10-10
**/
function var_string($value){
    switch(strtolower(gettype($value))){
        case 'null'     : {return NULL; break;}
        case 'integer'  : {settype($value, 'string'); return $value; break;}
        case 'double'   : {settype($value, 'string'); return $value; break;}
        case 'string'   : {return '"'.$value.'"'; break;}
        case 'array'    : {
            $return = '';
            $i = 0;
            foreach($value as $key => $val){
                $return .= ($return == '' ? '' : ', ');
                $return .= (gettype($key) == 'integer' ? $key : '"'.$key.'"');
                $return .= ' => ';
                $tmp_type = gettype($val);
                $return .= ($tmp_type == 'array' ? var_string($val) : ($tmp_type == 'integer' ? $val : '"'.$val.'"'));
            }
            return 'Array('.$return.')';
        }
        case 'object'   : {settype($value, 'string'); return $value;}
        case 'resource' : {settype($value, 'string'); return $value;}
        case 'boolean'  : {return $value ? 'TRUE' : 'FALSE';}
    }
    return TRUE;
}

/**
 * @name: get_rand_string
 * @description: 获取随机字符串
 * @param: integer 随机字符的长度
 * @param: integer 随机字符的模式或制定的字符内容 default[7],1-15
 * @param: boolean 是否去除字符 default[FALSE] O,o,0
 * @return: string
 * @create: 2014-10-10
**/
function get_rand_string($leng, $type=7, $dark=FALSE){
    $tmp_array = array(
                '1' => 'ABCDEFGHIJKLMNOPQRSTUVWXYZ',
                '2' => 'abcdefghijklmnopqrstuvwxyz',
                '4' => '0123456789',
                '8' => '~!@$&()_+-=,./<>?;\'\\:"|[]{}`'
            );
    $return = $target_string = '';
    if(is_numeric($type)){
        $array = array();
        $bin_string = decbin($type);
        $bin_leng  = strlen($bin_string);
        for($i = 0; $i < $bin_leng; $i++) if($bin_string{$i} == 1) $array[] = pow(2, $bin_leng - $i - 1);
        if(in_array(1, $array, TRUE)) $target_string .= $tmp_array['1'];
        if(in_array(2, $array, TRUE)) $target_string .= $tmp_array['2'];
        if(in_array(4, $array, TRUE)) $target_string .= $tmp_array['4'];
        if(in_array(8, $array, TRUE)) $target_string .= $tmp_array['8'];
    }else{
        $target_string = $type;
    }
    $target_leng = strlen($target_string);
    while(strlen($return) < $leng){
        $tmp_string = substr($target_string, mt_rand(0, $target_leng), 1);
        $dark && $tmp_string = (in_array($tmp_string, array('0', 'O', 'o'))) ? '' : $tmp_string;
        $return .= $tmp_string;
    }
    return $return;
}

/**
 * @name: icon_var
 * @description: 转换字符串编码
 * @param: string 被转换的原字符串
 * @param: string 被转换的类型 default[GBK,UTF-8,I]
 * @return: string
 * @create: 2014-10-10
**/
function icon_var($string, $type='GBK,UTF-8,I'){
    $type_array = explode(',', $type);
    $type_leng = count($type_array);
    if($type_leng != 2 && $type_leng != 3) return FALSE;
    $form = strtoupper(trim($type_array[0]));
    $to = strtoupper(trim($type_array[1]));
    $prame = '';
    $type_leng == 3 && ($prame = '//'.(strtoupper(trim($type_array[2]))=='T'?'TRANSLIT':'IGNORE'));
    return iconv($form, $to.$prame, $string);
}

/**
 * @name: filter_string
 * @description: 过滤非安全字符
 * @param: mixed 被过滤的原字符串或数组
 * @return: mixed
 * @create: 2014-10-10
**/
function filter_string($string){
    if(is_empty($string)) return '';
    if(is_array($string)){
        foreach($string as $key => $val) $string[$key] = filter_string($val);
        return $string;
    }else{
        $string = preg_replace_callback("'<script[^>]*?>.*?</script>'si", function($match){return '';}, $string);
        $string = preg_replace_callback("'<[\/\!]*?[^<>]*?>'si", function($match){return '';}, $string);
        $string = preg_replace_callback("'([\r\n])[\s]+'", function($match){return $match[1];}, $string);
        $string = preg_replace_callback("'&(quot|#34);'i", function($match){return '"';}, $string);
        $string = preg_replace_callback("'&(amp|#38);'i", function($match){return '&';}, $string);
        $string = preg_replace_callback("'&(lt|#60);'i", function($match){return '<';}, $string);
        $string = preg_replace_callback("'&(gt|#62);'i", function($match){return '>';}, $string);
        $string = preg_replace_callback("'&(nbsp|#160);'i", function($match){return ' ';}, $string);
        $string = preg_replace_callback("'&(iexcl|#161);'i", function($match){return chr(161);}, $string);
        $string = preg_replace_callback("'&(cent|#162);'i", function($match){return chr(162);}, $string);
        $string = preg_replace_callback("'&(pound|#163);'i", function($match){return chr(163);}, $string);
        $string = preg_replace_callback("'&(copy|#169);'i", function($match){return chr(169);}, $string);
        $string = preg_replace_callback("'&#(\d+);'", function($match){return chr($match[1]);}, $string);
        return trim(addslashes(nl2br(stripslashes($string))));
    }
}

/**
 * @name: get_var_get
 * @description: GET方式获取表单数据
 * @param: string 表单name参数名称
 * @param: boolean 是否过滤字符串安全 default[TRUE]
 * @return: mixed
 * @create: 2014-10-10
**/
function get_var_get($var_name, $is_filter=TRUE){
    $return = isset($_GET[$var_name]) ? $_GET[$var_name] : NULL;
    if($is_filter && !is_empty($return)) $return = filter_string($return);
    return $return;
}

/**
 * @name: get_var_post
 * @description: POST方式获取表单数据
 * @param: string 表单name参数名称
 * @param: boolean 是否过滤字符串安全 default[TRUE]
 * @return: mixed
 * @create: 2014-10-10
**/
function get_var_post($var_name, $is_filter=TRUE){
    $return = isset($_POST[$var_name]) ? $_POST[$var_name] : NULL;
    if($is_filter && !is_empty($return)) $return = filter_string($return);
    return $return;
}

/**
 * @name: get_var_value
 * @description: 获取表单数据(GET 和 POST)
 * @param: string 表单name参数名称
 * @param: boolean 是否过滤字符串安全 default[TRUE]
 * @param: boolean 是否优先获取POST default[TRUE]
 * @return: mixed
 * @create: 2014-10-10
**/
function get_var_value($var_name, $is_filter=TRUE, $is_post=TRUE){
    $return = NULL;
    if($is_post){
        $return = get_var_post($var_name, $is_filter);
        $return === NULL && $return = get_var_get($var_name, $is_filter);
    }else{
        $return = get_var_get($var_name, $is_filter);
        $return === NULL && $return = get_var_post($var_name, $is_filter);
    }
    return $return;
}

/**
 * @name: get_var_name
 * @description: 获取变量的名字[引用变量返回数组]
 * @param: mixed 变量的值
 * @param: mixed 变量的作用域 default[GLOBALS]
 * @return: string
 * @create: 2014-10-10
**/
function get_var_name(&$var_name, $scope=NULL){
    $return = FALSE;
    is_empty($scope) && $scope = $GLOBALS;
    $tmp = $var_name;
    $var_name = 'varname_isexists_'.mt_rand();
    $return = array_keys($scope, $var_name, TRUE);
    $var_name = $tmp;
    (is_type($return, 'array') && count($return) == 1) && $return = $return[0];
    return $return;
}

/**
 * @name: get_ip
 * @description: 获取客户端IP地址
 * @return: string
 * @create: 2014-10-10
**/
function get_ip(){
    $ip = '';
    isset($_SERVER['HTTP_X_FORWARDED_FOR']) && $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    if(!is_empty($ip)) return $ip;
    isset($_SERVER['HTTP_CLIENT_IP']) && $ip = $_SERVER['HTTP_CLIENT_IP'];
    if(!is_empty($ip)) return $ip;
    isset($_SERVER['REMOTE_ADDR']) && $ip = $_SERVER['REMOTE_ADDR'];
    if(!is_empty($ip)) return $ip;
    $ip = getenv('HTTP_X_FORWARDED_FOR');
    if(!is_empty($ip)) return $ip;
    $ip = getenv('HTTP_CLIENT_IP');
    if(!is_empty($ip)) return $ip;
    $ip = getenv('REMOTE_ADDR');
    if(!is_empty($ip)) return $ip;
    return '0.0.0.0';
}

/**
 * @name: ip_long
 * @description: ip地址转成长整型
 * @param: string ip地址
 * @return: integer
 * @create: 2014-10-10
**/
function ip_long($ip){
    $return = 0;
    $tmp_array = explode('.', $ip);
    foreach($tmp_array as $key => $val) $return += intval($val)*pow(256, abs($key-3));
    return $return;
}

/**
 * @name: long_ip
 * @description: 长整型转成ip地址
 * @param: integer 数字
 * @return: string
 * @create: 2014-10-10
**/
function long_ip($long_ip){
    if($long_ip < 0 || $long_ip > 4294967295) return FALSE;
    $ip = '';
    for($i=3; $i>=0; $i--){
        $tmp_num = intval($long_ip / pow(256, $i));
        $ip .= $tmp_num;
        $long_ip -= $tmp_num * pow(256, $i);
        if($i > 0) $ip .= '.';
    }
    return $ip;
}

/**
 * @name: time_int
 * @description: 转换时间成整形
 * @param: string 被转换时间 [yyyy:mm:dd hh:ii:ss]
 * @return: integer
 * @create: 2014-10-10
**/
function time_int($time_string){
    $return = FALSE;
    if(preg_match('/^(\d{4})-(\d{1,2})-(\d{1,2}) (\d{1,2}):(\d{1,2}):(\d{1,2})$/', $time_string, $match)){
        if(!isset($match[3])) return FALSE;
        $return = mktime($match[4], $match[5], $match[6], $match[2], $match[3], $match[1]);
    }
    return $return;
}

/**
 * @name: file_size_string
 * @description: 计算文件格式单位
 * @param: integer 被转换的数字 字节[Byte]
 * @param: integer 小数点位数 default[2]2位小数点
 * @param: integer 进制单位大小 default[1024]
 * @param: integer 取整类型 default[0]0-四舍五入,1-向下取整,2-向上取整
 * @return: string
 * @create: 2014-10-10
**/
function file_size_string($file_size, $decim=2, $units=1024, $val_crf=0){
    $tmp_array = array('B', 'KB', 'MB', 'GB', 'TB', 'PB');
    $i = 1;
    $j = count($tmp_array);
    $decim_pow = pow(10, $decim);
    while($file_size >= pow($units, $i) && $i <= $j) ++$i;
    if($val_crf == 2){
        return ceil(($file_size/pow($units, $i-1))*$decim_pow)/$decim_pow.' '.$tmp_array[$i-1];
    }else if($val_crf == 1){
        return round(($file_size/pow($units, $i-1))*$decim_pow)/$decim_pow.' '.$tmp_array[$i-1];
    }else{
        return floor(($file_size/pow($units, $i-1))*$decim_pow)/$decim_pow.' '.$tmp_array[$i-1];
    }
}

/**
 * @name: hex_bin
 * @description: 十六进制转二进制
 * @param: string 被转换字符串
 * @return: string
 * @create: 2014-10-10
**/
function hex_bin($string){
    $return = '';
    $length = strlen($string);
    for($i = 0; $i < $length; $i += 2) $return .= pack('C', hexdec(substr($string, $i, 2)));
    return $return;
}

/**
 * @name: get_content
 * @description: 获取文件或远程地址的内容
 * @param: string 文件路径或远程地址
 * @param: boolean 是否是本地文件
 * @param: integer 读取行数 默认全部
 * @param: integer 读取开始行数 默认开始
 * @return: string
 * @create: 2014-10-10
**/
function get_content($url, $is_file=TRUE, $line=0, $start=0){
    $return = '';
    if($is_file && !is_exists($url, 'file')) return FALSE;
    if(!($fopen = fopen($url, 'r'))) return FALSE;
    $i = $j = 0;
    while($tmp_string = fgets($fopen)){
        if($i >= $start){
            if($line > 0 && $line <= $j) break;
            $return .= $tmp_string;
            $j++;
        }
        $i++;
    }
    if($fopen) fclose($fopen);
    return $return;
}

/**
 * @name: shift_right
 * @description: 无符号右移位
 * @param: integer 被移动值
 * @param: integer 移动的位数
 * @return: integer
 * @create: 2014-10-10
**/
function shift_right($var_int, $move_int){
    if($move_int <= 0) return $var_int;
    if($move_int >= 32) return 0;
    $var_int = decbin($var_int);
    $var_int_leng = strlen($var_int);
    if($var_int_leng > 32){
        $var_int = substr($var_int, $var_int_leng-32, 32);
    }elseif($var_int_leng < 32){
        $var_int = str_pad($var_int, 32, '0', STR_PAD_LEFT);
    }
    return bindec(str_pad(substr($var_int, 0, 32-$move_int), 32, '0', STR_PAD_LEFT));
}

/**
 * @name: shift_left
 * @description: 无符号左移位
 * @param: integer 被移动值
 * @param: integer 移动的位数
 * @return: integer
 * @create: 2014-10-10
**/
function shift_left($var_int, $move_int){
    if($move_int <= 0) return $var_int;
    if($move_int >= 32) return 0;
    $var_int = decbin($var_int);
    $var_int_leng = strlen($var_int);
    if($var_int_leng > 32){
        $var_int = substr($var_int, $var_int_leng-32, 32);
    }elseif($var_int_leng < 32){
        $var_int = str_pad($var_int, 32, '0', STR_PAD_LEFT);
    }
    return bindec(str_pad(substr($var_int, $move_int), 32, '0', STR_PAD_RIGHT));
}

/**
 * @name: get_array_num
 * @description: 返回数组的深度数
 * @param: array 检测的数组
 * @param: integer 当前计算深度 default[1]
 * @return: integer
 * @create: 2014-10-10
**/
function get_array_num($array, $i=1){
    if(!is_type($array, 'array')) return FALSE;
    $i = $i < 1 ? 1 : $i;
    $return = $i;
    if(!is_empty($array)){
        foreach($array as $val){
            if(is_type($val, 'array')){
                $return = max($i, $return, get_array_num($val, $i+1));
            }
        }
    }
    return $return;
}

/**
 * @name: get_array_sum
 * @description: 返回数组的全部个数
 * @param: array 检测的数组
 * @return: integer
 * @create: 2014-10-10
**/
function get_array_sum($array){
    if(!is_type($array, 'array')) return FALSE;
    $return = 1;
    if(!is_empty($array)){
        foreach($array as $val){
            if(is_type($val, 'array')){
                $return += get_array_sum($val);
            }
        }
    }
    return $return;
}

/**
 * @name: xml_array
 * @description: XML转成数组
 * @param: string Xml字符串
 * @param: boolean 是否启用 attribute default[FALSE]
 * @return: array
 * @create: 2014-10-10
**/
function xml_array($xml_string, $attribute=FALSE){
    $return = array();
    $search = $attribute ? '|<((\S+)(.*))\s*>(.*)</\1>|Ums' : '|<((\S+)().*)>(.*)</\1>|Ums';
    $xml_string = preg_replace_callback('|>\s*<|', function($match){return ">\n<";}, $xml_string);
    $xml_string = preg_replace_callback('|<\?.*\?>|', function($match){return '';}, $xml_string);
    $xml_string = preg_replace_callback('|<(\S+?)(.*)/>|U', function($match){return '<'.$match[1].$match[2].'></'.$match[1].'>';}, $xml_string);
    if(!preg_match_all($search, $xml_string, $match) || is_empty($match[1])) return $xml_string;
    foreach($match[1] as $key => $val){
        if(!isset($return[$val])) $return[$val] = array();
        $return[$val][] = xml_array($match[4][$key], $attribute);
    }
    return $return;
}

/**
 * @name: object_to_array
 * @description: 对象转成数组
 * @param: object 对象
 * @param: boolean 是否全部
 * @return: array
 * @create: 2014-10-10
**/
function object_to_array($object, $is_all=FALSE){
    $array = Array();
    $tmp_array = (Array)$object;
    foreach($tmp_array as $key => $val){
        if($is_all && is_object($val)){
            $array[preg_replace_callback('/^.+\0/', function($match){return '';}, $key)] = object_to_array($val, $is_all);
        }else{
            $array[preg_replace_callback('/^.+\0/', function($match){return '';}, $key)] = $val;
        }
    }
    return $array;
}

/**
 * @name: array_to_object
 * @description: 数组转成对象
 * @param: array 数组
 * @param: boolean 是否全部
 * @return: object
 * @create: 2014-10-10
**/
function array_to_object($array, $is_all=FALSE){
    $object = new stdclass();
    if(!is_empty($array)){
        foreach($array as $key => $val){
            if($is_all && is_array($val)){
                $object -> $key = array_to_object($val, $is_all);
            }else{
                $object -> $key = $val;
            }
        }
    }
    return $object;
}

/**
 * @name: string_bin
 * @description: 字符串转成二进制
 * @param: string 普通字符串
 * @return: string
 * @create: 2014-10-10
**/
function string_bin($string){
    if(is_empty($string) || !is_string($string)) return NULL;
    $hex_string = unpack('H*', $string);
    $hex_string = str_split($hex_string[1], 1);
    $bin_string = '';
    foreach($hex_string as $val){
        $bin_string .= str_pad(base_convert($val, 16, 2), 4, '0', STR_PAD_LEFT);
    }
    return $bin_string;
}

/**
 * @name: bin_string
 * @description: 二进制转成字符串
 * @param: string 二进制字符串
 * @return: string
 * @create: 2014-10-10
**/
function bin_string($bin_string){
    if(is_empty($bin_string) || !is_string($bin_string)) return NULL;
    $hex_array = str_split($bin_string, 4);
    $string = '';
    foreach($hex_array as $val){
        $string .= base_convert($val, 2, 16);
    }
    $string = pack('H*', $string);
    return $string;
}

/**
 * @name: to_gethostbyaddr
 * @description: 根据IP解析主机名
 * @param: string IP地址
 * @return: string
 * @create: 2014-10-10
**/
function to_gethostbyaddr($addr_ip){
    static $global_to_gethostbyaddr = array();    //暂存的gethostbyaddr数据array('ip' => 'hostname')
    if(!is_array($global_to_gethostbyaddr) || !isset($global_to_gethostbyaddr[$addr_ip])){
        $global_to_gethostbyaddr[$addr_ip] = gethostbyaddr($addr_ip);
    }
    return $global_to_gethostbyaddr[$addr_ip];
}

/**
 * @name: to_gethostbyname
 * @description: 根据主机名解析IP
 * @param: string 主机名
 * @return: string
 * @create: 2014-10-10
**/
function to_gethostbyname($addr){
    $addr = strtolower($addr);
    $addr_md5 = md5($addr);
    static $global_to_gethostbyname = array();    //暂存的gethostbyname数据array('md5(strtolower(hostname))' => 'ip')
    if(!is_array($global_to_gethostbyname) || !isset($global_to_gethostbyname[$addr_md5])){
        $global_to_gethostbyname[$addr_md5] = gethostbyname($addr);
    }
    return $global_to_gethostbyname[$addr_md5];
}

/**
 * @name: filter_name
 * @description: 过滤不合法的文件和文件夹名称
 * @param: string 文件和文件夹名称
 * @return: string
 * @create: 2014-10-10
**/
function filter_name($file_folder){
    $file_folder = preg_replace_callback(array("'\\\\'", "'\/'", "':'", "'\*'", "'\?'", "'\"'", "'<'", "'>'", "'\|'", "' '"), function($match){return '';}, $file_folder);
    return trim($file_folder);
}

/**
 * @name: ip_cidr
 * @description: ipd地址转成cidr格式
 * @param: string 开始IPV4地址[or ip_long数字]
 * @param: string 结束IPV4地址[or ip_long数字]
 * @return: array
 * @create: 2014-10-10
**/
function ip_cidr($ip_start, $ip_end){
    $bit = 32;
    $start = is_numeric($ip_start) ? $ip_start : ip_long($ip_start);
    $end = is_numeric($ip_end) ? $ip_end : ip_long($ip_end);
    $return = array();
    while($end >= $start){
        $max_size = $bit;
        while($max_size > 0 && ($start == ($start & (4294967296 - pow(2, 33 - $max_size))))) $max_size--;
        $max_diff = floor($bit - floor(log($end - $start + 1)/log(2)));
        $max_size = max($max_size, $max_diff);
        $return[] = long2ip($start).'/'.$max_size;
        $start += pow(2, ($bit - $max_size));
    }
    return $return;
}

/**
 * @name: get_path
 * @description: 计算相对路径
 * @param: string 源路径
 * @param: string 目标路径
 * @return: string
 * @create: 2014-10-10
**/
function get_path($src_path, $des_path){
    $src_path = trim(preg_replace_callback("/[\/\\\\ ]{1,}/", function($match){return '/';}, $src_path));
    if($src_path == '') return FALSE;
    $des_path = trim(preg_replace_callback("/[\/\\\\ ]{1,}/", function($match){return '/';}, $des_path));
    if($des_path == '') return FALSE;
    $isrelative = FALSE;
    if($src_path{0} == '/' && $src_path{0} == $des_path{0}) $isrelative = TRUE; //根路径开始
    $src_array = explode('/', dirname($src_path));
    $des_array = explode('/', $des_path);
    if(!$isrelative && $src_array[0] == $des_array[0]) $isrelative = TRUE;
    if(!$isrelative) return $des_path;
    $isfound = FALSE;
    $src_res = $des_res = '';
    foreach($src_array as $key => $val){
        if(!$isfound && (!isset($des_array[$key]) || $des_array[$key] != $val)) $isfound = TRUE;
        if($isfound){
            $src_res .= '../';
        }else{
            unset($des_array[$key]);
        }
    }
    return $src_res.implode('/', $des_array);
}

/**
 * @name: up_file
 * @description: 上传文件
 * @param: array 被上传的文件数组信息
 * @param: string 上传文件的目录路径和文件名称
 * @param: array 允许、不允许上传的文件类型[NULL不限制,array('jpg|png', 'php')]
 * @param: integer 允许上传的大小[字节,-1不限制]
 * @param: string 上传文件的目录路径和文件名称备用
 * @param: string 上传文件的后缀名[default无,AUTO-自动带点]
 * @return: string[A-不允许类型,B-拒绝类型文件,S-超过大小,F-文件存在,T-备用文件存在,N(false)-失败,Y-成功]
 * @create: 2014-10-10
**/
function up_file($files, $dest_file, $allow=NULL, $size=-1, $filet=NULL, $annx=NULL){
    if(is_empty($files) || !is_type($files, 'array'))return FALSE;
    if(is_empty($dest_file) || !is_type($dest_file, 'string'))return FALSE;
    $up_size = intval($files['size']);
    $up_type = trim($files['type']);
    $up_name = trim($files['name']);
    $up_tmp_name = trim($files['tmp_name']);
    $up_name_annx = strtolower(substr($up_name, strrpos($up_name, '.')+1));
    if(!is_empty($annx)){
        if(strtoupper(substr($annx, 0, 4)) == 'AUTO'){
            if($annx{4} == '+'){
                $return = '.'.$up_name_annx.substr($annx, 5);
            }else{
                $return = '.'.$up_name_annx;
            }
            $dest_file .= $return;
        }else{
            $dest_file .= $annx;
        }
    }
    if(file_exists($dest_file)){
        if(is_empty($filet)){
            return 'F';
        }else{
            if(file_exists($filet) && $dest_file != $filet) return 'T';
            $dest_file = $filet;
        }
    }
    if($size >= 0 && $up_size > $size) return 'S';
    if(!is_empty($allow)){
        if(isset($allow[0]) && !is_empty($allow[0])){   //允许
            if($allow[0] != '*'){
                $tmp = explode('|', $allow[0]);
                $rs = FALSE;
                if(!is_empty($tmp)){
                    foreach($tmp as $val){
                        if($val=='*' || in_array($up_name_annx, $tmp)){$rs = TRUE; break;}
                    }
                }
                if(!$rs){return 'A';}
            }
        }
        if(isset($allow[1]) && !is_empty($allow[1])){   //拒绝
            $tmp = explode('|', $allow[1]);
            $rs = FALSE;
            if(!is_empty($tmp)){
                foreach($tmp as $val){
                    if(in_array($up_name_annx, $tmp)){ $rs = TRUE; break;}
                }
            }
            if($rs){return 'B';}
        }
    }
    if(move_uploaded_file($up_tmp_name, $dest_file)){
        if(isset($return)){
            return $return;
        }else{
            return 'Y';
        }
    }else{
        return 'N';
    }
}

/**
 * @name: read_file_dir
 * @description: 读取文件目录
 * @param: string 读取的目录
 * @param: integer 读取的目录的深度 [-1读取所有, default:-1]
 * @param: array 上次的目录[default-NULL] ***请不要提供任何参数***
 * @return: array
 * @create: 2014-10-10
**/
function read_file_dir($folder_url, $level=-1, &$array=NULL){
    $suffix = substr($folder_url, strlen($folder_url)-1, 1);
    if($suffix != '/' && $suffix != "\\") return FALSE;
    if(!is_dir($folder_url)) return FALSE;
    if(!$dir_handle = opendir($folder_url)) return FALSE;
    $return = array();
    $folder_size = 0;
    while($folder_file = readdir($dir_handle)){
        if($folder_file == '.' || $folder_file == '..' || strstr($folder_file, '.svn')) continue;
        $file_stat = stat($folder_url.$folder_file);
        if(is_dir($folder_url.$folder_file)){
            $return['folder'][] = array(
                'name' => $folder_file,
                'url' => $folder_url.$folder_file.$suffix,
                'size' => '0 B',
                'time' => date('Y-n-d H:i:s',$file_stat['mtime']),
                'mtime' => $file_stat['mtime']
            );
        }else{
            if(!is_empty($array)) $folder_size += $file_stat['size'];
            $return['file'][] = array(
                'name' => $folder_file,
                'url' => $folder_url.$folder_file,
                'size' => file_size_string($file_stat['size']),
                'time' => date('Y-n-d H:i:s',$file_stat['mtime']),
                'mtime' => $file_stat['mtime']
            );
        }
    }
    !is_empty($dir_handle) && closedir($dir_handle);
    if($level >= 0 || $level == -1){
        if(!is_empty($array)){
            $array['size'] = file_size_string($folder_size);
            if($level == 0) return $return;
        }
        if($level != -1) --$level;
        if(isset($return['folder']) && !is_empty($return['folder'])) foreach($return['folder'] as $key => $val) $return['folder'][$key]['ls'] = read_file_dir($val['url'], $level, $return['folder'][$key]);
    }
    return $return;
}

/**
 * @name: get_url_info
 * @description: 执行http和https
 * @param: string URL地址
 * @param: array 自定义访问参数 [default-array()]
 * @param: array 设定访问参数 [default-array()]
 * @return: array
 * @create: 2014-10-10
**/
function get_url_info($url, $set_info_zdy=array(), $prame=array()){
    $http = trim($url);
    $host = '';
    $to_url = '';
    $return = array(
        'url' => '',                        //最后一个有效的url地址
        'ip' => '',                         //最后一个有效的ip地址
        'content_type' => '',               //最后一个文档类型
        'http_code' => 0,                   //最后一个收到的HTTP代码
        'header_size' => 0,                 //header部分的大小
        'request_size' => 0,                //在HTTP请求中有问题的请求的大小
        'filetime' => -1,                   //远程获取文档的时间(时间戳),无法获取则返回值为-1
        'ssl_verify_result' => 0,           //结果的ssl认证所要求的核查设置
        'redirect_count' => 0,              //重定向次数
        'total_time' => 0,                  //最后一次传输所消耗的时间
        'namelookup_time' => 0,             //域名解析所消耗的时间
        'connect_time' => 0,                //建立连接所消耗的时间
        'pretransfer_time' => 0,            //从建立连接到准备传输所使用的时间
        'size_upload' => 0,                 //上传数据量的总值
        'size_download' => 0,               //下载数据量的总值
        'speed_download' => 0,              //平均下载速度
        'speed_upload' => 0,                //平均上传速度
        'download_content_length' => -1,    //从Content-Length:中读取的下载长度
        'upload_content_length' => -1,      //上传内容大小的说明
        'starttransfer_time' => 0,          //从建立连接到传输开始所使用的时间
        'redirect_time' => 0,               //在事务传输开始前重定向所使用的时间
        'certinfo' => array(),              //证书信息
        'errno' => 0,                       //错误代号
        'error' => '',                      //错误消息
        'head' => '',                       //head
        'body' => '',                       //body
        'charset' => '',                    //charset
        'protocol' => '',                   //protocol
        'host' => '',                       //host
        'port' => '',                       //port
        'RESULT' => FALSE                   //结果
    );
    $set_info = array();                    //设置初始信息
    $set_info['IS_NOBODY'] = FALSE;         //是否不获取内容
    $set_info['IS_HEADER'] = FALSE;         //是否输出头信息
    $set_info['IS_FOLLOWLOCATION'] = TRUE;  //是否允许重定向
    $set_info['IS_FOLLOWLOCATION_NUM'] = 20;//重定向最大次数
    $set_info['IS_TIMEOUT'] = 60;           //超时
    $set_info['IS_PORT'] = 80;              //端口
    $set_info['IS_METHOD'] = 'GET';         //GET POST HEAD
    $set_info['REDIRECT_COUNT'] = 0;        //重定向次数
    $set_info['REDIRECT_TIME'] = 0;         //重定向时间
    $set_info['BIND_IP'] = '';              //BINDIP
    if(isset($set_info_zdy) && is_array($set_info_zdy) && count($set_info_zdy) > 0)foreach($set_info_zdy as $key => $val){
        switch($key){
            case 'IS_NOBODY' : {$set_info['IS_NOBODY'] = $val ? TRUE : FALSE; break;}
            case 'IS_HEADER' : {$set_info['IS_HEADER'] = $val ? TRUE : FALSE; break;}
            case 'IS_FOLLOWLOCATION' : {$set_info['IS_FOLLOWLOCATION'] = $val ? TRUE : FALSE; break;}
            case 'IS_FOLLOWLOCATION_NUM' : {$set_info['IS_FOLLOWLOCATION_NUM'] = intval($val); break;}
            case 'IS_TIMEOUT' : {$set_info['IS_TIMEOUT'] = intval($val); break;}
            case 'IS_PORT' : {$set_info['IS_PORT'] = intval($val); break;}
            case 'IS_METHOD' : {$set_info['IS_METHOD'] = strtoupper($val); $set_info['IS_METHOD'] = (!in_array($set_info['IS_METHOD'], array('GET', 'POST', 'HEAD'), true)?'GET':$set_info['IS_METHOD']); break;}
            case 'REDIRECT_COUNT' : {$return['redirect_count'] = $set_info['REDIRECT_COUNT'] = doubleval($val); break;}
            case 'REDIRECT_TIME' : {$return['redirect_time'] = $set_info['REDIRECT_TIME'] = doubleval($val); break;}
            case 'BIND_IP' : {$set_info['BIND_IP'] = $val; break;}
        }
    }
    if(!$set_info['IS_FOLLOWLOCATION']){        //不允许重定向
        $set_info['IS_FOLLOWLOCATION_NUM'] = 0;
        $return['redirect_count'] = $set_info['REDIRECT_COUNT'] = 0;
    }
    $set_info['IS_NOBODY'] = $set_info['IS_METHOD'] == 'HEAD' ? TRUE : FALSE;
    $posint = strpos($http, '://');
    $protocol = 'HTTP://';                  //默认HTTP://
    if($posint === FALSE){
        $http = $protocol.$http;
    }else{
        $posint = intval($posint)+3;
        $protocol = strtoupper(substr($http, 0, $posint));
        $http = preg_replace_callback('/[\/]+/', function($match){return '/';}, substr($http, $posint));
        $http = $protocol.(isset($http{0}) && $http{0} == '/' ? substr($http, 1) : $http);
    }
    if(!preg_match("/(.*:\/\/)([^$][^\/]*)(.*)/", $http, $match)) $match = array('', '', '');
    if($match[2] == ''){                    //非法重新检测
        $http = $protocol.$http;
        if(!preg_match("/(.*:\/\/)([^$][^\/]*)(.*)/", $http, $match)) $match = array('', '', '');
        $host = $match[2];
    }else{
        $host = $match[2];
    }
    $is_port = FALSE;
    $posint = strpos($host, ':');           //识别端口[0-65535]
    if($posint > 0 && $posint < strlen($host)){
        $port_tmp = intval(substr($host, $posint+1));
        if($port_tmp > 0 && $port_tmp < 65536){
            $is_port = TRUE;
            $set_info['IS_PORT'] = $port_tmp;
        }
        $host = substr($host, 0, $posint);
    }
    if(check_data($host, 'cndomain')){
        $host = get_init('idna_convert') -> encode($host);
    }
    $to_url = $match[3];
    if($to_url == ''){                      //处理请求地址默认[/]
        $to_url = '/';
    }else{
        $to_url = preg_replace_callback('/[\/]+/', function($match){return '/';}, $to_url);
        if($to_url{0} != '/') $to_url = '/'.$to_url;
    }
    $return['url'] = $http;
    $return['protocol'] = $protocol;
    $return['host'] = $host;
    $Ttime = $start_time = get_cur_time();
    if(isset($prame['IP']) && !is_empty($prame['IP']) && preg_match("/\b(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\b/", trim($prame['IP']))){
        $ip = trim($prame['IP']);
        $prame['IP'] = '';                  //清空IP处理重定向时候误导
    }else{
        $ip = trim(to_gethostbyname($host));        //解析域名
        $return['namelookup_time'] = sprintf("%0.6f", time_diff($start_time));
        $return['namelookup_time'] = $return['namelookup_time'] == 0?0:$return['namelookup_time'];
        if(!preg_match("/\b(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\b/", $ip)){
            $return['total_time'] = $return['namelookup_time'] = 0;
            $return['errno'] = $return['http_code'] = 6;
            $return['error'] = 'Couldn\'t resolve host.';
            return $return;
        }
    }
    if($ip == '127.0.0.1' || $ip == '255.255.255.255'){     //不合常理的IP
        $return['total_time'] = $return['namelookup_time'] = 0;
        $return['errno'] = $return['http_code'] = 6;
        $return['error'] = 'Couldn\'t resolve host.-T';
        return $return;
    }
    $return['ip'] = $ip;
    if($protocol == 'HTTPS://'){            //支持ssl协议
        if(!$is_port) $set_info['IS_PORT'] = 443;
        if($set_info['BIND_IP'] != ''){
            $ip = 'ssl://'.$host.':'.$set_info['IS_PORT'];
        }else{
            $ip = 'ssl://'.$host;
        }
    }
    $return['port'] = $set_info['IS_PORT'];
    $body_info = '';
    $start_time = get_cur_time();
    if($set_info['BIND_IP'] != ''){
        $context = stream_context_create(array('socket' => array('bindto' => $set_info['BIND_IP'])));
        $fsocktp = stream_socket_client($ip, $errno, $errstr, 30, STREAM_CLIENT_CONNECT, $context);
    }else{
        $fsocktp = fsockopen($ip, $set_info['IS_PORT'], $errno, $errstr, 30);
    }
    stream_set_blocking($fsocktp, 1);
    $return['connect_time'] = sprintf("%0.6f", time_diff($start_time));
    if(!$fsocktp){
        $return['total_time'] = sprintf("%0.6f", time_diff($Ttime));
        $return['errno'] = $return['http_code'] = 7;
        $return['error'] = 'Failed to connect() to host or proxy.';
        return $return;
    }else{
        !isset($prame['VERSION']) && $prame['VERSION'] = '1.1';
        $version = $prame['VERSION'] == '1.0' ? '1.0' : '1.1';
        $heads = $set_info['IS_METHOD']." ".$to_url." HTTP/".$version."\r\nHost: ".$host."\r\n";
        if(isset($prame['COOKIE']) && !is_empty($prame['COOKIE'])){
            $heads .= 'Cookie: '.trim($prame['COOKIE'])."\r\n";         //发送cookie
        }
        if(isset($prame['HEADER']) && !is_empty($prame['HEADER']) && is_type($prame['HEADER'], 'array')){
            foreach($prame['HEADER'] as $val){
                if(!is_empty(trim($val)))$heads .= trim($val)."\r\n";       //发送用户的Header
            }
        }
        if(isset($prame['USER']) && !is_empty($prame['USER'])){
            $heads .= 'Authorization: BASIC '.base64_encode($prame['USER'].':'.$prame['PASS'])."\r\n"; //发送验证信息
        }
        if($set_info['IS_METHOD'] == 'POST' && $prame['POST_DATA'] != ''){  //发送POST数据
            $heads .= 'Content-type: application/x-www-form-urlencoded; charset=UTF-8'."\r\n";
            $heads .= 'Content-Length: '.strlen($prame['POST_DATA'])."\r\n";
            $heads .= "Connection:close\r\n";
            $heads .= "\r\n".$prame['POST_DATA']."\r\n\r\n";
        }else{
            $heads .= "Connection:close\r\n\r\n";
        }
        fwrite($fsocktp, $heads);
        $return['request_size'] = strlen($heads);
        $return['pretransfer_time'] = sprintf("%0.6f", time_diff($start_time));
        $ishead = TRUE;
        $ischunked = FALSE;
        $head_i = 0;
        $head_info = '';                        //接收到的头信息
        $max_count = 3;                         //允许重复读取次数
        stream_set_timeout($fsocktp, $set_info['IS_TIMEOUT']);      //设置超时时间
        $fgets = 'fgets';
        while(!$content = $fgets($fsocktp)){    //读取失败
            if(time_diff($start_time) > 30 || $max_count < 1){
                fclose($fsocktp);
                $return['total_time'] = sprintf("%0.6f", time_diff($Ttime));
                $return['errno'] = $return['http_code'] = 52;
                $return['error'] = 'Return Empty.';
                return $return;
            }else{
                $max_count--;
            }
        }
        $head_array = explode(' ', trim($content));
        if(count($head_array) > 1){
            $return['http_pro'] = trim($head_array[0]);//HTTP/1.1
            $return['http_code'] = trim($head_array[1]);
        }
        $return['starttransfer_time'] = sprintf("%0.6f", time_diff($start_time));
        $return['total_time'] = sprintf("%0.6f", time_diff($Ttime));
        $head_info .= $content;
        $head_i++;
        $next_leng = 0;             //分段HTTP/1.1下次剩余内容长度
        $head_to_url = '';          //重定向地址
        while($content = $fgets($fsocktp)){
            $tinfo = stream_get_meta_data($fsocktp);
            if(isset($tinfo['timed_out']) && $tinfo['timed_out']){  //每次检测超时
                fclose($fsocktp);
                $return['errno'] = $return['http_code'] = 28;
                $return['error'] = 'Operation timeout.';
                return $return;
            }
            if(trim($content) == ''){       //头信息接收完成
                if($head_to_url != ''){
                    fclose($fsocktp);
                    return get_url_info($head_to_url, $set_info, $prame);   //执行重定向
                }
                $ishead = FALSE;
                continue;
            }
            if($ishead){
                $tmp = explode(':', $content);
                if(!$ischunked && strtoupper(trim($tmp[0])) == 'TRANSFER-ENCODING' && strpos(strtolower($tmp[1]), 'chunked') !== FALSE) $ischunked = TRUE;  //分块传输
                if(!in_array($tmp[0], array('Transfer-Encoding'), TRUE)){
                    $head_info .= $content;
                    if(strtolower(trim($tmp[0])) == 'location' && in_array($return['http_code'], array(301, 302)) && $set_info['IS_FOLLOWLOCATION']){       //重定向 允许
                        if($set_info['IS_FOLLOWLOCATION_NUM']-- < 1){                       //超过最大重定向
                            fclose($fsocktp);
                            $return['total_time'] = sprintf("%0.6f", time_diff($Ttime));
                            $return['errno'] = $return['http_code'] = 47;
                            $return['error'] = 'Too many redirects.';
                            return $return;
                        }
                        $set_info['REDIRECT_COUNT']++;                                      //重定向次数+
                        $set_info['REDIRECT_TIME'] = sprintf("%0.6f", time_diff($start_time));      //重定向时间
                        unset($tmp[0]);
                        $tmpval = trim(implode(':', $tmp));
                        $head_to_url = trim(strpos($tmpval, '://') === FALSE ? $protocol.$host.($tmpval[0]=='/'?'':'/').$tmpval : $tmpval);
                    }else{
                        if(strtolower(trim($tmp[0])) == 'set-cookie'){  //记录Cookie
                            unset($tmp[0]);
                            $tmpval = trim(implode(':', $tmp));
                            if(!isset($prame['COOKIE']))$prame['COOKIE']='';
                            $prame['COOKIE'] .= ($prame['COOKIE']==''?'':'; ').substr($tmpval, 0, strpos($tmpval, ';'));
                        }else if(strtolower(trim($tmp[0])) == 'p3p'){   //记录P3P
                            unset($tmp[0]);
                            $tmpval = trim(implode(':', $tmp));
                            if(!isset($prame['P3P']))$prame['P3P']='';
                            $prame['P3P'] .= ($prame['P3P']==''?'':'; ').substr($tmpval, 0, strpos($tmpval, ';'));
                        }
                    }
                }else{
                    $head_i++;
                }
            }else{
                if($set_info['IS_NOBODY']) break;
                if($next_leng > 1){
                    $next_leng -= strlen($content);
                    $body_info .= $content;
                    continue;
                }else{
                    if($ischunked){
                        $next_leng = hexdec(trim($content));
                        if($next_leng < 1) break;
                        continue;
                    }else{
                        $body_info .= $content;
                    }
                }
            }
        }   //End while
        fclose($fsocktp);
        $fsocktp = NULL;
        $return['head'] = $head_info;
        $return['body'] = $body_info;
        $return['COOKIE'] = isset($prame['COOKIE']) ? $prame['COOKIE'] : '';
        $return['total_time'] = sprintf("%0.6f", time_diff($Ttime));
        $return['header_size'] = strlen($head_info)+($head_i*2);
        $head_infoArray = explode("\n", $head_info);
        unset($head_infoArray[0]);
        if(count($head_infoArray) > 0)foreach($head_infoArray as $val){
            $tmp = explode(':', $val);
            if(count($tmp) < 1) continue;
            $tmpkey = $tmp[0];
            unset($tmp[0]);
            $tmpval = implode(':', $tmp);
            switch(strtolower($tmpkey)){
                case 'content-type' : {
                    $return['content_type'] = $tmpval;
                    if(preg_match("/charset=([^;=\/\s]*)/i", $tmpval, $match)) $return['charset'] = $match[1];
                    BREAK;
                }
                case 'content-length' : {$return['download_content_length'] = intval($tmpval); break;}
            }
        }
        if(!$set_info['IS_NOBODY'] && $return['download_content_length'] == -1) $return['download_content_length'] = strlen($body_info);
    }
    if($return['http_code'] >= 100 && $return['http_code'] < 400) $return['RESULT'] = TRUE;
    return $return;
}

/**
 * @name: check_data
 * @description: 检测数据规则
 * @param: string 被检测的原字符串
 * @param: string 被检测的类型
 * @return: boolean
 * @create: 2014-10-10
**/
function check_data($string, $type='email'){
    $return = FALSE;
    switch($type){
        case 'email'        : {$return = preg_match("/^(\w+[-+.]*\w+)*@(\w+([-.]*\w+)*\.\w+([-.]*\w+)*)$/", $string); break;}
        case 'http'         : {$return = preg_match("/^http:\/\/[A-Za-z0-9-]+\.[A-Za-z0-9]+[\/=\?%\-&_~`@[\]\':+!]*([^<>\"])*$/", $string); break;}
        case 'qq'           : {$return = preg_match("/^[1-9]\d{4,11}$/", $string); break;}
        case 'post'         : {$return = preg_match("/^[1-9]\d{5}$/", $string); break;}
        case 'idnum'        : {$return = preg_match("/^\d{15}(\d{2}[A-Za-z0-9])?$/", $string); break;}
        case 'china'        : {$return = preg_match("/^[".chr(0xa1)."-".chr(0xff)."]+$/", $string); break;} //GBK中文
        case 'english'      : {$return = preg_match("/^[A-Za-z]+$/", $string); break;}
        case 'mobile'       : {$return = preg_match("/^((\(\d{3}\))|(\d{3}\-))?((13)|(14)|(15)|(17)|(18)){1}\d{9}$/", $string); break;}
        case 'phone'        : {$return = preg_match("/^((\(\d{3}\))|(\d{3}\-))?(\(0\d{2,3}\)|0\d{2,3}-)?[1-9]\d{6,7}$/", $string); break;}
        case 'safe'         : {$return = preg_match("/^(([A-Z]*|[a-z]*|\d*|[-_\~!@#\$%\^&\*\.\(\)\[\]\{\}<>\?\\\/\'\"]*)|.{0,5})$|\s/", $string) != 0 ? TRUE : FALSE; break;}
        case 'age'          : {$return = (preg_match("/^(-{0,1}|\+{0,1})[0-9]+(\.{0,1}[0-9]+)$/", $string) && intval($string) <= 130 && intval($string) >= 12) ? TRUE : FALSE; break;}
        case 'eng_num'      : {$return = preg_match("/^[A-Za-z0-9]+$/", $string); break;}
        case 'password'     : {$return = (preg_match("/^[A-Za-z0-9]+$/", $string) && strlen($string) <= 32 && strlen($string) >= 6) ? TRUE : FALSE; break;}
        case 'datetime'     : {$return = preg_match('/^[\d]{4}-[\d]{1,2}-[\d]{1,2}\s[\d]{1,2}:[\d]{1,2}:[\d]{1,2}$/', $string); break;}
        case 'datetimes'    : {$return = preg_match('/^[\d]{4}-[\d]{2}-[\d]{2}\s[\d]{2}:[\d]{2}:[\d]{2}$/', $string); break;}
        case 'date'         : {$return = preg_match('/^[\d]{4}-[\d]{1,2}-[\d]{1,2}$/', $string); break;}
        case 'dates'        : {$return = preg_match('/^[\d]{4}-[\d]{2}-[\d]{2}$/', $string); break;}
        case 'time'         : {$return = preg_match('/^[\d]{1,2}:[\d]{1,2}:[\d]{1,2}$/', $string); break;}
        case 'times'        : {$return = preg_match('/^[\d]{2}:[\d]{2}:[\d]{2}$/', $string); break;}
        case 'ip'           : {$return = preg_match("/^\b(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\b$/", $string); break;}
        case 'incchinese'   : {$return = preg_match('/[\x{4e00}-\x{9fa5}]+/u', $string); break;} //是否包含中文
        case 'plusnum'      : {$return = preg_match('/^[1-9]*[1-9][0-9]*$/', $string); break;} //是否是正整数
        case 'hostrecord'   : {$return = preg_match('/^[A-Z_a-z0-9][A-Za-z0-9-]+(\.[A-Za-z0-9-_]+)*$/', $string); break;} //正确的主机记录,english
        case 'cnhostrecord' : {$return = preg_match('/^[_a-zA-Z0-9]*([\x{4e00}-\x{9fa5}]*[-a-zA-Z0-9\.]*)+[a-zA-Z0-9_]$/iu', $string); break;} //正确的主机记录,english chinese
        case 'domain'       : {$return = preg_match('/^[A-Za-z0-9][A-Za-z0-9-]+(\.[A-Za-z0-9-]+){1,3}$/', $string); break;} //是否是域名
        case 'cndomain'     : {$return = (preg_match('/[\x{4e00}-\x{9fa5}]+/u', $string) && preg_match('/^([-a-zA-Z0-9\.]*[\x{4e00}-\x{9fa5}]*[-a-zA-Z0-9\.]*)+\.(中国|公司|网络|CN|COM|NET)$/iu', $string)) ? TRUE : FALSE; break;}  //是否中文域名
        case 'mac'          : {$return = preg_match('/^[a-fA-F\d]{2}:[a-fA-F\d]{2}:[a-fA-F\d]{2}:[a-fA-F\d]{2}:[a-fA-F\d]{2}:[a-fA-F\d]{2}$/', $string); break;}
        case 'ipv6'         : {$return = preg_match('/^\s*((([0-9A-Fa-f]{1,4}:){7}([0-9A-Fa-f]{1,4}|:))|(([0-9A-Fa-f]{1,4}:){6}(:[0-9A-Fa-f]{1,4}|((25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)(\.(25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3})|:))|(([0-9A-Fa-f]{1,4}:){5}(((:[0-9A-Fa-f]{1,4}){1,2})|:((25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)(\.(25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3})|:))|(([0-9A-Fa-f]{1,4}:){4}(((:[0-9A-Fa-f]{1,4}){1,3})|((:[0-9A-Fa-f]{1,4})?:((25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)(\.(25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3}))|:))|(([0-9A-Fa-f]{1,4}:){3}(((:[0-9A-Fa-f]{1,4}){1,4})|((:[0-9A-Fa-f]{1,4}){0,2}:((25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)(\.(25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3}))|:))|(([0-9A-Fa-f]{1,4}:){2}(((:[0-9A-Fa-f]{1,4}){1,5})|((:[0-9A-Fa-f]{1,4}){0,3}:((25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)(\.(25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3}))|:))|(([0-9A-Fa-f]{1,4}:){1}(((:[0-9A-Fa-f]{1,4}){1,6})|((:[0-9A-Fa-f]{1,4}){0,4}:((25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)(\.(25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3}))|:))|(:(((:[0-9A-Fa-f]{1,4}){1,7})|((:[0-9A-Fa-f]{1,4}){0,5}:((25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)(\.(25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3}))|:)))(%.+)?\s*$/', $string); break;}
    }
    gettype($return) == 'integer' && $return = $return == 0 ? FALSE : TRUE;
    return $return;
}

/**
 * @name: time_string
 * @description: 秒数转时间，格式[1年1月1天1时1分1秒]
 * @param: integer 秒数
 * @return: string
 * @create: 2014-11-11
**/
function time_string($sec_time){
    $tmp_array = array('年', '月', '周', '天', '时', '分', '秒');
    $time_array = array(31536000, 2628000, 604800, 86400, 3600, 60, 1);
    $return = '';
    $sec_time = intval($sec_time);
    if($sec_time == 0) return $return;
    if($sec_time < 0) $sec_time = abs($sec_time);
    foreach($time_array as $key => $val){
        if($sec_time >= $val){
            $return .= floor($sec_time/$time_array[$key]).$tmp_array[$key];
            $tmp = $sec_time % $time_array[$key];
            if($tmp != 0) $return .= time_string($tmp);
            break;
        }
    }
    return $return;
}
?>