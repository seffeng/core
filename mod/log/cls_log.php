<?php
/**
 * @copyright 2014
 * @description: cls_log
 * @file: cls_log.php
 * @charset: UTF-8
 * @create: 2014-10-11
 * @version 1.0
**/

class cls_log extends cls_mod{
    private $object_mark            = NULL;                 //object.mark.name
    protected $conflist             = array('LOG_DATAURL');

    /**
     * @name: __construct
     * @description: 构造函数
     * @scope: public
     * @param: string 对象标签 default[NULL]
     * @return: void
     * @create: 2014-10-11
    **/
    public function __construct($object_mark){
        if(!is_empty($object_mark)) $this -> object_mark = $object_mark;
    }

    /**
     * @name: set_conf
     * @description: 设置配置信息
     * @scope: public
     * @param: mixed 被配置的数据或名称
     * @param: mixed 被配置的数据值
     * @return: $this
     * @create: 2014-10-11
    **/
    public function set_conf($key, $val=NULL){
        $set_conf = parent::set_conf($key, $val);
        $this -> get_logs() -> set_conf($this -> get_conf('LOG_DATAURL'));
        return $set_conf;
    }

    /**
     * @name: get_logs
     * @description: 获取对象logs
     * @scope: public
     * @param: string 对象标签 default[NULL]
     * @param: boolean 是否新new对象 default[FALSE]
     * @return: object
     * @create: 2014-10-11
    **/
    public function get_logs($mark_name=NULL, $is_new=FALSE){
        $mark_name = ($mark_name == '') ? $this -> object_mark : $mark_name;
        return get_init('logs', $mark_name, $is_new);
    }

    /**
     * @name: write_string
     * @description: 日志文件的写
     * @scope: public
     * @param: string 操作内容
     * @param: string 操作类型信息 [default-NULL]
     * @param: string 操作关键信息 [default-NULL]
     * @param: integer 操作时间 [default-NULL][CORE_THIS_TIME]
     * @param: string 操作IP [default-NULL][CORE_THIS_IPS]
     * @return: void
     * @create: 2014-10-11
    **/
    public function write_string($option_content, $option_type=NULL, $option_key=NULL, $option_time=NULL, $option_ip=NULL){
        $this -> get_logs() -> log_write($option_content, $option_type, $option_key, $option_time, $option_ip);
    }
}
?>