<?php
/**
 * @copyright 2014
 * @description: CLS_MOD
 * @file: cls_mod.php
 * @charset: UTF-8
 * @create: 2014-10-11
 * @version 1.0
**/

class cls_mod{
    protected $conf             = array();          //configure
    protected $conflist         = array();          //configure.list

    /**
     * @name: get_conf
     * @description: 获取配置信息
     * @scope: public
     * @param: mixed 被配置的数据名称 default[NULL]
     * @return: $this
     * @create: 2014-10-11
    **/
    public function get_conf($key=NULL){
        if($key === NULL) return $this -> conf;
        if(!isset($this -> conf[$key])) return FALSE;
        return $this -> conf[$key];
    }

    /**
     * @name: set_conf
     * @description: 设置配置信息
     * @scope: public
     * @param: mixed 被配置的数据或名称
     * @param: mixed 被配置的数据值 default[NULL]
     * @return: $this
     * @create: 2014-10-11
    **/
    public function set_conf($key, $val=NULL){
        if(is_array($key) && count($key) > 0){
            foreach($key as $k => $v) if(in_array($k, $this -> conflist)) $this -> conf[$k] = $v;
        }else{
            if(in_array($key, $this -> conflist)) $this -> conf[$key] = $val;
        }
        return $this;
    }

    /**
     * @name: get_option_list
     * @description: 获取操作方法
     * @scope: public
     * @return: array
     * @create: 2014-10-11
    **/
    public function get_option_list(){
        $list = get_class_methods($this);
        if(is_foreach($list)) foreach($list as $key => $val){
            if(substr($val, 0, 1) == '_') unset($list[$key]);
        }
        return $list;
    }

    /**
     * @name: get_conf_list
     * @description: 获取可配置参数
     * @scope: public
     * @return: array
     * @create: 2014-10-11
    **/
    public function get_conf_list(){
        return $this -> conflist;
    }
}
?>