<?php
/**
 * @copyright 2014
 * @description: cls_uasparser
 * @file: cls_uasparser.php
 * @charset: UTF-8
 * @create: 2014-10-13
 * @version 1.0
**/

class cls_uasparser{
    public $updateInterval      = 172800;       //更新数据频率2day
    protected $debug            = FALSE;        //是否开启调试模式
    public $timeout             = 60;           //网络请求超时时间[秒]
    protected $dodownloads      = FALSE;        //是否开启数据下载
    protected $cache_dir        = NULL;         //下载数据缓存目录
    protected $data             = NULL;         //被格式化的数据
    protected static $ini_url   = 'http://user-agent-string.info/rpc/get_data.php?key=free&format=ini';         //数据地址全部数据
    protected static $ver_url   = 'http://user-agent-string.info/rpc/get_data.php?key=free&format=ini&ver=y';   //数据地址版本信息
    protected static $md5_url   = 'http://user-agent-string.info/rpc/get_data.php?format=ini&md5=y';            //数据地址文件信息
    protected static $info_url  = 'http://user-agent-string.info';                                              //数据地址关于信息

    /**
     * @name: __construct
     * @description: 构造函数
     * @scope: public static
     * @param: string 对象标签 default[NULL]
     * @param: boolean 是否新new对象 default[FALSE]
     * @return: object
     * @create: 2014-10-13
    **/
    public function __construct(){
        $this -> set_cache_dir(get_cur_root(__FILE__).'uagent/');
    }

    /**
     * @name: debug
     * @description: 调试打印消息
     * @scope: protected
     * @param: string 被打印的消息
     * @return: void
     * @create: 2014-10-13
    **/
    protected function debug($msg){
        if($this -> debug) echo gmdate('Y-m-d H:i:s')." \t".$msg."\n";
    }

    /**
     * @name: parse
     * @description: 格式化useragent信息
     * @scope: public
     * @param: string 被格式化useragent信息
     * @return: array
     * @create: 2014-10-13
    **/
    public function parse($useragent = NULL){
        $browser_id = $os_id = NULL;
        $result = array();
        $result['type']         = 'unknown';
        $result['ua_family']    = 'unknown';
        $result['ua_name']      = 'unknown';
        $result['os_family']    = 'unknown';
        $result['os_name']      = 'unknown';
        if((!isset($useragent) || $useragent == '') && isset($_SERVER['HTTP_USER_AGENT'])) $useragent = $_SERVER['HTTP_USER_AGENT'];
        if(!$this -> data) $this -> data = $this -> load_data();
        if(!$this -> data || !isset($useragent) || $useragent == '') return $result;
        foreach($this -> data['robots'] as $val){
            if($val[0] == $useragent){
                $result['type'] = 'Robot';
                if($val[1]) $result['ua_family'] = $val[1];
                if($val[2]) $result['ua_name'] = $val[2];
                if($val[7]){    //OS set
                    $os_data = $this -> data['os'][$val[7]];
                    if($os_data[0]) $result['os_family'] = $os_data[0];
                    if($os_data[1]) $result['os_name'] = $os_data[1];
                }
                if($val[8]) $result['ua_info_url'] = self::$info_url.$val[8];
                $result['device_type'] = $this -> data['device'][1][0];
                $result['device_icon'] = $this -> data['device'][1][1];
                $result['device_info_url'] = self::$info_url.$this -> data['device'][1][2];
                return $result;
            }
        }
        //Find a browser based on the regex
        foreach($this -> data['browser_reg'] as $val){
            if(preg_match($val[0], $useragent, $info)){
                $browser_id = $val[1];
                break;
            }
        }
        //A valid browser was found
        if($browser_id){ // Browser detail
            $browser_data = $this -> data['browser'][$browser_id];
            if($this -> data['browser_type'][$browser_data[0]][0]){
                $result['type'] = $this -> data['browser_type'][$browser_data[0]][0];
            }
            if(isset($info[1])) $result['ua_version'] = $info[1];
            if($browser_data[1]) $result['ua_family'] = $browser_data[1];
            if($browser_data[1]) $result['ua_name'] = $browser_data[1].(isset($info[1])?(' '.$info[1]):'');
        }
        //Browser OS, does this browser match contain a reference to an os
        if(isset($this -> data['browser_os'][$browser_id])){ // OS detail
            $os_id = $this -> data['browser_os'][$browser_id][0];
            $os_data = $this -> data['os'][$os_id];
            if($os_data[0]) $result['os_family'] = $os_data[0];
            if($os_data[1]) $result['os_name'] = $os_data[1];
        }
        if(!$os_id) foreach($this -> data['os_reg'] as $val){
            if(preg_match($val[0], $useragent)){
                $os_id = $val[1];
                break;
            }
        }
        //A valid OS was found
        if($os_id){
            $os_data = $this -> data['os'][$os_id];
            if($os_data[0]) $result['os_family'] = $os_data[0];
            if($os_data[1]) $result['os_name'] = $os_data[1];
        }
        return $result;
    }

    /**
     * @name: load_data
     * @description: 加载基础规则数据
     * @scope: protected
     * @return: boolean
     * @create: 2014-10-13
    **/
    protected function load_data(){
        if(!file_exists($this -> cache_dir)){
            $this -> debug('Cache file not found');
            return FALSE;
        }
        if(file_exists($this -> cache_dir.'/cache.ini')){
            $cacheini = parse_ini_file($this -> cache_dir.'/cache.ini');
            if($cacheini['lastupdatestatus'] != '1' || $cacheini['lastupdate'] < time() - $this -> updateInterval){
                if($this -> dodownloads){
                    $this -> download_data();
                }else{
                    $this -> debug('Downloads suppressed, using old data');
                }
            }
        }else{
            if(!$this -> dodownloads) $this -> debug('Data missing - Doing download even though downloads are suppressed');
            $this -> download_data();
        }
        if(file_exists($this -> cache_dir.'/uasdata.ini')){
            return parse_ini_file($this -> cache_dir.'/uasdata.ini', TRUE);
        }else{
            $this -> debug('Data file not found');
        }
        return FALSE;
    }

    /**
     * @name: download_data
     * @description: 下载新的数据并缓存
     * @scope: public
     * @param: boolean 是否允许覆盖
     * @return: boolean
     * @create: 2014-10-13
    **/
    public function download_data($force = FALSE){
        $status = FALSE;
        if(!ini_get('allow_url_fopen') && !function_exists('curl_init')){
            $this -> debug('Fopen wrappers and curl unavailable, cannot continue');
            return $status;
        }
        $cacheini = array();
        if(file_exists($this -> cache_dir.'/cache.ini')) $cacheini = parse_ini_file($this -> cache_dir.'/cache.ini');
        $ver = $this -> get_contents(self::$ver_url, $this -> timeout);
        if(preg_match('/^[0-9]{8}-[0-9]{2}$/', $ver)){ //Should be a date and version string like '20130529-01'
            if(array_key_exists('localversion', $cacheini)){
                if($ver <= $cacheini['localversion']){ //Version on server is same as or older than what we already have
                    if($force){
                        $this -> debug('Existing file is current, but forcing a download anyway.');
                    }else{
                        $this -> debug('Download skipped, existing file is current.');
                        $status = TRUE;
                        $this -> write_cache_ini($ver, $status);
                        return $status;
                    }
                }
            }
        }else{
            $this -> debug('Version string format mismatch.');
            $ver = 'none';
        }
        $ini = $this -> get_contents(self::$ini_url, $this -> timeout);
        if(!empty($ini)){
            $md5hash = $this -> get_contents(self::$md5_url, $this -> timeout);
            if(!empty($md5hash)){
                if(md5($ini) == $md5hash){
                    $written = file_put_contents($this -> cache_dir . '/uasdata.ini', $ini, LOCK_EX);
                    if($written === FALSE){
                        $this -> debug('Failed to write data file to '.$this -> cache_dir.'/uasdata.ini');
                    }else{
                        $status = TRUE;
                    }
                }else{
                    $this -> debug('Data file hash mismatch.');
                }
            }else{
                $this -> debug('Failed to fetch hash file.');
            }
        }else{
            $this -> debug('Failed to fetch data file.');
        }
        $this -> write_cache_ini($ver, $status);
        return $status;
    }

    /**
     * @name: write_cache_ini
     * @description: 写入配置的缓存信息
     * @scope: protected
     * @param: string 版本信息
     * @param: string 状态信息
     * @return: boolean
     * @create: 2014-10-13
    **/
    protected function write_cache_ini($ver, $status){
        $cacheini = '; cache info for class UASparser - http://user-agent-string.info/download/UASparser'."\n";
        $cacheini .= '[main]'."\n";
        $cacheini .= 'localversion = "'.$ver."\"\n";
        $cacheini .= 'lastupdate = "'.time()."\"\n";
        $cacheini .= 'lastupdatestatus = "'.$status."\"\n";
        $written = file_put_contents($this -> cache_dir.'/cache.ini', $cacheini, LOCK_EX);
        if($written === FALSE){
            $this -> debug('Failed to write cache file to '.$this -> cache_dir.'/cache.ini');
            return FALSE;
        }
        return TRUE;
    }

    /**
     * @name: get_contents
     * @description: 获取网络地址内容
     * @scope: protected
     * @param: string 被获取的网络地址
     * @param: integer 超时时间
     * @return: string
     * @create: 2014-10-13
    **/
    protected function get_contents($url, $timeout = 300){
        $data = '';
        $starttime = microtime(TRUE);
        if(ini_get('allow_url_fopen')){
            $fp = fopen($url, 'rb', FALSE, stream_context_create(array('http' => array('timeout' => $timeout, 'header' => "Accept-Encoding: gzip\r\n"))));
            if(is_resource($fp)){
                $data = stream_get_contents($fp);
                $res = stream_get_meta_data($fp);
                if(array_key_exists('wrapper_data', $res)){
                    foreach($res['wrapper_data'] as $dataline){
                        if($dataline == 'Content-Encoding: gzip'){ //Data was compressed
                            $data = gzinflate(substr($data, 10, -8)); //Uncompress data
                            $this -> debug('Successfully uncompressed data');
                            break;
                        }
                    }
                }
                fclose($fp);
                if(empty($data)){
                    if($this -> debug){
                        if($res['timed_out']){
                            $this -> debug('Fetching URL failed due to timeout: '.$url);
                        }else{
                            $this -> debug('Fetching URL failed: '.$url);
                        }
                    }
                    $data = '';
                }else{
                    $this -> debug('Fetching URL with fopen succeeded: '.$url.'. '.strlen($data).' bytes in '.(microtime(TRUE) - $starttime).' sec.');
                }
            }else{
                $this -> debug('Opening URL failed: '.$url);
            }
        }elseif(function_exists('curl_init')){
            $ch = curl_init($url);
            curl_setopt_array($ch, array(
                    CURLOPT_TIMEOUT => $timeout,
                    CURLOPT_CONNECTTIMEOUT => $timeout,
                    CURLOPT_RETURNTRANSFER => TRUE,
                    CURLOPT_ENCODING => 'gzip'
                )
            );
            $data = curl_exec($ch);
            if($data !== FALSE and curl_errno($ch) == 0){
                $this -> debug('Fetching URL with curl succeeded: '.$url.'. '.strlen($data).' bytes in '.(microtime(TRUE) - $starttime).' sec.');
            }else{
                $this -> debug('Opening URL with curl failed: '.$url.' '.curl_error($ch));
                $data = '';
            }
            curl_close($ch);
        }
        return $data;
    }

    /**
     * @name: set_cache_dir
     * @description: 设置cache目录
     * @scope: public
     * @param: string cache目录
     * @return: boolean
     * @create: 2014-10-13
    **/
    public function set_cache_dir($cache_dir){
        $this -> debug('Setting cache dir to '.$cache_dir);
        if(!file_exists($cache_dir)) mkdir($cache_dir, 0777, TRUE);
        if(!is_dir($cache_dir)){
            $this -> debug('Cache dir('.$cache_dir.') is not a directory or not writable');
            return FALSE;
        }
        $cache_dir = realpath($cache_dir);
        $this -> cache_dir = $cache_dir;
        return TRUE;
    }

    /**
     * @name: set_dodownloads
     * @description: 设置是否开启下载数据
     * @scope: public
     * @param: boolean 是否开启下载
     * @return: void
     * @create: 2014-10-13
    **/
    public function set_dodownloads($dodownloads){
        $this -> dodownloads = $dodownloads ? TRUE : FALSE;
    }
}
?>