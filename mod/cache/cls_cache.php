<?php
/**
 * @copyright 2014
 * @description: cls_cache
 * @file: cls_cache.php
 * @charset: UTF-8
 * @create: 2014-10-13
 * @version 1.0
**/

class cls_cache extends cls_mod{
    private $object_mark            = NULL;                 //object.mark.name
    protected $conflist             = array('CACHE_TYPE','MEMCACHED_DATA');

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
        return $set_conf;
    }

    /**
     * @name: get_memory
     * @description: 获取对象memory
     * @scope: public
     * @param: string 对象标签 default[NULL]
     * @param: boolean 是否新new对象 default[FALSE]
     * @return: object
     * @create: 2014-10-13
    **/
    public function get_memory($mark_name=NULL, $is_new=FALSE){
        $mark_name = ($mark_name == '') ? $this -> object_mark : $mark_name;
        $sess_object = NULL;
        switch($this -> get_conf('CACHE_TYPE')){    //识别SESSION
            case 'MEMCACHED' : {
                $sess_object = get_init('memcached', $mark_name, $is_new);
                break;
            }
            default : {
                die('Start Cache ERROR!');
            }
        }
        $sess_object -> set_conf($this -> get_conf('MEMCACHED_DATA'));
        if(!$sess_object -> is_connect()){
            if(!$sess_object -> connect()) return FALSE;
        }
        return $sess_object;
    }

    /**
     * @name: reconnect
     * @description: 重新连接的MEMCACHE服务器
     * @scope: public
     * @return: boolean
     * @create: 2014-10-13
    **/
    public function reconnect(){
        $memory = $this -> get_memory();
        if($memory === FALSE) return FALSE;
        return $memory -> connect();
    }

    /**
     * @name: is_key
     * @description: 是否是合法长度的KEY
     * @scope: public
     * @param: string 被操作的KEY
     * @return: boolean
     * @create: 2014-10-13
    **/
    public function is_key($key){
        $leng = strlen($key);
        if($leng < 1 || $leng > 255) return FALSE;
        return TRUE;
    }

    /**
     * @name: get
     * @description: 获取cache值
     * @scope: public
     * @param: string 被操作的KEY
     * @return: mixed
     * @create: 2014-10-13
    **/
    public function get($key){
        if(!$this -> is_key($key)) return FALSE;
        $memory = $this -> get_memory();
        if($memory === FALSE) return FALSE;
        return $memory -> get($key);
    }

    /**
     * @name: set
     * @description: 设置cache值
     * @scope: public
     * @param: string 被操作的KEY
     * @param: string 被操作的值
     * @param: integer 生存的有效期[0-无限,单位秒]
     * @return: boolean
     * @create: 2014-10-13
    **/
    public function set($key, $val, $ttl=NULL){
        if(!$this -> is_key($key)) return FALSE;
        $memory = $this -> get_memory();
        if($memory === FALSE) return FALSE;
        return $memory -> set($key, $val, $ttl);
    }

    /**
     * @name: inc
     * @description: 自增cache值
     * @scope: public
     * @param: string 被操作的KEY
     * @return: boolean
     * @create: 2014-10-13
    **/
    public function inc($key){
        if(!$this -> is_key($key)) return FALSE;
        $memory = $this -> get_memory();
        if($memory === FALSE) return FALSE;
        return $memory -> inc($key);
    }

    /**
     * @name: dec
     * @description: 自减cache值
     * @scope: public
     * @param: string 被操作的KEY
     * @return: boolean
     * @create: 2014-10-13
    **/
    public function dec($key){
        if(!$this -> is_key($key)) return FALSE;
        $memory = $this -> get_memory();
        if($memory === FALSE) return FALSE;
        return $memory -> dec($key);
    }

    /**
     * @name: delete
     * @description: 删除cache值
     * @scope: public
     * @param: string 被操作的KEY
     * @return: boolean
     * @create: 2014-10-13
    **/
    public function delete($key, $time_out = 0){
        if(!$this -> is_key($key)) return FALSE;
        $memory = $this -> get_memory();
        if($memory === FALSE) return FALSE;
        return $memory -> delete($key, $time_out);
    }

    /**
     * @name: flush
     * @description: 刷新清空cache值
     * @scope: public
     * @return: void
     * @create: 2014-10-13
    **/
    public function flush(){
        $memory = $this -> get_memory();
        if($memory === FALSE) return FALSE;
        return $memory -> flush();
    }

    /**
     * @name: get_status
     * @description: 获取当前状态
     * @scope: public
     * @return: array
     * @create: 2014-10-13
    **/
    public function get_status(){
        $memory = $this -> get_memory();
        if($memory === FALSE) return FALSE;
        return $memory -> get_status();
    }
}
?>