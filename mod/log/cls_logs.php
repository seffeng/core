<?php
/**
 * @copyright 2014
 * @description: cls_logs
 * @file: cls_logs.php
 * @charset: UTF-8
 * @create: 2014-10-11
 * @version 1.0
**/

class cls_logs{
    private $log_url_data           = NULL;                 //日志读写目录

    /**
     * @name: set_conf
     * @description: 设置必要的参数数据
     * @scope: public
     * @param: string 日志的目录
     * @return: void
     * @create: 2014-10-11
    **/
    public function set_conf($log_url_data){
        if(substr($log_url_data, -1) == '/') $this -> log_url_data = $log_url_data;
    }

    /**
     * @name: get_log_file_url
     * @description: 获取日志文件路径
     * @scope: public
     * @param: string 操作类型信息
     * @param: integer 操作时间 [default-NULL][CORE_THIS_TIME]
     * @return: string
     * @create: 2014-10-11
    **/
    public function get_log_file_url($option_type, $time=NULL){
        if($time < 1) $time = CORE_THIS_TIME;
        return $this -> log_url_data.$option_type.date('Ymd', $time).'.php';
    }

    /**
     * @name: log_write
     * @description: 日志文件的读写
     * @scope: public
     * @param: string 操作内容 [default-NULL]
     * @param: string 操作类型信息 [default-NULL]
     * @param: string 操作关键信息 [default-NULL]
     * @param: integer 操作时间 [default-NULL][CORE_THIS_TIME]
     * @param: string 操作IP [default-NULL][CORE_THIS_IPS]
     * @return: void
     * @create: 2014-10-11
    **/
    public function log_write($option_content=NULL, $option_type=NULL, $option_key=NULL, $option_time=NULL, $option_ip=NULL){
        $option_time        = is_exists($option_time,     'var')? preg_replace(array("'\['", "'\]'", "'\('", "'\)'", "'\{'", "'\}'"), array('', '', '', '', '', ''), $option_time) : CORE_THIS_TIME;
        $option_ip          = is_exists($option_ip,       'var')? preg_replace(array("'\['", "'\]'", "'\('", "'\)'", "'\{'", "'\}'"), array('', '', '', '', '', ''), $option_ip) : CORE_THIS_IPS;
        $option_key         = is_exists($option_key,      'var')? preg_replace(array("'\['", "'\]'", "'\('", "'\)'", "'\{'", "'\}'"), array('', '', '', '', '', ''), $option_key) : 'NULL';
        $option_type        = is_exists($option_type,     'var')? preg_replace(array("'\['", "'\]'", "'\('", "'\)'", "'\{'", "'\}'"), array('', '', '', '', '', ''), $option_type) : 'NULL';
        $option_content     = is_exists($option_content,  'var')? preg_replace(array("'\['", "'\]'", "'\('", "'\)'", "'\{'", "'\}'"), array('', '', '', '', '', ''), $option_content) : 'NULL';
        $option_type        = filter_name($option_type);
        $log_file_name      = $this -> get_log_file_url($option_type, CORE_THIS_TIME);
        $write_string       = '['.date('m-d H:i:s', $option_time).']['.$option_ip.']['.$option_type.']['.$option_key.']['.$option_content.']'."\r\n";
        if(!is_exists($log_file_name,'file')){
            $tmp_time = date('Y-m-d H:i:s', CORE_THIS_TIME);
            $write_string = "<?php echo '<meta http-equiv=\"refresh\" content=\"0;url=../\">'; exit;?>\r\n/**************************************************\r\n*    TO---LOG [".($option_key != 'NULL' ? $option_key : 'ALL')."]\r\n*    Time：".$tmp_time."\r\n**************************************************/\r\n\r\n".$write_string;
        }
        if(!$this -> write_string_file($log_file_name, $write_string)) die('Write Log File ERROR.');
    }

    /**
     * @name: write_string_file
     * @description: 将字符串写入对应文件[自动创建目录]
     * @scope: private
     * @param: string 文件全路径
     * @param: string 文件内容
     * @return: boolean
     * @create: 2014-10-11
    **/
    private function write_string_file($file, $string){
        $return = TRUE;
        if(!is_type($file, 'string') || $file == '' || substr($file, -1) == '/' || !is_type($string, 'string') || (substr($file, 0, 1) != '/' && substr($file, 1, 2) != ':/')) $return = FALSE;
        if($return){
            $folder = dirname($file);
            if(!is_exists($folder, 'dir')){
                $tmp = '';
                foreach(explode('/', $folder) as $key => $val){
                    $tmp .= $val.'/';
                    if($key >= 3 && !is_exists($tmp, 'dir')){   //目录
                        if(!mkdir($tmp, 0777)){
                            $return = FALSE;
                            break;
                        }
                    }
                }
            }
            if($return && !is_exists($folder, 'dir')) $return = FALSE;
            if($return){
                if($fp = @fopen($file, 'a+')){
                    !@fwrite($fp, $string) && $return = FALSE;
                    @fclose($fp);
                }else{
                    $return = FALSE;
                }
            }
        }
        return $return;
    }
}
?>