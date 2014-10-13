<?php
/**
 * @copyright 2014
 * @description: cls_session_mysqli
 * @file: cls_session_mysqli.php
 * @charset: UTF-8
 * @create: 2014-10-13
 * @version 1.0
**/

class cls_session_mysqli{
    private $sess_table_name    = 'session';        //数据库表名
    private $sess_id_name       = 'id';             //数据库字段名SESSION_ID
    private $sess_data_name     = 'data';           //数据库字段名SESSION_DATA
    private $sess_time_name     = 'time';           //数据库字段名SESSION_TIME
    private $sess_ip_name       = 'ip';             //数据库字段名SESSION_IP
    private $sess_u_id_name     = 'u_id';           //数据库字段名SESSION_U_ID
    private $sess_u_name        = 'u_id';           //SESSION中KEY名SESSION_U_NAME
    private $max_life_time      = 1440;             //SESSION有效时间[秒]
    private $sess_path          = NULL;             //SESSION存放路径
    private $sess_id            = NULL;             //当前SESSION_ID值
    private $sess_name          = 'PHPSESSID';      //SESSION使用的名称
    private $mysqli             = NULL;             //操作的mysqli对象
    private $this_time          = 0;                //操作的mysqli对象

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
        isset($array['SESS_ID_NAME']) && $this -> sess_id_name = $array['SESS_ID_NAME'];
        isset($array['SESS_DATA_NAME']) && $this -> sess_data_name = $array['SESS_DATA_NAME'];
        isset($array['SESS_TIME_NAME']) && $this -> sess_time_name = $array['SESS_TIME_NAME'];
        isset($array['SESS_IP_NAME']) && $this -> sess_ip_name = $array['SESS_IP_NAME'];
        isset($array['SESS_U_ID_NAME']) && $this -> sess_u_id_name = $array['SESS_U_ID_NAME'];
        isset($array['SESS_U_NAME']) && $this -> sess_u_name = $array['SESS_U_NAME'];
        isset($array['MAX_LIFE_TIME']) && $this -> max_life_time = intval($array['MAX_LIFE_TIME']);
        $this -> mysqli = get_mod('mysqli', isset($array['SESS_MYSQLI_MARK']) ? $array['SESS_MYSQLI_MARK']:'session_mysqli');
        if($this -> mysqli && isset($array['SESS_MYSQLI_CONF'])){
            $this -> mysqli -> set_conf($array['SESS_MYSQLI_CONF']);
            isset($array['SESS_TABLE_NAME']) && $this -> sess_table_name = $this -> mysqli -> get_table_name($array['SESS_TABLE_NAME']);
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
        return $this -> mysqli -> get_array_one('SELECT '.$this -> sess_data_name.' FROM '.$this -> sess_table_name.' WHERE '.$this -> sess_id_name.'=\''.$sess_id.'\'', $this -> sess_data_name);
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
        $this -> mysqli -> reconnect();
        $u_id = 0;
        !is_empty($this -> sess_u_name) && isset($_SESSION) && isset($_SESSION[$this -> sess_u_name]) && $u_id = doubleval($_SESSION[$this -> sess_u_name]);
        $u_id = $u_id < 1 ? 0 : $u_id;
        if($this -> mysqli -> get_array_one('SELECT count(*) as num FROM '.$this -> sess_table_name.' WHERE '.$this -> sess_id_name.'=\''.$sess_id.'\'', 'num') > 0){
            $this -> mysqli -> get_query('UPDATE '.$this -> sess_table_name.' SET '.$this -> sess_data_name.'=\''.$sess_data.'\','.$this -> sess_time_name.'=\''.($this -> this_time + $this -> max_life_time).'\','.$this -> sess_ip_name.'=\''.ip_long(get_ip()).'\','.$this -> sess_u_id_name.'=\''.$u_id.'\' WHERE '.$this -> sess_id_name.'=\''.$sess_id.'\'');
        }else{
            $this -> mysqli -> get_query('INSERT INTO '.$this -> sess_table_name.' ('.$this -> sess_id_name.','.$this -> sess_data_name.','.$this -> sess_time_name.','.$this -> sess_ip_name.','.$this -> sess_u_id_name.') VALUES (\''.$sess_id.'\',\''.$sess_data.'\',\''.($this -> this_time + $this -> max_life_time).'\',\''.ip_long(get_ip()).'\',\''.$u_id.'\')');
        }
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
        $this -> mysqli -> get_query('DELETE FROM '.$this -> sess_table_name.' WHERE '.$this -> sess_id_name.'=\''.$sess_id.'\'');
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
        $this -> mysqli -> get_query('DELETE FROM '.$this -> sess_table_name.' WHERE '.$this -> sess_time_name.'<'.$this -> this_time);
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
        if((!is_string($sess_id) || strlen($sess_id) != 32) && isset($_COOKIE[$this -> sess_name]) && strlen($_COOKIE[$this -> sess_name]) == 32) $sess_id = $_COOKIE[$this -> sess_name];
        if(!preg_match('/^[a-f\d]{32}$/', $sess_id)) $sess_id = get_rand_string(32, 'abcdef0123456789');
        if(is_string($sess_id) && strlen($sess_id) == 32) session_id($sess_id);
        session_start();
        $this -> sess_id = $sess_id;
        $_COOKIE[$this -> sess_name] = $sess_id;
        setcookie($this -> sess_name, $sess_id, time()+$this -> max_life_time, '/', ini_get('session.cookie_domain'));
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