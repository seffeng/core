<?php
/**
 * @copyright 2014
 * @description: cls_ftp
 * @file: cls_ftp.php
 * @charset: UTF-8
 * @create: 2014-10-13
 * @version 1.0
**/

class cls_ftp extends cls_mod{
    private $object_mark            = NULL;                 //object.mark.name
    protected $conflist             = array('FTP_HOST','FTP_USER','FTP_PASS','FTP_PORT');

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
        $this -> get_ftps() -> set($this -> get_conf('FTP_HOST'), $this -> get_conf('FTP_USER'), $this -> get_conf('FTP_PASS'), $this -> get_conf('FTP_PORT'));
        return $set_conf;
    }

    /**
     * @name: get_ftps
     * @description: 获取对象ftps
     * @scope: public
     * @param: string 对象标签 default[NULL]
     * @param: boolean 是否新new对象 default[FALSE]
     * @return: object
     * @create: 2014-10-13
    **/
    public function get_ftps($mark_name=NULL, $is_new=FALSE){
        $mark_name = ($mark_name == '') ? $this -> object_mark : $mark_name;
        return get_init('ftps', $mark_name, $is_new);
    }

    /**
     * @name: upload
     * @description: 上传文件至FTP服务器
     * @scope: public
     * @param: string 本地文件
     * @param: string 远程文件
     * @return: boolean
     * @create: 2014-10-13
    **/
    public function upload($local, $remote){
        return $this -> get_ftps() -> stor($local, $remote);
    }

    /**
     * @name: download
     * @description: 下载文件至本地服务器
     * @scope: public
     * @param: string 远程文件
     * @param: string 本地文件
     * @param: boolean 是否替换 default[TRUE]
     * @param: integer 下载文件内容偏移量 default[0]
     * @return: boolean
     * @create: 2014-10-13
    **/
    public function download($remote, $local, $is_replace=TRUE, $rest = 0){
        return $this -> get_ftps() -> retr($remote, $local, $is_replace, $rest);
    }

    /**
     * @name: del_file
     * @description: 删除文件从FTP服务器
     * @scope: public
     * @param: string 远程文件
     * @return: boolean
     * @create: 2014-10-13
    **/
    public function del_file($remote){
        return $this -> get_ftps() -> dele_file($remote);
    }

    /**
     * @name: get_size
     * @description: 文件是否存在从FTP服务器
     * @scope: public
     * @param: string 远程文件
     * @return: boolean
     * @create: 2014-10-13
    **/
    public function get_size($remote){
        return $this -> get_ftps() -> get_size($remote);
    }

    /**
     * @name: get_list
     * @description: 列出当前的文件和文件夹
     * @scope: public
     * @return: array
     * @create: 2014-10-13
    **/
    public function get_list(){
        return $this -> get_ftps() -> get_list();
    }

    /**
     * @name: set_pwd
     * @desciption: 设置[进入]目录
     * @scope: public
     * @param: string 目录名称[/images/]可以是相对或绝对路径
     * @return: object[$this]
     * @create: 2014-10-13
    **/
    public function set_pwd($pwd){
        return $this -> get_ftps() -> set_pwd($pwd);
    }

    /**
     * @name: get_pwd
     * @desciption: 获取当前目录
     * @scope: public
     * @return: string
     * @create: 2014-10-13
    **/
    public function get_pwd(){
        return $this -> get_ftps() -> get_pwd();
    }
}
?>