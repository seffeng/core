<?php
/**
 * @copyright 2014
 * @description: cls_ende
 * @file: cls_ende.php
 * @charset: UTF-8
 * @create: 2014-10-13
 * @version 1.0
**/

class cls_ende extends cls_mod{
    private $object_mark            = NULL;                 //object.mark.name
    protected $conflist             = array('ENDE_KEY','ENDE_KEYNAME','ENDE_NOCRYPT');

    /**
     * @name: __construct
     * @description: 构造函数
     * @scope: public
     * @param: string 对象标签 default[NULL]
     * @return: void
     * @create: 2014-10-13
    **/
    public function __construct($object_mark){
        if(!is_empty($object_mark)) $this -> object_mark = $object_mark;
    }

    /**
     * @name: get_encdec
     * @description: 获取对象encdec
     * @scope: public
     * @param: string 对象标签 default[NULL]
     * @param: boolean 是否新new对象 default[FALSE]
     * @return: object
     * @create: 2014-10-13
    **/
    public function get_encdec($mark_name=NULL, $is_new=FALSE){
        $mark_name = ($mark_name == '') ? $this -> object_mark : $mark_name;
        return get_init('encdec', $mark_name, $is_new);
    }

    /**
     * @name: set_conf
     * @description: 设置配置信息
     * @scope: public
     * @param: mixed 被配置的数据或名称
     * @param: mixed 被配置的数据值
     * @return: $this
     * @create: 2014-10-13
    **/
    public function set_conf($key, $val=NULL){
        $set_conf = parent::set_conf($key, $val);
        $this -> get_encdec() -> set_conf($this -> get_conf('ENDE_KEY'), $this -> get_conf('ENDE_KEYNAME'), $this -> get_conf('ENDE_NOCRYPT'));
        return $set_conf;
    }

    /**
     * @name: enc_data
     * @description: 加密数据
     * @scope: public
     * @param: mixed 被加密的数据
     * @param: string 加密关键key default['']
     * @return: string
     * @create: 2014-10-13
    **/
    public function enc_data($string, $key=''){
        $key == '' && $key = $this -> get_conf('ENDE_KEY');
        if(!is_string($string)) $string = @serialize($string);
        return $this -> get_encdec() -> en_de_code($string, $key, 1);
    }

    /**
     * @name: dec_data
     * @description: 解密数据
     * @scope: public
     * @param: string 被加密的字符串
     * @param: string 解密关键key default['']
     * @return: mixed
     * @create: 2014-10-13
    **/
    public function dec_data($string, $key=''){
        $key == '' && $key = $this -> get_conf('ENDE_KEY');
        $return = $this -> get_encdec() -> en_de_code($string, $key, 2);
        $returns = @unserialize($return);
        if($returns) $return = $returns;
        return $return;
    }

    /**
     * @name: enc_http_string
     * @description: 加密生成可URL发送数据
     * @scope: public
     * @param: array 发送的数据
     * @return: string
     * @create: 2014-10-13
    **/
    public function enc_http_string($data){
        return $this -> get_encdec() -> data_en($data, TRUE);
    }

    /**
     * @name: enc_http_array
     * @description: 加密生成可URL发送数据
     * @scope: public
     * @param: array 发送的数据
     * @return: array
     * @create: 2014-10-13
    **/
    public function enc_http_array($data){
        return $this -> get_encdec() -> data_en($data, FALSE);
    }

    /**
     * @name: dec_http_string
     * @description: 解密和校验数据
     * @scope: public
     * @param: mixed 被解密和校验的数据
     * @return: string
     * @create: 2014-10-13
    **/
    public function dec_http_string($data){
        return $this -> get_encdec() -> data_de($data, TRUE);
    }

    /**
     * @name: dec_http_array
     * @description: 解密和校验数据
     * @scope: public
     * @param: mixed 被解密和校验的数据
     * @return: array
     * @create: 2014-10-13
    **/
    public function dec_http_array($data){
        return $this -> get_encdec() -> data_de($data, FALSE);
    }
}
?>