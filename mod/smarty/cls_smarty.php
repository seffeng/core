<?php
/**
 * @copyright 2014
 * @description: cls_smarty
 * @file: cls_smarty.php
 * @charset: UTF-8
 * @create: 2014-10-13
 * @version 1.0
**/

class cls_smarty extends cls_mod{
    private $object_mark            = NULL;                 //object.mark.name
    protected $conflist             = array('SMARTY_COMPILE_CHECK','SMARTY_DEBUG','SMARTY_CACHE','SMARTY_USE_SUB_DIRS',
                                    'SMARTY_CACHE_LIFE_TIME','SMARTY_DATA_CONFIG','SMARTY_DATA_CACHE','SMARTY_TPL',
                                    'SMARTY_DATA_TPL_C','SMARTY_LEFT_DELI','SMARTY_RIGHT_DELI');

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
        $this -> get_smarty() -> compile_check      = $this -> get_conf('SMARTY_COMPILE_CHECK');
        $this -> get_smarty() -> debugging          = $this -> get_conf('SMARTY_DEBUG');
        $this -> get_smarty() -> caching            = $this -> get_conf('SMARTY_CACHE');
        $this -> get_smarty() -> use_sub_dirs       = $this -> get_conf('SMARTY_USE_SUB_DIRS');
        $this -> get_smarty() -> cache_lifetime     = $this -> get_conf('SMARTY_CACHE_LIFE_TIME');
        $this -> get_smarty() -> config_dir         = $this -> get_conf('SMARTY_DATA_CONFIG');
        $this -> get_smarty() -> cache_dir          = $this -> get_conf('SMARTY_DATA_CACHE');
        $this -> get_smarty() -> template_dir       = $this -> get_conf('SMARTY_TPL');
        $this -> get_smarty() -> compile_dir        = $this -> get_conf('SMARTY_DATA_TPL_C');
        $this -> get_smarty() -> left_delimiter     = $this -> get_conf('SMARTY_LEFT_DELI');
        $this -> get_smarty() -> right_delimiter    = $this -> get_conf('SMARTY_RIGHT_DELI');
        return $set_conf;
    }

    /**
     * @name: get_smarty
     * @description: 获取对象smarty
     * @scope: public
     * @param: string 对象标签 default[NULL]
     * @param: boolean 是否新new对象 default[FALSE]
     * @return: object
     * @create: 2014-10-13
    **/
    public function get_smarty($mark_name=NULL, $is_new=FALSE){
        $mark_name = ($mark_name == '') ? $this -> object_mark : $mark_name;
        return get_init('smarty', $mark_name, $is_new, TRUE);
    }

    /**
     * @name: show
     * @description: 显示网页
     * @scope: public
     * @param: string 网页模版的路径
     * @return: void
     * @create: 2014-10-13
    **/
    public function show($html_file, $is_exit=TRUE){
        $this -> get_smarty() -> display($html_file);
        $is_exit && exit;
    }

    /**
     * @name: assign
     * @description: 设置变量
     * @scope: public
     * @param: mixed 被设置的变量
     * @param: mixed 变量值
     * @return: void
     * @create: 2014-10-13
    **/
    public function assign($var_name, $var_value=NULL){
        $this -> get_smarty() -> assign($var_name, $var_value);
    }

    /**
     * @name: fetch
     * @description: 获取内容
     * @scope: public
     * @param: string 网页模版的路径
     * @return: string
     * @create: 2014-10-13
    **/
    public function fetch($html_file){
        return $this -> get_smarty() -> fetch($html_file);
    }
}
?>