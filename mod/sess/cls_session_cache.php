<?php
/**
 * @copyright 2014
 * @description: cls_session_cache
 * @file: cls_session_cache.php
 * @charset: UTF-8
 * @create: 2014-10-13
 * @version 1.0
**/

class cls_session_cache{
    private $max_life_time      = 1440;             //SESSION有效时间[秒]
    private $sess_path          = NULL;             //SESSION存放路径
    private $sess_id            = NULL;             //当前SESSION_ID值
    private $sess_name          = 'PHPSESSID';      //SESSION使用的名称
    private $mysqli             = NULL;             //操作的mysqli对象
    private $this_time          = 0;                //操作的mysqli对象
    private $is_cookie          = TRUE;             //是否用COOKIE模式

    /**
     * @name: __construct
     * @description: class被实例化时调用此函数
     * @scope: public
     * @return: void
     * @create: 2014-10-13
    **/
    public function __construct(){
        $this -> this_time = time();
    }

    /**
     * @name: set_conf
     * @description: 配置连接数据库信息
     * @scope: public
     * @param: array 被设置的暑假
     * @return: void
     * @create: 2014-10-13
    **/
    public function set_conf($array){
        isset($array['SESS_PATH']) && $this -> sess_path = $array['SESS_PATH'];
        isset($array['SESS_NAME']) && $this -> sess_name = $array['SESS_NAME'];
        isset($array['SESS_COOKIE']) && $this -> is_cookie = $array['SESS_COOKIE'] ? TRUE : FALSE;
        isset($array['MAX_LIFE_TIME']) && $this -> max_life_time = max(intval($array['MAX_LIFE_TIME']), 0);
        $this -> cache = get_mod('cache', isset($array['SESS_CACHE_MARK']) ? $array['SESS_CACHE_MARK']:'session_cache');
        if($this -> cache && isset($array['SESS_CACHE_CONF'])){
            $this -> cache -> set_conf($array['SESS_CACHE_CONF']);
        }
    }

    /**
     * @name: sess_open
     * @description: session打开
     * @scope: public
     * @param: string session路径
     * @param: string session名称
     * @return: void
     * @create: 2014-10-13
    **/
    public function sess_open($save_path=NULL, $sess_name=NULL){
    }

    /**
     * @name: session_close
     * @description: session关闭
     * @scope: public
     * @return: void
     * @create: 2014-10-13
    **/
    public function sess_close(){
    }

    /**
     * @name: session_read
     * @description: session读取
     * @scope: public
     * @param: string session_id
     * @return: string
     * @create: 2014-10-13
    **/
    public function sess_read($sess_id){
        return $this -> cache -> get($sess_id);
    }

    /**
     * @name: session_write
     * @description: session写入数据
     * @scope: public
     * @param: string session_id
     * @param: string session数据
     * @return: void
     * @create: 2014-10-13
    **/
    public function sess_write($sess_id, $sess_data){
        $this -> cache -> reconnect();
        $this -> cache -> set($sess_id, $sess_data, $this -> max_life_time);
    }

    /**
     * @name: destroy
     * @description: session销毁
     * @scope: public
     * @param: string session_id
     * @return: void
     * @create: 2014-10-13
    **/
    public function sess_destroy($sess_id){
        $this -> cache -> delete($sess_id);
    }

    /**
     * @name: gc
     * @description: session回收
     * @scope: public
     * @param: integer session最大生成时间
     * @return: void
     * @create: 2014-10-13
    **/
    public function sess_gc($max_life_time){
    }

    /**
     * @name: open
     * @description: 启用session
     * @scope: public
     * @param: string 32位的sessionID
     * @param: integer 是否继续开启[0-否,1-继续开启,2-重新开启]
     * @return: object[this]
     * @create: 2014-10-13
    **/
    public function open($sess_id=NULL, $isnew=FALSE){
        if($isnew){
            if($this -> is_open()) $this -> close();
            $sess_id = get_rand_string(32, 'abcdef0123456789');
        }else{
            if($this -> is_open()) return $this;
        }
        session_set_save_handler(array(&$this, 'sess_open'), array(&$this, 'sess_close'), array(&$this, 'sess_read'), array(&$this, 'sess_write'), array(&$this, 'sess_destroy'), array(&$this, 'sess_gc'));
        if($this -> is_cookie){  //启用COOKIE
            if((!is_string($sess_id) || strlen($sess_id) != 32) && isset($_COOKIE[$this -> sess_name]) && strlen($_COOKIE[$this -> sess_name]) == 32) $sess_id = $_COOKIE[$this -> sess_name];
        }
        if(!preg_match('/^[a-f\d]{32}$/', $sess_id)) $sess_id = get_rand_string(32, 'abcdef0123456789');
        if(is_string($sess_id) && strlen($sess_id) == 32) session_id($sess_id);
        session_start();
        $this -> sess_id = $sess_id;
        if($this -> is_cookie){  //启用COOKIE
            $_COOKIE[$this -> sess_name] = $sess_id;
            setcookie($this -> sess_name, $sess_id, time()+$this -> max_life_time, '/', ini_get('session.cookie_domain'));
        }else{
            unset($_COOKIE[$this -> sess_name]);
            setcookie($this -> sess_name, $sess_id, time()-$this -> max_life_time, '/', ini_get('session.cookie_domain'));
        }
        return $this;
    }

    /**
     * @name: close
     * @description: 关闭销毁session
     * @scope: public
     * @return: object[this]
     * @create: 2014-10-13
    **/
    public function close(){
        $this -> sess_id = NULL;
        if(isset($_SESSION) && is_array($_SESSION) && count($_SESSION) > 0) foreach($_SESSION as $key => $val) unset($_SESSION[$key]);
        $_SESSION = NULL;
        session_unset();
        session_destroy();
        return $this;
    }

    /**
     * @name: is_open
     * @description: 是否打开session
     * @scope: public
     * @return: boolean
     * @create: 2014-10-13
    **/
    public function is_open(){
        return (is_string($this -> sess_id) && strlen($this -> sess_id) == 32)?TRUE:FALSE;
    }

    /**
     * @name: get_session_id
     * @description: 获取当前session_id
     * @scope: public
     * @return: string
     * @create: 2014-10-13
    **/
    public function get_session_id(){
        return $this -> sess_id;
    }
}
?>