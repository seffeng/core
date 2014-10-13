<?php
/**
 * @copyright 2014
 * @description: cls_encdec
 * @file: cls_encdec.php
 * @charset: UTF-8
 * @create: 2014-10-13
 * @version 1.0
**/

class cls_encdec{
    private $key                    = NULL;                 //通信密钥Key
    private $no_crypt               = NULL;                 //不需要加密参数名
    private $mykey                  = 'mykey';              //通信KEY名
    private $mycode                 = 'mycode';             //通信CODE

    /**
     * @name: set_conf
     * @description: 设置必要的参数数据
     * @scope: public
     * @param: string 通信密钥Key
     * @param: string 通信KEY名 default[mykey]
     * @param: array 无需加密参数 default[NULL]
     * @return: void
     * @create: 2014-10-13
    **/
    public function set_conf($key, $mykey='mykey', $no_crypt=NULL){
        if(!preg_match('/^[a-z][a-z\d_]*$/i', $key)) return FALSE;
        if(!preg_match('/^[a-z][a-z\d_]*$/i', $mykey)) return FALSE;
        $this -> key = $key;
        $this -> mykey = $mykey;
        $this -> no_crypt = array();
        if(!is_empty($no_crypt) && is_type($no_crypt, 'array')) foreach($no_crypt as $val){
            if(preg_match('/^[a-z][a-z\d_]*$/i', $mykey) && !in_array($val, $this -> no_crypt)) $this -> no_crypt[] = $val;
        }
        if(!in_array($this -> mykey, $this -> no_crypt)) $this -> no_crypt[] = $this -> mykey;
    }

    /**
     * @name: en_de_code
     * @description: 加密解密数据
     * @scope: public
     * @param: string 被加密or解密的字符串
     * @param: string 加密or解密关键key default['']
     * @param: integer 加密or解密 default[1],1-加密,2-解密
     * @return: string
     * @create: 2014-10-13
    **/
    public function en_de_code($string, $key='', $types=1){
        if($key == '') return FALSE;
        $key = md5($key);
        $key_leng = strlen($key);
        if($key_leng == 0) return FALSE;
        $string = $types != 1 ? base64_decode($string):substr(md5($string.$key), 0, 8).$string;
        $stringLeng = strlen($string);
        $rndkey = $box = array();
        $result = '';
        for($i = 0; $i <= 255; $i++){
            $rndkey[$i] = ord($key[$i % $key_leng]);
            $box[$i] = $i;
        }
        for($j = $i = 0; $i < 256; $i++){
            $j = ($j + $box[$i] + $rndkey[$i]) % 256;
            $tmp = $box[$i];
            $box[$i] = $box[$j];
            $box[$j] = $tmp;
        }
        for($a = $j = $i = 0; $i < $stringLeng; $i++){
            $a = ($a + 1) % 256;
            $j = ($j + $box[$a]) % 256;
            $tmp = $box[$a];
            $box[$a] = $box[$j];
            $box[$j] = $tmp;
            $result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
        }
        if($types != 1){
            if(substr($result,0,8) == substr(md5(substr($result, 8).$key), 0, 8)) {
                return substr($result, 8);
            }else{
                return FALSE;
            }
        }else{
            return str_replace('=', '', base64_encode($result));
        }
    }

    /**
     * @name: data_de
     * @description: 解密和校验数据
     * @scope: public
     * @param: mixed 被解密和校验的数据
     * @param: boolean 返回结果是否字符串 default[TRUE]
     * @return: mixed
     * @create: 2014-10-13
    **/
    public function data_de($data, $is_string=TRUE){
        $de_data = $this -> get_de_data($data, FALSE);
        if(!is_array($de_data)) return FALSE;               //解密数据失败
        if(!isset($de_data[$this -> mykey])) return FALSE;  //无有效数据
        $get_sign_md5 = $de_data[$this -> mykey];
        unset($de_data[$this -> mykey]);
        if($get_sign_md5 != $this -> get_sign_md5($de_data)) return FALSE;  //数据被攥改
        if($is_string){
            $return = '';
            if(count($de_data) > 0) foreach($de_data as $key => $val){
                $return .= ($return == ''?'':'&').urlencode($key).'='.urlencode($val);
            }
            return $return;
        }
        return $de_data;
    }

    /**
     * @name: data_en
     * @description: 加密生成可发送数据
     * @scope: public
     * @param: array 发送的数据
     * @param: boolean 返回结果是否字符串 default[TRUE]
     * @return: mixed
     * @create: 2014-10-13
    **/
    public function data_en($data, $is_string=TRUE){
        if(!is_array($data) || count($data) < 1) return FALSE;
        $data[$this -> mykey] = $this -> get_sign_md5($data);
        return $this -> get_en_data($data, $is_string);
    }

    /**
     * @name: get_en_data
     * @description: 对数据各自加密
     * @scope: private
     * @param: array 被加密数据
     * @param: boolean 返回结果是否字符串 default[1]
     * @return: mixed
     * @create: 2014-10-13
    **/
    private function get_en_data($array, $is_string=TRUE){
        $return = $is_string ? '' : array();
        if(is_array($array) && count($array) > 0) foreach($array as $key => $val){
            if(is_array($this -> no_crypt) && !in_array(strval($key), $this -> no_crypt)){
                $val = serialize($val);
                $tmp_val = $this -> en_de_code($val, $this -> key, 1);
            }else{
                $tmp_val = $val;
            }
            if($is_string){
                $return .= ($return == ''?'':'&').urlencode($key).'='.urlencode($tmp_val);
            }else{
                $return[$key] = $tmp_val;
            }
        }
        return $return;
    }

    /**
     * @name: get_de_data
     * @description: 对数据各自解密
     * @scope: private
     * @param: mixed 被加密数据
     * @param: boolean 返回结果是否字符串 default[1]
     * @return: mixed
     * @create: 2014-10-13
    **/
    private function get_de_data($data, $is_string = TRUE){
        $return = $is_string ? '' : array();
        if(!is_array($data)){   //分析标准参数格式字符串
            $data_tmp = array();
            foreach(explode('&', $data) as $val){
                $tmp = explode('=', $val);
                if(count($tmp) == 2) $data_tmp[urldecode($tmp[0])] = urldecode($tmp[1]);
            }
            $data = $data_tmp;
        }
        if(count($data) > 0) foreach($data as $key => $val){
            if(is_array($this -> no_crypt) && !in_array(strval($key), $this -> no_crypt)){
                $tmp_val = $this -> en_de_code($val, $this -> key, 2);  //解密数据
                if($tmp_val === FALSE) return FALSE;
                $tmp_val = unserialize($tmp_val);
            }else{
                $tmp_val = $val;
            }
            if($is_string){
                $return .= ($return == ''?'':'&').urlencode($key).'='.urlencode($tmp_val);
            }else{
                $return[$key] = $tmp_val;
            }
        }
        return $return;
    }

    /**
     * @name: get_sign_md5
     * @description: 获取唯一值,MD5值
     * @scope: private
     * @param: array 提供求值的数据
     * @return: string
     * @create: 2014-10-13
    **/
    private function get_sign_md5($array){
        if(is_array($array) && count($array) > 0){
            asort($array);
            $tmp_val = '';
            foreach($array as $key => $val){
                if(is_array($this -> no_crypt) && !in_array(strval($key), $this -> no_crypt)){
                    $tmp_val .= $key.'='.serialize($val);
                }
            }
        }else{
            return FALSE;
        }
        return strtolower(md5($tmp_val));
    }
}
?>