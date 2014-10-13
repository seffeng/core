<?php
/**
 * @copyright 2014
 * @description: cls_memcached
 * @file: cls_memcached.php
 * @charset: UTF-8
 * @create: 2014-10-13
 * @version 1.0
**/

class cls_memcached{
    private $memcache   = NULL;                 //连接MEMCACHE对象
    private $host       = NULL;                 //连接MEMCACHE主机
    private $port       = NULL;                 //连接MEMCACHE端口
    private $time_out   = 3;                    //连接MEMCACHE超时时间[秒]
    private $expire     = 0;                    //连接MEMCACHE设置变量有效期[0-永久,max-2592000][秒]
    private $flag       = 0;                    //连接MEMCACHE设置变量压缩方式[0-默认]

    /**
     * @name: __destruct
     * @description: 系统析构函数释放资源
     * @scope: public
     * @return: void
     * @create: 2014-10-13
    **/
    public function __destruct(){
        $this -> close();
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
        isset($array['MEMCACHE_HOST']) && $this -> host = $array['MEMCACHE_HOST'];
        isset($array['MEMCACHE_PORT']) && $this -> port = $array['MEMCACHE_PORT'];
        isset($array['MEMCACHE_TIME_OUT']) && $this -> time_out = $array['MEMCACHE_TIME_OUT'];
        isset($array['MEMCACHE_EXPIRE']) && $this -> expire = $array['MEMCACHE_EXPIRE'];
        isset($array['MEMCACHE_FLAG']) && $this -> flag = $array['MEMCACHE_FLAG'];
    }

    /**
     * @name: is_connect
     * @description: 是否连接MEMCACHE
     * @scope: public
     * @return: boolean
     * @create: 2014-10-13
    **/
    public function is_connect(){
        return (!isset($this -> memcached) || $this -> memcached === NULL) ? FALSE : TRUE;
    }

    /**
     * @name: connect
     * @description: 连接MEMCACHE服务器
     * @scope: public
     * @param: string MEMCACHE服务器主机[unix:/] default[NULL]
     * @param: integer MEMCACHE服务器端口 default[NULL] [unix:/必须0]
     * @param: integer MEMCACHE服务器超时时间[秒] default[NULL]
     * @return: boolean
     * @create: 2014-10-13
    **/
    public function connect($host = NULL, $port = NULL, $time_out = NULL){
        if(!is_exists('Memcache', 'class')) return FALSE;
        $memcache = new Memcache();
        if($host == '') $host = $this -> host;
        if($port == '') $port = $this -> port;
        if($time_out == '') $time_out = $this -> time_out;
        $time_out = intval($time_out);
        $time_out = $time_out < 1 ? 0 : $time_out;
        $port = intval($port);
        if($port > 65535 || $port < 0) return FALSE;
        if(strtolower(substr($host, 0, 6)) == 'unix:/' && $port != 0) return FALSE;
        if(@$memcache -> connect($host, $port, $time_out)){
            $this -> memcached = $memcache;
            return TRUE;
        }
        return FALSE;
    }

    /**
     * @name: reconnect
     * @description: 重新连接的MEMCACHE服务器
     * @scope: public
     * @return: boolean
     * @create: 2014-10-13
    **/
    public function reconnect(){
        $this -> close();
        return $this -> connect();
    }

    /**
     * @name: close
     * @description: 关闭当前连接的MEMCACHE服务器
     * @scope: public
     * @return: void
     * @create: 2014-10-13
    **/
    public function close(){
        if(isset($this -> memcached) && $this -> memcached !== NULL){
            $this -> memcached -> close();
            $this -> memcached = NULL;
        }
    }

    /**
     * @name: set
     * @description: 设置MEMCACHE缓存数据
     * @scope: public
     * @param: string 缓存KEY
     * @param: mixed 缓存数据
     * @param: integer 缓存数据保留时间 default[NULL-0-永久]
     * @param: integer 存储压缩模式 default[NULL-0]
     * @return: boolean
     * @create: 2014-10-13
    **/
    public function set($key, $val, $expire = NULL, $flag = NULL){
        $flag === NULL && $flag = $this -> flag;
        $flag = $flag === NULL ? 0 : $flag;
        $expire === NULL && $expire = $this -> expire;
        $expire = intval($expire);
        if($expire < 1){
            return $this -> memcached -> set($key, $val, $flag);
        }else{
            $expire = $expire > 2592000 ? 2592000 : $expire;
            return $this -> memcached -> set($key, $val, $flag, $expire);
        }
    }

    /**
     * @name: get
     * @description: 获取MEMCACHE缓存数据
     * @scope: public
     * @param: string 缓存KEY
     * @param: integer 存储压缩模式 default[NULL-0]与set需一直
     * @return: mixed
     * @create: 2014-10-13
    **/
    public function get($key, $flag = 0){
        $flag === NULL && $flag = $this -> flag;
        $flag = $flag === NULL ? 0 : $flag;
        return $this -> memcached -> get($key, $flag);
    }

    /**
     * @name: delete
     * @description: 删除MEMCACHE数据
     * @scope: public
     * @param: string 缓存KEY
     * @param: integer 延时删除时间[秒] default[0-现在删除]
     * @return: boolean
     * @create: 2014-10-13
    **/
    public function delete($key, $time_out = 0){
        return $this -> memcached -> delete($key, $time_out);
    }

    /**
     * @name: inc
     * @description: 自增数据
     * @scope: public
     * @param: string 缓存KEY
     * @return: boolean
     * @create: 2014-10-13
    **/
    public function inc($key){
        return $this -> memcached -> increment($key);
    }

    /**
     * @name: dec
     * @description: 自减数据
     * @scope: public
     * @param: string 缓存KEY
     * @return: boolean
     * @create: 2014-10-13
    **/
    public function dec($key){
        return $this -> memcached -> decrement($key);
    }

    /**
     * @name: flush
     * @description: 刷新MEMCACHE缓存,清楚全部MEMCACHE数据
     * @scope: public
     * @return: boolean
     * @create: 2014-10-13
    **/
    public function flush(){
        return $this -> memcached -> flush();
    }

    /**
     * @name: get_status
     * @description: 获取MEMCACHE状态
     * @scope: public
     * @return: array
     * @create: 2014-10-13
    **/
    public function get_status(){
        return $this -> get_extended_stats();
    }

    /**
     * @name: add
     * @description: 添加MEMCACHE缓存数据,如果key存在则不添加不替换
     * @scope: public
     * @param: string 缓存KEY
     * @param: mixed 缓存数据
     * @param: integer 存储压缩模式 default[NULL-0]
     * @param: integer 缓存数据保留时间 default[NULL-0-永久]
     * @return: boolean
     * @create: 2014-10-13
    **/
    public function add($key, $val, $flag = NULL, $expire = NULL){
        $flag === NULL && $flag = $this -> flag;
        $flag = $flag === NULL ? 0 : $flag;
        $expire === NULL && $expire = $this -> expire;
        $expire = intval($expire);
        $expire = ($expire < 0 ? 0 : ($expire > 2592000 ? 2592000 : $expire));
        return $this -> memcached -> add($key, $val, $flag, $expire);
    }

    /**
     * @name: get_extended_stats
     * @description: 获取MEMCACHE扩展信息
     * @scope: public
     * @param: string 信息类型[reset, malloc, maps, cachedump, slabs, items, sizes]
     * @param: integer 数据块ID[default 0]
     * @param: integer 数据记录数[default 100]
     * @return: array
     * @create: 2014-10-13
    **/
    public function get_extended_stats($type=NULL, $slabid=0, $limit=100){
        $type = strtolower(trim($type));
        if($type!='' && !in_array($type, array('reset', 'malloc', 'maps', 'cachedump', 'slabs', 'items', 'sizes'))) return FALSE;
        return $this -> memcached -> getExtendedStats($type, $slabid, $limit);
    }

    /**
     * @name: get_all_key
     * @description: 获取memcache服务器的全部KEY
     * @scope: public
     * @return: array
     * @create: 2014-10-13
    **/
    public function get_all_key(){
        $mem_key_all = array();
        $all_items = $this -> get_extended_stats('items');
        if(is_type($all_items, 'array') && !is_empty($all_items)) foreach($all_items as $server => $items){
            if(isset($items['items']) && is_type($items['items'], 'array') && !is_empty($items['items'])) foreach($items['items'] as $slabid => $data){
                $items_val = $this -> get_extended_stats('cachedump', $slabid, 0);
                if(is_type($items_val, 'array') && !is_empty($items_val)) foreach($items_val as $servers => $item){
                    if(is_type($item, 'array') && !is_empty($item)) foreach($item as $mem_key => $mem_info){
                        $mem_key = trim($mem_key);
                        if($mem_key != '' && !in_array($mem_key, $mem_key_all)) $mem_key_all[] = $mem_key;
                    }
                }
            }
        }
        return $mem_key_all;
    }

    /**
     * @name: get_all_value
     * @description: 获取memcache服务器的全部KEY和值
     * @scope: public
     * @return: array
     * @create: 2014-10-13
    **/
    public function get_all_value(){
        $mem_key_all = $this -> get_all_key();
        $mem_value_all = array();
        if(is_type($mem_key_all, 'array') && !is_empty($mem_key_all)) foreach($mem_key_all as $mem_key){
            $mem_value_all[$mem_key] = $this -> get($mem_key);
        }
        return $mem_value_all;
    }
}
?>
