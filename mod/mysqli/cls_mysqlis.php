<?php
/**
 * @copyright 2014
 * @description: cls_mysqlis
 * @file: cls_mysqlis.php
 * @charset: UTF-8
 * @create: 2014-10-13
 * @version 1.0
**/

class cls_mysqlis{
    private $reconnect_count    = 0;            //被重连接的次数
    private $db_link            = NULL;         //数据库连接
    private $db_name            = NULL;         //数据库名称
    private $db_chart           = 'utf8';       //数据库编码
    private $db_host            = NULL;         //数据库主机
    private $db_port            = '3306';       //数据库端口3306
    private $db_user            = NULL;         //数据库用户名
    private $db_pass            = NULL;         //数据库密码
    private $cur_sql            = NULL;         //当前执行的sql语句
    private $query_result       = NULL;         //执行sql的query结果
    private $list_sql           = array();      //被执行过的sql集合
    private $is_sql             = TRUE;         //SQL语句是否记录
    private $sql_max            = 30;           //SQL最大的记录量

    /**
     * @name: __destruct
     * @description: 对象被注销时调用此函数
     * @scope: public
     * @return: void
     * @create: 2014-10-13
    **/
    public function __destruct(){
        $this -> close();
    }

    /**
     * @name: debug
     * @description: 输出消息并终止程序
     * @scope: private final
     * @param: string 输出的消息
     * @param: string 是否终止程序 default[TRUE]
     * @return: void
     * @create: 2014-10-13
    **/
    private final function debug($message, $is_exit=TRUE){
        (is_exists('debug', 'function')) && debug($message, __CLASS__);
        if($is_exit) exit;
    }

    /**
     * @name: select_db
     * @description: 选择数据库
     * @scope: public
     * @param: string 数据库名称
     * @param: resource 数据库连接 default[NULL]
     * @return: boolean
     * @create: 2014-10-13
    **/
    public function select_db($db_name=NULL, $db_link=NULL){
        is_empty($db_name) && $db_name = $this -> db_name;
        is_empty($db_link) && $db_link = $this -> db_link;
        if(!$this -> is_connect($db_link)) $this -> debug('Mysql Not Connect Or Connect Failure!');
        !($return = mysqli_select_db($db_link, $db_name)) && $this -> debug('Mysql Select DB ['.$db_name.'] Failure!');
        return $return;
    }

    /**
     * @name: select_charct
     * @description: 选择数据库编码
     * @scope: public
     * @param: string 数据库编码
     * @param: resource 数据库连接 default[NULL]
     * @return: boolean
     * @create: 2014-10-13
    **/
    public function select_charct($db_chart=NULL, $db_link=NULL){
        is_empty($db_chart) && $db_chart = $this -> db_chart;
        is_empty($db_link) && $db_link = $this -> db_link;
        if(is_exists('mysqli_set_charset', 'function')){
            $return = mysqli_set_charset($db_link, $db_chart);
        }else{
            !($return = $this -> query('SET NAMES \''.$db_chart.'\'', $db_link)) && $this -> debug('Mysql Set Char ['.$db_chart.'] Failure!');
        }
        return $return ? true : FALSE;
    }

    /**
     * @name: is_connect
     * @description: 是否连接数据库
     * @scope: public
     * @param: resource 数据库连接 default[NULL]
     * @param: boolean 是否真实连接 default[FALSE]
     * @return: boolean
     * @create: 2014-10-13
    **/
    public function is_connect($db_link=NULL, $is_real=FALSE){
        is_empty($db_link) && $db_link = $this -> db_link;
        if(is_empty($db_link)) return FALSE;
        if(!$is_real){
            if(is_type($db_link, 'object')) return TRUE;
        }else{
            return $this -> query('select unix_timestamp() as time', $db_link, FALSE) ? TRUE : FALSE;
        }
        return FALSE;
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
        isset($array['MYSQLI_HOST']) && $this -> db_host = $array['MYSQLI_HOST'];
        isset($array['MYSQLI_USER']) && $this -> db_user = $array['MYSQLI_USER'];
        isset($array['MYSQLI_PASS']) && $this -> db_pass = $array['MYSQLI_PASS'];
        isset($array['MYSQLI_PORT']) && $this -> db_port = $array['MYSQLI_PORT'];
        isset($array['MYSQLI_DBNAME']) && $this -> db_name = $array['MYSQLI_DBNAME'];
        isset($array['MYSQLI_CHARSET']) && $this -> db_chart = $array['MYSQLI_CHARSET'];
    }

    /**
     * @name: connect
     * @description: 连接数据库
     * @scope: public
     * @param: string 主机地址 default[NULL]
     * @param: string 数据库用户 default[NULL]
     * @param: string 数据库密码 default[NULL]
     * @param: string 主机端口 default[NULL]
     * @return: resource
     * @create: 2014-10-13
    **/
    public function connect($db_host=NULL, $db_user=NULL, $db_pass=NULL, $db_port=NULL){
        !is_empty($db_host) && $this -> db_host = $db_host;
        !is_empty($db_user) && $this -> db_user = $db_user;
        !is_empty($db_pass) && $this -> db_pass = $db_pass;
        !is_empty($db_port) && $this -> db_port = $db_port;
        if(!is_empty($this -> db_port) && !is_empty($this -> db_name)){
            if(is_empty($this -> db_name)) $this -> debug('Mysql Connect Error: Select Database Name NULL!');
            $db_link = mysqli_connect($this -> db_host, $this -> db_user, $this -> db_pass, $this -> db_name, $this -> db_port);
        }else if(!is_empty($this -> db_name)){
            $db_link = mysqli_connect($this -> db_host, $this -> db_user, $this -> db_pass, $this -> db_name);
        }else{
            $db_link = mysqli_connect($this -> db_host, $this -> db_user, $this -> db_pass);
        }
        if(!$db_link || is_empty($db_link)) $this -> debug('Mysql Connect Error: UserName or PassWord Error!');
        $this -> db_link = $db_link;
        !is_empty($this -> db_name) && $this -> select_db($this -> db_name, $this -> db_link);
        !is_empty($this -> db_chart) && $this -> select_charct($this -> db_chart, $this -> db_link);
        return $db_link;
    }

    /**
     * @name: get_link
     * @description: 获取数据库连接
     * @scope: public
     * @return: resource
     * @create: 2014-10-13
    **/
    public function get_link(){
        if(!$this -> is_connect($this -> db_link)) return FALSE;
        return $this -> db_link;
    }

    /**
     * @name: reconnect
     * @description: 连接数据库
     * @scope: public
     * @return: resource
     * @create: 2014-10-13
    **/
    public function reconnect(){
        ++$this -> reconnect_count;
        return $this -> connect();
    }

    /**
     * @name: close
     * @description: 关闭数据库连接
     * @scope: public
     * @param: resource 数据库连接 default[NULL]
     * @return: void
     * @create: 2014-10-13
    **/
    public function close($db_link=NULL){
        is_empty($db_link) && $db_link = $this -> db_link;
        $this -> is_connect($db_link) && mysqli_close($db_link);
    }

    /**
     * @name: free
     * @description: 释放资源
     * @scope: public
     * @param: mixed 数据库结果集体 default[NULL]
     * @return: void
     * @create: 2014-10-13
    **/
    public function free($query_result=NULL){
        is_empty($query_result) && $query_result = $this -> query_result;
        if(is_empty($query_result) || !is_type($query_result, array('resource'))) return true;
        mysqli_free_result($query_result);
    }

    /**
     * @name: query
     * @description: 执行sql语句
     * @scope: public
     * @param: string Sql语句
     * @param: resource 数据库连接 default[NULL]
     * @param: boolean 是否需要检查连接 default[TRUE]
     * @return: resource
     * @create: 2014-10-13
    **/
    public function query($sql, $db_link=NULL, $is_conn=TRUE){
        is_empty($db_link) && $db_link = $this -> db_link;
        if($is_conn){
            !$this -> is_connect($db_link, TRUE) && $this -> reconnect() && $db_link = $this -> db_link;
            if(!$this -> is_connect($db_link)) $this -> debug('Mysql Not Connect Or Connect Failure!');
        }
        $this -> cur_sql = trim($sql);
        $start_time = get_cur_time();
        $this -> query_result = mysqli_query($db_link, $this -> cur_sql);
        $time_diff = time_diff($start_time);
        if($this -> is_sql){    //开启记录sql
            if(count($this -> list_sql) >= $this -> sql_max) array_shift($this -> list_sql);
            $this -> list_sql[] = array('sql' => $this -> cur_sql, 'time' => $time_diff, 'result' => ($this -> query_result ? TRUE : FALSE), 'message' => mysqli_error($db_link), 'code' => mysqli_errno($db_link));
        }
        if($this -> query_result === FALSE) $this -> debug('Mysql Query Failure,Sql['.$sql.']!'.mysqli_error($db_link), FALSE);
        return $this -> query_result;
    }

    /**
     * @name: fetch_array
     * @description: 获取数组数据
     * @scope: public
     * @param: mixed query后的结果集合或者Sql语句
     * @param: integer 返回结果索引[1-字段,2-数字,3-混合]
     * @param: resource 数据库连接 default[NULL]
     * @return: array
     * @create: 2014-10-13
    **/
    public function fetch_array($resource, $result=1, $db_link=NULL){
        if(is_type($resource, 'string')){
            is_empty($db_link) && $db_link = $this -> db_link;
            $resource = $this -> query($resource, $db_link);
            if(!$resource) return FALSE;
        }
        if(is_type($result, 'integer')){
            switch(intval($result)){
                case 1 : {$result = MYSQLI_ASSOC;break;}
                case 2 : {$result = MYSQLI_NUM;break;}
                default : {$result = MYSQLI_BOTH;}
            }
        }else{
            $return_value = $result;
            $result = MYSQLI_BOTH;
        }
        $return = mysqli_fetch_array($resource, $result);
        if(isset($return_value) && !is_empty($return_value)){
            return isset($return[$return_value]) ? $return[$return_value] : FALSE;
        }else{
            return $return;
        }
    }

    /**
     * @name: get_array
     * @description: 获取数组数据全部
     * @scope: public
     * @param: mixed query后的结果集合或者Sql语句
     * @param: integer 返回结果索引[1-字段,2-数字,3-混合]
     * @param: resource 数据库连接 default[NULL]
     * @return: array
     * @create: 2014-10-13
    **/
    public function get_array($sql, $result=1, $db_link=NULL){
        $is_string = is_type($sql, 'string') ? TRUE : FALSE;
        if($is_string){
            is_empty($db_link) && $db_link = $this -> db_link;
            $resource = $this -> query($sql, $db_link);
        }else{
            $resource = $sql;
        }
        if(!$resource) return FALSE;
        $return = array();
        if(is_type($result, 'integer')){
            switch(intval($result)){
                case 1 : {$result = MYSQLI_ASSOC;break;}
                case 2 : {$result = MYSQLI_NUM;break;}
                default : {$result = MYSQLI_BOTH;}
            }
        }else{
            $return_value = $result;
            $result = MYSQLI_BOTH;
        }
        while($array = mysqli_fetch_array($resource, $result)){
            $return[] = $array;
        }
        return $return;
    }

    /**
     * @name: get_array_one
     * @description: 获取数组数据全部一条
     * @scope: public
     * @param: mixed query后的结果集合或者Sql语句
     * @param: integer 返回结果索引[1-字段,2-数字,3-混合]
     * @param: resource 数据库连接 default[NULL]
     * @return: array
     * @create: 2014-10-13
    **/
    public function get_array_one($sql, $result=1, $db_link=NULL){
        $is_string = is_type($sql, 'string') ? TRUE : FALSE;
        if($is_string){
            is_empty($db_link) && $db_link = $this -> db_link;
            $sql_limit = strtolower(trim($sql));    //limit
            if(substr($sql, 0, 6) == 'select'){
                if(strrpos($sql_limit, ' limit') < 0 || (!preg_match("/([\s]+limit[\s]+[\d]+)$/i", $sql) && !preg_match("/([\s]+limit[\s]+[\d]+[\s]*,[\s]*[\d]+)$/i", $sql))) $sql = rtrim($sql).' LIMIT 1';
            }
            $resource = $this -> query($sql, $db_link);
        }else{
            $resource = $sql;
        }
        if(!$resource) return FALSE;
        return $this -> fetch_array($resource, $result, $db_link);
    }

    /**
     * @name: get_table_info
     * @description: 获取表信息
     * @scope: public
     * @param: string 表名称
     * @param: string 字段名称
     * @return: array
     * @create: 2014-10-13
    **/
    public function get_table_info($table_name, $filed_name=NULL){
        if(is_empty($table_name)) return FALSE;
        $result = $this -> get_array_one('SHOW TABLE STATUS FROM '.($this -> get_dbname()).' WHERE NAME=\''.$table_name.'\'');
        if(is_empty($result)) return FALSE;
        $return = array();
        foreach($result as $key => $val){
            $return[strtolower($key)] = strtolower(trim($val));
        }
        if(!is_empty($filed_name)){
            return (isset($return[$filed_name])) ? $return[$filed_name] : FALSE;
        }
        return $return;
    }

    /**
     * @name: get_table_fields
     * @description: 获取表字段信息
     * @scope: public
     * @param: string 表名称
     * @param: string 字段名称
     * @return: array
     * @create: 2014-10-13
    **/
    public function get_table_fields($table_name, $filed_name=NULL){
        if(is_empty($table_name)) return FALSE;
        $return = $this -> get_array('SHOW FULL FIELDS FROM '.$table_name);
        if(is_empty($return)) return FALSE;
        if(!is_empty($filed_name)){
            foreach($return as $key => $val){
                if(isset($val['Field']) && $val['Field'] == $filed_name){
                    return $val;
                }
            }
            return FALSE;
        }
        return $return;
    }

    /**
     * @name: get_num_rows
     * @description: 获取数据条数
     * @scope: public
     * @param: string Sql语句
     * @param: resource 数据库连接 default[NULL]
     * @return: integer
     * @create: 2014-10-13
    **/
    public function get_num_rows($sql, $db_link=NULL){
        if(is_type($sql, 'string')){
            is_empty($db_link) && $db_link = $this -> db_link;
            $resource = $this -> query($sql, $db_link);
        }else{
            $resource = $sql;
        }
        return $resource ? mysqli_num_rows($resource) : FALSE;
    }

    /**
     * @name: insert_id
     * @description: 获取数据最后插入Id
     * @scope: public
     * @param: resource 数据库连接 default[NULL]
     * @return: integer
     * @create: 2014-10-13
    **/
    public function insert_id($db_link=NULL){
        is_empty($db_link) && $db_link = $this -> db_link;
        return mysqli_insert_id($db_link);
    }

    /**
     * @name: affected_rows
     * @description: 取得前一次操作所影响的记录数
     * @scope: public
     * @param: resource 数据库连接 default[NULL]
     * @return: integer
     * @create: 2014-10-13
    **/
    public function affected_rows($db_link=NULL){
        is_empty($db_link) && $db_link = $this -> db_link;
        return mysqli_affected_rows($db_link);
    }

    /**
     * @name: rollback
     * @description: 回滚数据(只适用于InnoDB)
     * @scope: public
     * @param: resource 数据库连接 default[NULL]
     * @return: boolean
     * @create: 2014-10-13
    **/
    public function rollback($db_link=NULL){
        is_empty($db_link) && $db_link = $this -> db_link;
        !($return = $this -> query('ROLLBACK;', $db_link)) && $this -> debug('Mysql ROLLBACK Failure!', FALSE);
        return $return ? true : FALSE;
    }

    /**
     * @name: get_dbname
     * @description: 获取当前数据库名称
     * @scope: public
     * @param: boolean 是否获取Mysql内部 default[TRUE]
     * @return: string
     * @create: 2014-10-13
    **/
    public function get_dbname($is_mysql=TRUE){
        if(!$is_mysql){
            return $this -> db_name;
        }else{
            return $this -> get_array_one('SELECT DATABASE() AS value', 'value');
        }
    }

    /**
     * @name: get_version
     * @description: 获取当前数据库版本
     * @scope: public
     * @return: string
     * @create: 2014-10-13
    **/
    public function get_version(){
        return $this -> get_array_one('SELECT VERSION() AS value', 'value');
    }

    /**
     * @name: get_user
     * @description: 获取当前数据库帐号
     * @scope: public
     * @param: boolean 是否获取Mysql内部 default[TRUE]
     * @return: string
     * @create: 2014-10-13
    **/
    public function get_user($is_mysql=TRUE){
        if(!$is_mysql){
            return $this -> db_user;
        }else{
            $value = $this -> get_array_one('SELECT USER() AS value', 'value');
            $tmp = explode('@', $value);
            return $tmp[0];
        }
    }

    /**
     * @name: get_host
     * @description: 获取当前数据库主机
     * @scope: public
     * @param: boolean 是否获取Mysql内部 default[TRUE]
     * @return: string
     * @create: 2014-10-13
    **/
    public function get_host($is_mysql=TRUE){
        if(!$is_mysql){
            return $this -> db_host;
        }else{
            $value = $this -> get_array_one('SELECT USER() AS value', 'value');
            $tmp = explode('@', $value);
            return $tmp[1];
        }
    }

    /**
     * @name: get_dbtime
     * @description: 获取当前数据库时间
     * @scope: public
     * @return: integer
     * @create: 2014-10-13
    **/
    public function get_dbtime(){
        return $this -> get_array_one('SELECT NOW() AS value', 'value');
    }

    /**
     * @name: get_sql_list
     * @description: 获取当前所执行的sql
     * @scope: public
     * @return: array
     * @create: 2014-10-13
    **/
    public function get_sql_list(){
        return $this -> list_sql;
    }
}
?>