<?php
/**
 * @copyright 2014
 * @description: cls_sess
 * @file: cls_sess.php
 * @charset: UTF-8
 * @create: 2014-10-13
 * @version 1.0
**/

class cls_sess extends cls_mod{
    private $object_mark            = NULL;                 //object.mark.name
    protected $conflist             = array('SESS_TYPE','SESS_COOKIE','SESS_PATH','SESS_NAME','SESS_TABLE_NAME','SESS_ID_NAME','SESS_DATA_NAME','SESS_TIME_NAME',
                                        'SESS_IP_NAME','SESS_U_ID_NAME','SESS_U_NAME','MAX_LIFE_TIME','SESS_MYSQLI_MARK','SESS_MYSQLI_CONF',
                                        'SESS_CACHE_MARK','SESS_CACHE_CONF');

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
     * @name: get_session
     * @description: 获取对象session
     * @scope: public
     * @param: string 对象标签 default[NULL]
     * @param: boolean 是否新new对象 default[FALSE]
     * @return: object
     * @create: 2014-10-13
    **/
    public function get_session($mark_name=NULL, $is_new=FALSE){
        $mark_name = ($mark_name == '') ? $this -> object_mark : $mark_name;
        $sess_object = NULL;
        //识别SESSION
        switch($this -> get_conf('SESS_TYPE')){
            case 'MYSQLI' : {
                $sess_object = get_init('session_mysqli', $mark_name, $is_new);
                break;
            }
            case 'CACHE' : {
                $sess_object = get_init('session_cache', $mark_name, $is_new);
                break;
            }
            default : {
                die('Start Session ERROR!');
            }
        }
        $sess_object -> set_conf($this -> get_conf());
        return $sess_object;
    }

    /**
     * @name: open
     * @description: 开启SESSION
     * @scope: public
     * @param: string 32位的SESSION_ID
     * @param: integer 是否继续开启[default-0][0-否,1-继续开启,2-重新开启]
     * @return: boolean
     * @create: 2014-10-13
    **/
    public function open($sess_id=NULL, $is_true=0){
        return $this -> get_session() -> open($sess_id, $is_true) ? TRUE : FALSE;
    }

    /**
     * @name: close
     * @description: 关闭SESSION
     * @scope: public
     * @return: boolean
     * @create: 2014-10-13
    **/
    public function close(){
        return $this -> get_session() -> close() ? TRUE : FALSE;
    }

    /**
     * @name: is_open
     * @description: 是否开启SESSION
     * @scope: public
     * @return: boolean
     * @create: 2014-10-13
    **/
    public function is_open(){
        return $this -> get_session() -> is_open() ? TRUE : FALSE;
    }

    /**
     * @name: get_session_id
     * @description: 获取当前SESSION_ID
     * @scope: public
     * @return: string
     * @create: 2014-10-13
    **/
    public function get_session_id(){
        return $this -> get_session() -> get_session_id();
    }

    /**
     * @name: get_session_id
     * @description: 获取当前SESSION_ID
     * @scope: public
     * @param: string SESSION的KEY
     * @return: mixed
     * @create: 2014-10-13
    **/
    public function get($key){
        if($this -> is_set($key)){
            return $_SESSION[$key];
        }else{
            return NULL;
        }
    }

    /**
     * @name: get_session_id
     * @description: 获取当前SESSION_ID
     * @scope: public
     * @param: string SESSION的KEY
     * @param: mixed SESSION的VAL
     * @return: void
     * @create: 2014-10-13
    **/
    public function set($key, $val){
        $_SESSION[$key] = $val;
    }

    /**
     * @name: get_session_id
     * @description: 获取当前SESSION_ID
     * @scope: public
     * @param: string SESSION的KEY
     * @return: void
     * @create: 2014-10-13
    **/
    public function del($key){
        if($this -> is_set($key)){
            $_SESSION[$key] = NULL;
            unset($_SESSION[$key]);
        }
    }

    /**
     * @name: is_set
     * @description: 是否设置SESSION
     * @scope: public
     * @param: string SESSION的KEY
     * @return: boolean
     * @create: 2014-10-13
    **/
    public function is_set($key){
        return isset($_SESSION[$key]) ? TRUE : FALSE;
    }
}
?>