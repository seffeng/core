<?php
/**
 * @copyright 2014
 * @description: cls_ftps
 * @file: cls_ftps.php
 * @charset: UTF-8
 * @create: 2014-10-13
 * @version 1.0
**/

class cls_ftps{
    private $socket         = NULL; //SOCKET句柄
    private $data_socket    = NULL; //数据传送SOCKET句柄
    private $is_login       = FALSE;//是否登录用户
    private $port           = 21;   //SOCKET_IP
    private $data_port      = 0;    //数据传送端口
    private $ip             = '';   //SOCKET_端口
    private $user_name      = '';   //登录用户名
    private $password       = '';   //登录密码
    private $data_ip        = '';   //数据传送IP
    private $pwd            = '/';  //当前的服务器路径
    private $local_data     = './'; //保存文件的根路径
    private $last_type      = '';   //最后传输编码
    private $allow_cmd      = array();   //允许执行的FTP命令

    /**
     * @name: __destruct
     * @description: 释放资源
     * @scope: public
     * @return: void
     * @create: 2014-10-13
    **/
    public function __destruct(){
        $this -> close_data();
        $this -> close();
    }

    /**
     * @name: set
     * @desciption: 设置参数
     * @scope: public
     * @param: string IP地址
     * @param: string 用户名
     * @param: string 密码
     * @param: string 端口
     * @return: $this
     * @create: 2014-10-13
    **/
    public function set($ip, $user_name=NULL, $password=NULL, $port=21, $local_data=NULL){
        $ip != '' && $this -> ip = $ip;
        $user_name != '' && $this -> user_name = $user_name;
        $password != '' && $this -> password = $password;
        $port != '' && $this -> port = $port;
        $local_data != '' && $this -> local_data = $local_data;
        return $this;
    }

    /**
     * @name: connect
     * @desciption: 连接服务器,返回连接欢迎消息
     * @scope: public
     * @return: $this
     * @create: 2014-10-13
    **/
    public function connect(){
        $this -> socket = fsockopen($this -> ip, $this -> port);
        if(!$this -> socket) return FALSE;
        $array = $this -> get_data($this -> socket);
        if($this -> iscode(220, $array)) return $this;
        return FALSE;
    }

    /**
     * @name: is_connect
     * @desciption: 是否连接服务器
     * @scope: public
     * @return: boolean
     * @create: 2014-10-13
    **/
    public function is_connect(){
        if(!$this -> socket) return FALSE;
        return TRUE;
    }

    /**
     * @name: close
     * @desciption: 关闭服务器连接
     * @scope: public
     * @return: void
     * @create: 2014-10-13
    **/
    public function close(){
        $this -> set_data('QUIT'."\r\n", $this -> socket);
        if($this -> socket){
            fclose($this -> socket);
            $this -> socket = NULL;
        }
    }

    /**
     * @name: close_data
     * @desciption: 关闭服务器数据连接
     * @scope: public
     * @return: void
     * @create: 2014-10-13
    **/
    public function close_data(){
        if($this -> data_socket){
            fclose($this -> data_socket);
            $this -> data_socket = NULL;
        }
    }

    /**
     * @name: login
     * @desciption: 登录FTP服务
     * @scope: public
     * @param: string 用户名
     * @param: string 密码
     * @return: $this
     * @create: 2014-10-13
    **/
    public function login($user_name = NULL, $password = NULL){
        if(!$this -> is_connect() && !$this -> connect()) return FALSE;
        $user_name == '' && $user_name = $this -> user_name;
        $password == '' && $password = $this -> password;
        if(!$this -> set_data('USER '.$user_name."\r\n", $this -> socket)) return FALSE;
        $array = $this -> get_data($this -> socket);
        if(!$this -> iscode(331, $array)) return FALSE;
        if(!$this -> set_data('PASS '.$password."\r\n", $this -> socket)) return FALSE;
        $array = $this -> get_data($this -> socket);
        if(!$this -> iscode(230, $array)) return FALSE;
        if(!$this -> start()) return FALSE;
        $this -> is_login = TRUE;
        return $this;
    }

    /**
     * @name: logout
     * @desciption: 登出FTP服务
     * @scope: public
     * @return: boolean
     * @create: 2014-10-13
    **/
    public function logout(){
        if(!$this -> is_login) return TRUE;
        if(!$this -> set_data('QUIT '."\r\n", $this -> socket)) return FALSE;
        if(!$this -> get_data($this -> socket)) return FALSE;
        $this -> is_login = FALSE;
        return TRUE;
    }

    /**
     * @name: icon_var
     * @description: 转换字符串编码
     * @scope: private
     * @param: string 被转换的原字符串
     * @param: string 被转换的类型 default[GBK,UTF-8,I]
     * @return: string
     * @create: 2014-10-13
    **/
    private function icon_var($string, $type='GBK,UTF-8,I'){
        $type_array = explode(',', $type);
        $type_leng = count($type_array);
        if($type_leng != 2 && $type_leng != 3) return FALSE;
        $form = strtoupper(trim($type_array[0]));
        $to = strtoupper(trim($type_array[1]));
        $prame = '';
        $type_leng == 3 && ($prame = '//'.(strtoupper(trim($type_array[2]))=='T'?'TRANSLIT':'IGNORE'));
        return iconv($form, $to.$prame, $string);
    }

    /**
     * @name: iscode
     * @desciption: 判定状态码
     * @scope: public
     * @param: string 被允许的状态码
     * @param: string 结果状态码
     * @return: boolean
     * @create: 2014-10-13
    **/
    public function iscode($code, $array){
        $code = trim($code);
        if(strlen($code) < 1 || !isset($array['code']) || strlen($array['code']) != 3) return FALSE;
        if(!is_array($code)) $code = explode(',', $code);
        if(in_array($array['code'], $code)) return TRUE;
        return FALSE;
    }

    /**
     * @name: start
     * @desciption: 初始化FTP服务连接，在login之后
     * @scope: public
     * @return: boolean
     * @create: 2014-10-13
    **/
    public function start(){
        if(!$this -> set_data('SYST '."\r\n", $this -> socket)) return FALSE;
        $array = $this -> get_data($this -> socket);
        if(!$this -> iscode(215, $array)) return FALSE;
        if(!$this -> set_data('FEAT '."\r\n", $this -> socket)) return FALSE;
        $array = $this -> get_data($this -> socket);
        if(!$this -> iscode(211, $array)) return FALSE;
        foreach(explode("\n", $array['string']) as $key => $val){
            if($key < 1) continue;
            $val = trim($val);
            if(preg_match('/^[A-Z]+/i', $val, $match)) $this -> allow_cmd[] = $match[0];
        }
        if(!$this -> set_pwd('/')) return FALSE;
        if(!$this -> get_pwd()) return FALSE;
        if($this -> set_type('A')) return TRUE;
        return FALSE;
    }

    /**
     * @name: set_pasv
     * @desciption: 设置传输模式[PASV]
     * @scope: private
     * @return: $this
     * @create: 2014-10-13
    **/
    private function set_pasv(){
        if(!$this -> set_data('PASV '."\r\n", $this -> socket)) return FALSE;
        $array = $this -> get_data($this -> socket);
        if(!$this -> iscode(227, $array)) return FALSE;
        if(preg_match("/\(([\d,]+)\)/", $array['string'], $match)){
            $tmp = explode(',', $match[1]);
            $this -> data_port = intval($tmp[4])*256+intval($tmp[5]);
            unset($tmp[4], $tmp[5]);
            $this -> data_ip = implode('.', $tmp);
        }
        $this -> data_socket = fsockopen($this -> data_ip, $this -> data_port);
        if($this -> data_socket) return $this;
        return FALSE;
    }

    /**
     * @name: get_data
     * @desciption: 读取socket数据信息
     * @scope: private
     * @param: resource socket资源句柄
     * @return: array
     * @create: 2014-10-13
    **/
    private function get_data($socket){
        if(!$this -> is_connect()) return FALSE;
        if($socket == NULL) return FALSE;
        $string = '';
        $code = '';
        $max = 100000000;
        while(TRUE){
            if(--$max < 1) break;
            if(feof($socket)) break;
            $bufs = fgets($socket, 4096);
            if($bufs === FALSE) break;
            $string .= $bufs;
            $bufs_leng = strlen($bufs);
            if($bufs_leng == 3){
                $code = intval($bufs);
                if(is_numeric($bufs)) break;
            }else if($bufs_leng > 3){
                $code = intval(substr($bufs, 0, 3));
                if(substr($bufs, 3, 1) == ' ') break;
            }
        }
        return array('code' => $code, 'string' => $this -> icon_var($string, 'GBK,UTF-8,I'));
    }

    /**
     * @name: set_data
     * @desciption: 发送数据到指定的socket
     * @scope: private
     * @param: string 发送的数据命令
     * @param: resource socket资源句柄
     * @return: boolean
     * @create: 2014-10-13
    **/
    private function set_data($string, $socket = NULL){
        if(!$this -> is_connect()) return FALSE;
        $socket == NULL && $socket = $this -> socket;
        return fwrite($socket, $string) ? TRUE : FALSE;
    }

    /**
     * @name: get_data_file
     * @desciption: 读取socket数据文件内容信息不能转码
     * @scope: private
     * @param: resource socket资源句柄
     * @return: array
     * @create: 2014-10-13
    **/
    private function get_data_file($socket = NULL){
        if(!$this -> is_connect()) return FALSE;
        $socket == NULL && $socket = $this -> data_socket;
        $string = '';
        $max = 100000000;
        while(TRUE){
            if(--$max < 1) break;
            if(feof($socket)) break;
            $bufs = fread($socket, 4096);
            if($bufs === FALSE) break;
            $string .= $bufs;
        }
        return $string;
    }

    /**
     * @name: get_pwd
     * @desciption: 获取当前目录
     * @scope: public
     * @return: string
     * @create: 2014-10-13
    **/
    public function get_pwd(){
        if(!$this -> set_data('PWD'."\r\n", $this -> socket)) return FALSE;
        $array = $this -> get_data($this -> socket);
        if(!$this -> iscode(257, $array)) return FALSE;
        if(!preg_match("/\d+ \"(.*)\" /", $array['string'], $match)) return FALSE;
        $this -> pwd = rtrim($match[1], '/').'/';
        return $this -> pwd;
    }

    /**
     * @name: set_type
     * @desciption: 设置传输编码
     * @scope: private
     * @param: string 传输编码[default-A][A-ASCII,I-8bit]
     * @return: $this
     * @create: 2014-10-13
    **/
    private function set_type($type = 'A'){
        $type = strtoupper($type);
        switch($type){
            case 'I' : { $type = 'I'; break; }
            default : { $type = 'A'; }
        }
        if($this -> last_type == $type) return $this;
        $this -> last_type = $type;
        if(!$this -> set_data('TYPE '.$type.' '."\r\n", $this -> socket)) return FALSE;
        $array = $this -> get_data($this -> socket);
        if($this -> iscode(200, $array)) return $this;
        return FALSE;
    }

    /**
     * @name: set_pwd
     * @desciption: 设置[进入]目录
     * @scope: public
     * @param: string 目录名称[/images/]可以是相对或绝对路径
     * @return: $this
     * @create: 2014-10-13
    **/
    public function set_pwd($pwd){
        $pwd = preg_replace("/[\/\\\\]{1,}/", '/', $pwd);
        if($pwd{0} != '/') $pwd = $this -> pwd.'/'.$pwd; //相对
        if($pwd != '/') $pwd = rtrim($pwd, '/');
        $pwd = $this -> icon_var($pwd, 'UTF-8,GBK,I');
        if(!$this -> set_data('CWD '.$pwd."\r\n", $this -> socket)) return FALSE;
        $array = $this -> get_data($this -> socket);
        if(!$this -> iscode(250, $array)) return FALSE;
        $this -> pwd = $pwd;
        return $this;
    }

    /**
     * @name: get_size
     * @desciption: 获取文件大小
     * @scope: public
     * @param: string FTP服务器文件路径
     * @return: integer
     * @create: 2014-10-13
    **/
    public function get_size($web_url){
        if(!$this -> is_login && !$this -> login()) return FALSE;
        $web_url = $this -> get_url_file($web_url, $this -> pwd, FALSE);
        if(!$web_url) return FALSE;
        if(!$this -> set_data('SIZE '.$web_url."\r\n", $this -> socket)) return FALSE;
        $array = $this -> get_data($this -> socket);
        if(!$this -> iscode(213, $array)) return FALSE;
        if(!preg_match("/\d+ (\d+)/", $array['string'], $match)) return FALSE;
        return $match[1];
    }

    /**
     * @name: get_list
     * @desciption: 读取FTP列表信息
     * @scope: public
     * @return: array
     * @create: 2014-10-13
    **/
    public function get_list(){
        if(!$this -> is_login && !$this -> login()) return FALSE;
        if(!$this -> set_type('A')) return FALSE;
        $this -> set_pasv();
        $is_allow = FALSE;
        if(count($this -> allow_cmd) > 0 && in_array('MLSD', $this -> allow_cmd)){
            if(!$this -> set_data("MLSD\r\n", $this -> socket)) return FALSE;
            $is_allow = TRUE;
        }else{
            if(!$this -> set_data("LIST -al\r\n", $this -> socket)) return FALSE;
        }
        $array = $this -> get_data($this -> socket);
        if($this -> iscode(150, $array)){
            $array = $this -> get_data($this -> socket);
            if(!$this -> iscode(226, $array)) return FALSE;
            $array = $this -> get_data($this -> data_socket);
            if(!isset($array['string'])) return FALSE;
        }
        $this -> close_data();
        if(!is_array($array) || !isset($array['string']) || $array['string'] == '') return FALSE;
        $return = $dir = $file = array();
        foreach(explode("\n", $array['string']) as $line){
            $line = trim($line);
            if($line == '') continue;
            $array_line = array();
            $line_leng = strlen($line);
            if($is_allow){
                while($line_leng > 0){
                    $pos = strpos($line, ';');
                    if($pos === FALSE){
                        $array_line['name'] = trim($line);
                        break;
                    }else{
                        $tmp_string = substr($line, 0, $pos);
                        if(strlen($tmp_string) > 0){
                            $tmp_array = explode('=', $tmp_string);
                            if(count($tmp_array) == 2){
                                $array_line[$tmp_array[0]] = trim($tmp_array[1]);
                            }else{
                                $array_line[] = trim($tmp_string);
                            }
                        }
                        $line = substr($line, $pos+1);
                        $line_leng = strlen($line);
                    }
                }
            }else{
                if(preg_match('/^([a-z\-]+)\s+(\d+)\s+(\S+)\s+(\S+)\s+(\d+)\s+(.*)\s+(\S+)$/', $line, $match)){
                    $array_line['type'] = strtolower(substr($match[1], 0, 1)) == 'd' ? 'dir' : 'file';
                    $array_line['size'] = $match[5];
                    $array_line['modify'] = date('YmdHis', strtotime($match[6]));
                    $array_line['UNIX.uid'] = $match[3];
                    $array_line['UNIX.gid'] = $match[4];
                    $array_line['unique'] = $match[2];
                    $array_line['name'] = $match[7];
                    $num = preg_replace(array("'r'", "'w'", "'x'", "'-'"), array(4,2,1,0), strtolower(substr($match[1], 1, 9)));
                    $array_line['UNIX.mode'] = '0'.($num{0}+$num{1}+$num{2}).($num{3}+$num{4}+$num{5}).($num{6}+$num{7}+$num{8});
                }
            }
            if(isset($array_line['name']) && $array_line['name'] != '.' && $array_line['name'] != '..'){
                if(isset($array_line['type']) && strtolower($array_line['type']) == 'dir'){
                    $dir[] = $array_line;
                }else{
                    $file[] = $array_line;
                }
            }
        }
        $return = array_merge($file, $dir);
        return $return;
    }

    /**
     * @name: get_url_file
     * @desciption: 计算路径文件和文件夹
     * @scope: private
     * @param: string 被操作的远程文件路径
     * @param: string 被操作的远程文件路径对应的相对路径
     * @param: boolean 文件和文件夹 default[FALSE-文件,TRUE-文件夹]
     * @return: string
     * @create: 2014-10-13
    **/
    private function get_url_file($url, $url_annx, $isdir = FALSE){
        if($url != ''){
            $url = preg_replace("/[\/\\\\]{1,}/", '/', $url);
            if($isdir){ //folder
                if(substr($url, -1) != '/') return FALSE;   //必须是文件路径
            }else{  //file
                if(substr($url, -1) == '/') return FALSE;   //必须是文件名路径
            }
            if($url{0} != '/') $url = $url_annx.$url; //相对路径
            $url = $this -> icon_var($url, 'UTF-8,GBK,I');
            return $url;
        }
        return FALSE;
    }

    /**
     * @name: retr
     * @desciption: 下载文件数据
     * @scope: public
     * @param: string 被下载的远程文件路径
     * @param: string 存储下载文件的路径
     * @param: boolean 文件存在是否替换 default[FALSE]
     * @param: integer 设置传送数据偏移量 default[0]
     * @return: string
     * @create: 2014-10-13
    **/
    public function retr($web_url, $local_url, $is_replace=FALSE, $rest = 0){
        if(!$this -> is_login && !$this -> login()) return FALSE;
        $local_url = $this -> get_url_file($local_url, $this -> local_data, FALSE);
        if(!$local_url) return FALSE;
        if(!$is_replace && file_exists($local_url)) return FALSE;   //保存本地文件存在且不替换
        $web_url = $this -> get_url_file($web_url, $this -> pwd, FALSE);
        if(!$web_url) return FALSE;
        $rest = intval($rest);
        $rest = $rest < 1 ? 0 : $rest;
        if(!$this -> set_type('I')) return FALSE;
        if(!$this -> set_data('REST '.$rest."\r\n", $this -> socket)) return FALSE;
        $array = $this -> get_data($this -> socket);
        if($this -> iscode(350, $array)){
            $this -> set_pasv();
            $web_url = icon_var($web_url, 'UTF-8,GBK,I');
            if(!$this -> set_data('RETR '.$web_url."\r\n", $this -> socket)) return FALSE;
            $array = $this -> get_data($this -> socket);
            if($this -> iscode(150, $array)){
                $string = $this -> get_data_file($this -> data_socket);
                $this -> close_data();
                $array = $this -> get_data($this -> socket);
                if(!$this -> iscode(226, $array)) return FALSE;
                if(strlen($string) < 1) return FALSE;
                return file_put_contents($local_url, $string) ? TRUE : FALSE;
            }
        }
        return FALSE;
    }

    /**
     * @name: mkd
     * @desciption: 创建目录
     * @scope: public
     * @param: string 创建的指定目录
     * @return: boolean
     * @create: 2014-10-13
    **/
    public function mkd($web_url){
        if(!$this -> is_login && !$this -> login()) return FALSE;
        $web_url = $this -> get_url_file($web_url, $this -> pwd, TRUE);
        if(!$web_url) return FALSE;
        $web_url_cur = '';
        foreach(explode('/', rtrim($web_url, '/')) as $key => $val){
            $web_url_cur .= $val.'/';
            if($key > 0){
                if(!$this -> set_data('MKD '.$web_url_cur."\r\n", $this -> socket)) return FALSE;
                $array = $this -> get_data($this -> socket);
                if(!$this -> iscode('257,550', $array)) return FALSE;
            }
        }
        return TRUE;
    }

    /**
     * @name: dele_file
     * @desciption: 删除文件
     * @scope: public
     * @param: string 被删除的文件路径
     * @return: boolean
     * @create: 2014-10-13
    **/
    public function dele_file($web_url){
        if(!$this -> is_login && !$this -> login()) return FALSE;
        $web_url = $this -> get_url_file($web_url, $this -> pwd, FALSE);
        if(!$web_url) return FALSE;
        if(!$this -> set_data('DELE '.$web_url."\r\n", $this -> socket)) return FALSE;
        $array = $this -> get_data($this -> socket);
        if(!$this -> iscode(250, $array)) return FALSE;
        return TRUE;
    }

    /**
     * @name: dele_dir
     * @desciption: 删除目录文件夹
     * @scope: public
     * @param: string 被删除的文件夹
     * @return: boolean
     * @create: 2014-10-13
    **/
    public function dele_dir($web_url){
        if(!$this -> is_login && !$this -> login()) return FALSE;
        $web_url = $this -> get_url_file($web_url, $this -> pwd, TRUE);
        if(!$web_url) return FALSE;
        if(!$this -> set_pwd($this -> icon_var($web_url, 'GBK,UTF-8,I'))) return FALSE;
        $get_list = $this -> get_list();
        if(is_array($get_list) && count($get_list) > 0){
            $pwd = $this -> get_pwd();
            foreach($get_list as $val){
                if(strtolower($val['type']) == 'dir'){
                    $dele_dir = $this -> dele_dir($pwd.$val['name'].'/');
                    if(!$dele_dir) return FALSE;
                }else{
                    $dele_file = $this -> dele_file($pwd.$val['name']);
                    if(!$dele_file) return FALSE;
                }
            }
        }
        if($web_url != '/' && !$this -> set_data('RMD '.$web_url."\r\n", $this -> socket)) return FALSE;
        $array = $this -> get_data($this -> socket);
        $this -> set_pwd('/');
        if(!$this -> iscode(250, $array)) return FALSE;
        return TRUE;
    }

    /**
     * @name: stor
     * @desciption: 上传文件
     * @scope: public
     * @param: string 本地文件
     * @param: string 远程文件
     * @return: boolean
     * @create: 2014-10-13
    **/
    public function stor($local_url, $web_url){
        if(!$this -> is_login && !$this -> login()) return FALSE;
        $local_url = $this -> get_url_file($local_url, $this -> local_data, FALSE);
        if(!$local_url) return FALSE;
        if(!file_exists($local_url)) return FALSE;   //保存本地文件存在且不替换
        $web_url = $this -> get_url_file($web_url, $this -> pwd, FALSE);
        if(!$web_url) return FALSE;
        if(!($fopen = fopen($local_url, 'rb'))) return FALSE; //文件数据不可读取
        $mkd = $this -> mkd(substr($web_url, 0, strrpos($web_url, '/')+1)); //自动创建目录
        if(!$mkd) return FALSE;
        if(!$this -> set_type('I')) return FALSE;
        $this -> set_pasv();
        $web_url = icon_var($web_url, 'UTF-8,GBK,I');
        if(!$this -> set_data('STOR '.$web_url."\r\n", $this -> socket)) return FALSE;
        $array = $this -> get_data($this -> socket);
        if($this -> iscode(150, $array)){
            $max = 100000000;
            while(TRUE){
                if(--$max < 1) break;
                if(feof($fopen)) break;
                $bufs = fread($fopen, 8192);
                if($bufs === FALSE) break;
                if(!$this -> set_data($bufs, $this -> data_socket)) return FALSE;
            }
            if($fopen) fclose($fopen);
            $this -> close_data();
            $array = $this -> get_data($this -> socket);
            if($this -> iscode(226, $array)) return TRUE;
        }
        return FALSE;
    }
}
?>