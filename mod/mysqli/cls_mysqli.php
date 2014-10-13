<?php
/**
 * @copyright 2014
 * @description: cls_mysqli
 * @file: cls_mysqli.php
 * @charset: UTF-8
 * @create: 2014-10-13
 * @version 1.0
**/

class cls_mysqli extends cls_mod{
    private $object_mark            = NULL;                 //object.mark.name
    protected $conflist             = array('MYSQLI_HOST','MYSQLI_USER','MYSQLI_PASS','MYSQLI_PORT','MYSQLI_DBNAME','MYSQLI_CHARSET','MYSQLI_TABLE_PREFIX',
                                    'MYSQLI_ISCACHE','MYSQLI_CACHE_TIME','MYSQLI_CACHE','MYSQLI_ISRW','MYSQLI_READ','MYSQLI_WRITE');
    private $cache_list             = array();              //cache.list
    private $cache_max              = 30;                   //cache.max

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
     * @description: 设置配置信息[默认连接-写入连接-读取连接]
     * @scope: public
     * @param: mixed 被配置的数据或名称
     * @param: mixed 被配置的数据值
     * @return: $this
     * @create: 2014-10-13
    **/
    public function set_conf($key, $val=NULL){
        $set_conf = parent::set_conf($key, $val);
        $conf = $this -> get_conf();    //必须配置默认连接[否则自动配置默认连接]
        if(isset($conf['MYSQLI_ISRW']) && $conf['MYSQLI_ISRW']){    //开启了读写分离
            isset($conf['MYSQLI_WRITE']) && $this -> get_mysqlis('MYSQLI_WRITE') -> set_conf($conf['MYSQLI_WRITE']);
            isset($conf['MYSQLI_READ']) && $this -> get_mysqlis('MYSQLI_READ') -> set_conf($conf['MYSQLI_READ']);
        }
        if(isset($conf['MYSQLI_ISCACHE']) && $conf['MYSQLI_ISCACHE']){    //开启了CACHE
            isset($conf['MYSQLI_CACHE']) && $this -> get_cache('MYSQLI_CACHE') -> set_conf($conf['MYSQLI_CACHE']);
        }
        if(isset($conf['MYSQLI_PASS']) && $conf['MYSQLI_PASS'] != ''){
            unset($conf['MYSQLI_ISRW'], $conf['MYSQLI_READ'], $conf['MYSQLI_WRITE'], $conf['MYSQLI_ISCACHE'], $conf['MYSQLI_CACHE']);
            $this -> get_mysqlis() -> set_conf($conf);
        }else if(isset($conf['MYSQLI_WRITE']) && isset($conf['MYSQLI_WRITE']['MYSQLI_PASS']) && $conf['MYSQLI_WRITE']['MYSQLI_PASS'] != ''){
            $this -> get_mysqlis() -> set_conf($conf['MYSQLI_WRITE']);
            parent::set_conf('MYSQLI_TABLE_PREFIX', $conf['MYSQLI_WRITE']['MYSQLI_TABLE_PREFIX']);
        }else if(isset($conf['MYSQLI_READ']) && isset($conf['MYSQLI_READ']['MYSQLI_PASS']) && $conf['MYSQLI_READ']['MYSQLI_PASS'] != ''){
            $this -> get_mysqlis() -> set_conf($conf['MYSQLI_READ']);
            parent::set_conf('MYSQLI_TABLE_PREFIX', $conf['MYSQLI_READ']['MYSQLI_TABLE_PREFIX']);
        }
        return $set_conf;
    }

    /**
     * @name: get_mysqlis
     * @description: 获取对象mysqlis
     * @scope: public
     * @param: string 对象标签 default[NULL]
     * @param: boolean 是否新new对象 default[FALSE]
     * @return: object
     * @create: 2014-10-13
    **/
    public function get_mysqlis($mark_name=NULL, $is_new=FALSE){
        $mark_name = ($mark_name == '') ? $this -> object_mark : $mark_name;
        return get_init('mysqlis', $mark_name, $is_new);
    }

    /**
     * @name: get_cache
     * @description: 获取对象cache
     * @scope: public
     * @param: string 对象标签 default[NULL]
     * @param: boolean 是否新new对象 default[FALSE]
     * @return: object
     * @create: 2014-10-13
    **/
    public function get_cache($mark_name=NULL, $is_new=FALSE){
        $mark_name = ($mark_name == '') ? $this -> object_mark : $mark_name;
        return get_mod('cache', $mark_name, $is_new);
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
        return $this -> get_mysqlis() -> connect($db_host, $db_user, $db_pass, $db_port);
    }

    /**
     * @name: set_dbname
     * @description: 选择数据库
     * @scope: public
     * @param: string 数据库名称
     * @param: resource 数据库连接 default[NULL]
     * @return: boolean
     * @create: 2014-10-13
    **/
    public function set_dbname($db_name=NULL, $db_link=NULL){
        return $this -> get_mysqlis() -> select_db($db_name, $db_link);
    }

    /**
     * @name: set_charset
     * @description: 选择数据库编码
     * @scope: public
     * @param: string 数据库编码
     * @param: resource 数据库连接 default[NULL]
     * @return: boolean
     * @create: 2014-10-13
    **/
    public function set_charset($db_chart=NULL, $db_link=NULL){
        return $this -> get_mysqlis() -> select_charct($db_chart, $db_link);
    }

    /**
     * @name: reconnect
     * @description: 连接数据库
     * @scope: public
     * @return: resource
     * @create: 2014-10-13
    **/
    public function reconnect(){
        return $this -> get_mysqlis() -> reconnect();
    }

    /**
     * @name: get_close
     * @description: 关闭数据库连接
     * @scope: public
     * @param: resource 数据库连接 default[NULL]
     * @return: void
     * @create: 2014-10-13
    **/
    public function get_close($db_link=NULL){
        return $this -> get_mysqlis() -> close($db_link);
    }

    /**
     * @name: get_free
     * @description: 释放资源
     * @scope: public
     * @param: mixed 数据库结果集体 default[NULL]
     * @return: void
     * @create: 2014-10-13
    **/
    public function get_free($query_result=NULL){
        return $this -> get_mysqlis() -> free($query_result);
    }

    /**
     * @name: get_query
     * @description: 执行sql语句
     * @scope: public
     * @param: string Sql语句
     * @param: resource 数据库连接 default[NULL]
     * @return: resource
     * @create: 2014-10-13
    **/
    public function get_query($sql, $db_link=NULL, $is_conn=TRUE){
        return $this -> get_mysqlis() -> query($sql, $db_link, $is_conn);
    }

    /**
     * @name: get_query_insert
     * @description: 执行插入sql语句
     * @scope: public
     * @param: string 插入表名
     * @param: array 插入表数据
     * @param: resource 数据库连接 default[NULL]
     * @return: resource
     * @create: 2014-10-13
    **/
    public function get_query_insert($table, $array, $db_link=NULL, $is_conn=TRUE){
        if($table == '' || count($array) < 1) return FALSE;
        $set_string = '';
        foreach($array as $key => $val) $set_string .= ($set_string == '' ? '' : ', ').'`'.$key.'`=\''.$val.'\'';
        if($set_string == '') return FALSE;
        $sql = 'INSERT INTO '.$this -> get_table_name($table).' SET '.$set_string;
        return $this -> get_mysqlis() -> query($sql, $db_link, $is_conn);
    }

    /**
     * @name: get_fetch_array
     * @description: 获取数组数据
     * @scope: public
     * @param: mixed query后的结果集合或者Sql语句
     * @param: integer 返回结果索引[1-字段,2-数字,3-混合]
     * @param: resource 数据库连接 default[NULL]
     * @return: array
     * @create: 2014-10-13
    **/
    public function get_fetch_array($sql, $result=1, $db_link=NULL){
        if(is_type($sql, 'string')){
            $sql_cache = $this -> get_sql_cache($sql, 'FETCH_ARRAY:');
            switch($sql_cache['T']){
                case 'RETURN' : { return $sql_cache['D']; break; }
                case 'OBJECT' : {
                    $sql_res = $sql_cache['D'] -> fetch_array($sql, $result, $db_link);
                    if($sql_res && isset($sql_cache['K']) && $sql_cache['K'] != ''){
                        //WIRTE CACHE
                        $cache_time = $this -> get_conf('MYSQLI_CACHE_TIME');
                        $cache_time = max($cache_time, 0);
                        $this -> get_cache('MYSQLI_CACHE') -> set($sql_cache['K'], $sql_res, $cache_time);
                    }
                    return $sql_res;
                    break;
                }
            }
        }
        return $this -> get_mysqlis() -> fetch_array($sql, $result, $db_link);
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
        if(is_type($sql, 'string')){
            $sql_cache = $this -> get_sql_cache($sql, 'GET_ARRAY:');
            switch($sql_cache['T']){
                case 'RETURN' : { return $sql_cache['D']; break; }
                case 'OBJECT' : {
                    $sql_res = $sql_cache['D'] -> get_array($sql, $result, $db_link);
                    if($sql_res && isset($sql_cache['K']) && $sql_cache['K'] != ''){
                        //WIRTE CACHE
                        $cache_time = $this -> get_conf('MYSQLI_CACHE_TIME');
                        $cache_time = max($cache_time, 0);
                        $this -> get_cache('MYSQLI_CACHE') -> set($sql_cache['K'], $sql_res, $cache_time);
                    }
                    return $sql_res;
                    break;
                }
            }
        }
        return $this -> get_mysqlis() -> get_array($sql, $result, $db_link);
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
        if(is_type($sql, 'string')){
            $sql_cache = $this -> get_sql_cache($sql, 'GET_ARRAY_ONE:');
            switch($sql_cache['T']){
                case 'RETURN' : { return $sql_cache['D']; break; }
                case 'OBJECT' : {
                    $sql_res = $sql_cache['D'] -> get_array_one($sql, $result, $db_link);
                    if($sql_res && isset($sql_cache['K']) && $sql_cache['K'] != ''){
                        //WIRTE CACHE
                        $cache_time = $this -> get_conf('MYSQLI_CACHE_TIME');
                        $cache_time = max($cache_time, 0);
                        $this -> get_cache('MYSQLI_CACHE') -> set($sql_cache['K'], $sql_res, $cache_time);
                    }
                    return $sql_res;
                    break;
                }
            }
        }
        return $this -> get_mysqlis() -> get_array_one($sql, $result, $db_link);
    }

    /**
     * @name: get_sql_cache
     * @description: 获取被sqlcache结果和读写分离对象
     * @scope: private
     * @param: string Sql语句
     * @param: string Sql操作函数区分 default[NULL]
     * @return: array
     * @create: 2014-10-13
    **/
    private function get_sql_cache($sql, $get_fun=NULL){
        if($this -> get_sql_option($sql) == 'SELECT'){  //查询
            if($this -> get_conf('MYSQLI_ISCACHE')){
                $sql_key = $this -> get_cache_key($get_fun.preg_replace_callback('/\s+sql_no_cache\s+/i', function($match){return ' ';}, $sql));    //读取唯一KEY
                if(!$this -> sql_is_nocache($sql)){  //READ CACHE
                    if($sql_key){
                        $sql_val = $this -> get_cache('MYSQLI_CACHE') -> get($sql_key);
                        if($sql_val !== FALSE){ //读取到cache数据直接返回
                            if(count($this -> cache_list) >= $this -> cache_max) array_shift($this -> cache_list);
                            $this -> cache_list[$sql_key] = $sql;
                            return array('T' => 'RETURN', 'D' => $sql_val);
                        }
                    }
                }
            }else{
                $sql_key = '';  //NOT-CACHE
            }
            if($this -> get_conf('MYSQLI_ISRW')){
                return array('T' => 'OBJECT', 'D' => $this -> get_mysqlis('MYSQLI_READ'), 'K' => $sql_key);
            }else{
                return array('T' => 'OBJECT', 'D' => $this -> get_mysqlis(), 'K' => $sql_key);
            }
        }else{  //写入
            if($this -> get_conf('MYSQLI_ISRW')){
                return array('T' => 'OBJECT', 'D' => $this -> get_mysqlis('MYSQLI_WRITE'));
            }else{
                return array('T' => 'OBJECT', 'D' => $this -> get_mysqlis());
            }
        }
    }

    /**
     * @name: get_cache_sql_list
     * @description: 获取被cache命中的Sql语句[最大30条]
     * @scope: public
     * @return: array
     * @create: 2014-10-13
    **/
    public function get_cache_sql_list(){
        return $this -> cache_list;
    }

    /**
     * @name: get_cache_key
     * @description: 获取Sql语句的MD5用作cacheKEY
     * @scope: private
     * @param: string Sql语句
     * @return: string
     * @create: 2014-10-13
    **/
    private function get_cache_key($sql){
        $sql = trim($sql);
        if($sql!='') return strtolower(md5($sql));
        return FALSE;
    }

    /**
     * @name: get_sql_option
     * @description: 判定Sql语句是否是查询
     * @scope: private
     * @param: string Sql语句
     * @return: string
     * @create: 2014-10-13
    **/
    private function get_sql_option($sql){
        $sql = trim($sql);
        if($sql!='' && preg_match('/^([^\s]+)/', $sql, $match)) return strtoupper($match[1]);
        return '';
    }

    /**
     * @name: sql_is_nocache
     * @description: 判定Sql语句是否不要缓存
     * @scope: private
     * @param: string Sql语句
     * @return: boolean
     * @create: 2014-10-13
    **/
    private function sql_is_nocache($sql){
        $sql = trim($sql);
        if($sql!='' && preg_match('/SQL_NO_CACHE/i', $sql, $match)) return TRUE;
        return FALSE;
    }

    /**
     * @name: get_insert_id
     * @description: 获取数据最后插入Id
     * @scope: public
     * @param: resource 数据库连接 default[NULL]
     * @return: integer
     * @create: 2014-10-13
    **/
    public function get_insert_id($db_link=NULL){
        return $this -> get_mysqlis() -> insert_id($db_link);
    }

    /**
     * @name: get_affected_rows
     * @description: 取得前一次操作所影响的记录数
     * @scope: public
     * @param: resource 数据库连接 default[NULL]
     * @return: integer
     * @create: 2014-10-13
    **/
    public function get_affected_rows($db_link=NULL){
        return $this -> get_mysqlis() -> affected_rows($db_link);
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
        return $this -> get_mysqlis() -> rollback($db_link);
    }

    /**
     * @name: get_table_prefix
     * @description: 获取表前缀
     * @scope: public
     * @return: string
     * @create: 2014-10-13
    **/
    public function get_table_prefix(){
        return $this -> get_conf('MYSQLI_TABLE_PREFIX');
    }

    /**
     * @name: get_table_name
     * @description: 获取数据表名称和数据库名称
     * @scope: public
     * @param: string 数据表名
     * @param: string 数据库名 [default - NULL,FALSE-不返回数据库名]
     * @param: boolean 是否带点[`] [default - TRUE]
     * @return: string
     * @create: 2014-10-13
    **/
    public function get_table_name($table_name, $database_name=NULL, $is_dian=TRUE){
    	$tmp_array = explode('.', trim($table_name));
    	if(count($tmp_array) > 2){
    		return FALSE;
    	}else{
    		if(isset($tmp_array[1])){
    			$database_name = trim($tmp_array[0]);
    			$table_name = trim($tmp_array[1]);
    		}else{
    			if(is_empty($database_name)) $database_name = $this -> get_dbname();
    		}
    	}
    	if(is_empty($table_name)) return FALSE;
    	$table_prefix = $this -> get_table_prefix();
    	$table_name = $table_prefix.$table_name;
    	if($is_dian){
    	    $return = ($database_name === FALSE || is_empty($database_name)) ? '`'.$table_name.'`' : '`'.$database_name.'`.`'.$table_name.'`';
    	}else{
    	    $return = ($database_name === FALSE || is_empty($database_name)) ? $table_name : $database_name.'.'.$table_name;
    	}
    	return $return;
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
        return $this -> get_mysqlis() -> get_table_info($table_name, $filed_name);
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
        return $this -> get_mysqlis() -> get_table_fields($table_name, $filed_name);
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
        return $this -> get_mysqlis() -> get_dbname($is_mysql);
    }

    /**
     * @name: get_version
     * @description: 获取当前数据库版本
     * @scope: public
     * @return: string
     * @create: 2014-10-13
    **/
    public function get_version(){
        return $this -> get_mysqlis() -> get_version();
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
        return $this -> get_mysqlis() -> get_user($is_mysql);
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
        return $this -> get_mysqlis() -> get_host($is_mysql);
    }

    /**
     * @name: get_dbtime
     * @description: 获取当前数据库时间
     * @scope: public
     * @return: integer
     * @create: 2014-10-13
    **/
    public function get_dbtime(){
        return $this -> get_mysqlis() -> get_dbtime();
    }

    /**
     * @name: get_sql_list
     * @description: 获取当前所执行的sql
     * @scope: public
     * @return: array
     * @create: 2014-10-13
    **/
    public function get_sql_list(){
        return $this -> get_mysqlis() -> get_sql_list();
    }
}
?>