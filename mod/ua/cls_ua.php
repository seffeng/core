<?php
/**
 * @copyright 2014
 * @description: cls_ua
 * @file: cls_ua.php
 * @charset: UTF-8
 * @create: 2014-10-13
 * @version 1.0
**/

class cls_ua extends cls_mod{
    private $object_mark            = NULL;                 //object.mark.name
    protected $conflist             = array('UA_UPDATE_TIME','UA_IS_DOWNLOAD','UA_CACHE_DIR');

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
        if($this -> get_conf('UA_UPDATE_TIME') > 0) $this -> get_uasparser() -> updateInterval = $this -> get_conf('UA_UPDATE_TIME');
        if($this -> get_conf('UA_CACHE_DIR') != '') $this -> get_uasparser()-> set_cache_dir($this -> get_conf('UA_CACHE_DIR'));
        $this -> get_uasparser() -> set_dodownloads($this -> get_conf('UA_IS_DOWNLOAD') ? TRUE : FALSE);
        return $set_conf;
    }
    
    /**
     * @name: get_uasparser
     * @description: 获取对象uasparser
     * @scope: public
     * @param: string 对象标签 default[NULL]
     * @param: boolean 是否新new对象 default[FALSE]
     * @return: object
     * @create: 2014-10-13
    **/
    public function get_uasparser($mark_name=NULL, $is_new=FALSE){
        $mark_name = ($mark_name == '') ? $this -> object_mark : $mark_name;
        return get_init('uasparser', $mark_name, $is_new);
    }
    
    /**
     * @name: parser_string
     * @description: 返回解析useragent数据
     * @scope: public
     * @param: string 被解析的useragent数据 default[NULL]
     * @return: array
     * @create: 2014-10-13
    **/
    public function parser_string($ua_string=NULL){
        $return = $this -> get_uasparser() -> parse($ua_string);
        if($return['os_family'] == 'unknown'){   //分析
            $return['ua_string'] = $ua_string;
            $os_family = $os_name = '';
            //IOS
            if($os_family=='' && preg_match('/iPh\ OS\ 6_/i', $ua_string)){
                $os_family = 'iOS'; $os_name = 'iOS 6';
            }
            if($os_family=='' && preg_match('/iPh\ OS\ 7_/i', $ua_string)){
                $os_family = 'iOS'; $os_name = 'iOS 6';
            }
            if($os_family=='' && preg_match('/CaptiveNetworkSupport/i', $ua_string)){
                $os_family = 'iOS'; $os_name = 'iOS x';
            }
            if($os_family=='' && preg_match('/ios/i', $ua_string)){
                $os_family = 'iOS'; $os_name = 'iOS x';
            }
            if($os_family=='' && preg_match('/Skype\ WISPr/i', $ua_string)){
                $os_family = 'iOS'; $os_name = 'iOS x';
            }
            //Android
            if($os_family=='' && preg_match('/Apache-HttpClient/i', $ua_string)){
                $os_family = 'Android'; $os_name = 'Android x';
            }
            if($os_family=='' && preg_match('/^DOOV/i', $ua_string)){    //朵唯
                $os_family = 'Android'; $os_name = 'Android x';
            }
            if($os_family=='' && preg_match('/^SCH-/i', $ua_string)){   //三星
                $os_family = 'Android'; $os_name = 'Android x';
            }
            if($os_family=='' && preg_match('/^N9000/i', $ua_string)){   //三星
                $os_family = 'Android'; $os_name = 'Android x';
            }
            if($os_family=='' && preg_match('/^GT-/i', $ua_string)){   //三星
                $os_family = 'Android'; $os_name = 'Android x';
            }
            if($os_family=='' && preg_match('/GT-[a-z]\d+/i', $ua_string)){   //三星
                $os_family = 'Android'; $os_name = 'Android x';
            }
            if($os_family=='' && preg_match('/^SM-/i', $ua_string)){   //三星
                $os_family = 'Android'; $os_name = 'Android x';
            }
            if($os_family=='' && preg_match('/^MI[-\ ]{1}\S+/i', $ua_string)){   //MIUI
                $os_family = 'Android'; $os_name = 'Android x';
            }
            if($os_family=='' && preg_match('/^HTC\ /i', $ua_string)){   //HTC 
                $os_family = 'Android'; $os_name = 'Android x';
            }
            if($os_family=='' && preg_match('/X[\d]{3}\ /i', $ua_string)){   //OPPO
                $os_family = 'Android'; $os_name = 'Android x';
            }
            if($os_family=='' && preg_match('/R[\d]{3}T\ /i', $ua_string)){   //OPPO
                $os_family = 'Android'; $os_name = 'Android x';
            }
            if($os_family=='' && preg_match('/U[\d]{3}T\ /i', $ua_string)){   //OPPO
                $os_family = 'Android'; $os_name = 'Android x';
            }
            if($os_family=='' && preg_match('/ZTE\ /i', $ua_string)){   //ZTE
                $os_family = 'Android'; $os_name = 'Android x';
            }
            if($os_family=='' && preg_match('/HUAWEI/i', $ua_string)){   //华为
                $os_family = 'Android'; $os_name = 'Android x';
            }
            if($os_family=='' && preg_match('/Y[\d]{3}-U[\d]{2};/i', $ua_string)){   //华为
                $os_family = 'Android'; $os_name = 'Android x';
            }
            if($os_family=='' && preg_match('/360WebApp/i', $ua_string)){   //360WebApp
                $os_family = 'Android'; $os_name = 'Android x';
            }
            if($os_family=='' && preg_match('/T580/i', $ua_string)){   //天语
                $os_family = 'Android'; $os_name = 'Android x';
            }
            if($os_family=='' && preg_match('/iMO920/i', $ua_string)){   //欧博信
                $os_family = 'Android'; $os_name = 'Android x';
            }
            if($os_family=='' && preg_match('/ME525+/i', $ua_string)){   //摩托罗拉
                $os_family = 'Android'; $os_name = 'Android x';
            }
            if($os_family=='' && preg_match('/eMOTO/i', $ua_string)){   //佳信达
                $os_family = 'Android'; $os_name = 'Android x';
            }
            if($os_family=='' && preg_match('/L35h;/i', $ua_string)){   //索尼
                $os_family = 'Android'; $os_name = 'Android x';
            }
            if($os_family=='' && preg_match('/S39h;/i', $ua_string)){   //索尼
                $os_family = 'Android'; $os_name = 'Android x';
            }
            if($os_family=='' && preg_match('/^OPSSON/i', $ua_string)){   //欧博信
                $os_family = 'Android'; $os_name = 'Android x';
            }
            if($os_family=='' && preg_match('/^HS-[a-z]{2}[\d]{3}/i', $ua_string)){   //海信
                $os_family = 'Android'; $os_name = 'Android x';
            }
            if($os_family=='' && preg_match('/Lenovo A[\d]{3}[a-z]?;/i', $ua_string)){   //联想
                $os_family = 'Android'; $os_name = 'Android x';
            }
            if($os_family=='' && preg_match('/^Coolpad/i', $ua_string)){   //欧博信
                $os_family = 'Android'; $os_name = 'Android x';
            }
            if($os_family=='' && preg_match('/^[\d]{4}[a-z]?;/i', $ua_string)){   //酷派
                $os_family = 'Android'; $os_name = 'Android x';
            }
            if($os_family=='' && preg_match('/^vivo/i', $ua_string)){   //步步高
                $os_family = 'Android'; $os_name = 'Android x';
            }
            if($os_family=='' && preg_match('/M040;/i', $ua_string)){   //魅族
                $os_family = 'Android'; $os_name = 'Android x';
            }
            if($os_family=='' && preg_match('/U1203;/i', $ua_string)){   //优思小C
                $os_family = 'Android'; $os_name = 'Android x';
            }
            if($os_family=='' && preg_match('/^epade/i', $ua_string)){   //易派
                $os_family = 'Android'; $os_name = 'Android x';
            }
            if($os_family=='' && preg_match('/GN[\d]{3}[T]?;/i', $ua_string)){   //金立
                $os_family = 'Android'; $os_name = 'Android x';
            }
            if($os_family=='' && preg_match('/T[\d]{3}[A]?;/i', $ua_string)){   //天时达
                $os_family = 'Android'; $os_name = 'Android x';
            }
            if($os_family=='' && preg_match('/Agoo-sdk/i', $ua_string)){
                $os_family = 'Android'; $os_name = 'Android x';
            }
            if($os_family=='' && preg_match('/AnZhi/i', $ua_string)){
                $os_family = 'Android'; $os_name = 'Android x';
            }
            if($os_family=='' && preg_match('/yixin_httputils/i', $ua_string)){
                $os_family = 'Android'; $os_name = 'Android x';
            }
            if($os_family=='' && preg_match('/App-Market/i', $ua_string)){
                $os_family = 'Android'; $os_name = 'Android x';
            }
            if($os_family=='' && preg_match('/LT26ii;/i', $ua_string)){
                $os_family = 'Android'; $os_name = 'Android x';
            }
            if($os_family=='' && preg_match('/Jakarta/i', $ua_string)){
                $os_family = 'Android'; $os_name = 'Android x';
            }
            if($os_family=='' && preg_match('/QQPimSecure/i', $ua_string)){ //QQ安全助手
                $os_family = 'Android'; $os_name = 'Android x';
            }
            if($os_family=='' && preg_match('/kingreader/i', $ua_string)){  //开卷有益
                $os_family = 'Android'; $os_name = 'Android x';
            }
            if($os_family=='' && preg_match('/; [2345]{1}\.[\d]+; /i', $ua_string)){  //; 4.3; or ; 4.1.1; or ; 2.2.2;
                $os_family = 'Android'; $os_name = 'Android x';
            }
            if($os_family=='' && preg_match('/; [2345]{1}\.[\d]+\.[\d]+; /i', $ua_string)){  //; 4.3; or ; 4.1.1; or ; 2.2.2;
                $os_family = 'Android'; $os_name = 'Android x';
            }
            //Other
            if($os_family=='' && preg_match('/NokiaN9/i', $ua_string)){ //米狗
                $os_family = 'MeeGo'; $os_name = 'MeeGo x';
            }
            if($os_family=='' && preg_match('/NokiaC6/i', $ua_string)){
                $os_family = 'Symbian OS'; $os_name = 'Symbian OS x';
            }
            if($os_family=='' && preg_match('/3gpp-gba/i', $ua_string)){    //NokiaN8
                $os_family = 'Symbian OS'; $os_name = 'Symbian OS x';
            }
            if($os_family=='' && preg_match('/Microsoft-ATL-Native/i', $ua_string)){
                $os_family = 'Windows'; $os_name = 'Windows Phone x';
            }
            if($os_family=='' && preg_match('/Nokia/i', $ua_string)){
                $os_family = 'Windows'; $os_name = 'Windows Phone x';
            }
            //不是绝对确定
            if($os_family=='' && preg_match('/Chilkat/i', $ua_string)){ //http://www.chilkatsoft.com/ChilkatHttpUA.asp
                $os_family = 'Windows'; $os_name = 'Windows x';
            }
            if($os_family=='' && preg_match('/^TMA\//i', $ua_string)){
                $os_family = 'Windows'; $os_name = 'Windows x';
            }
            if($os_family=='' && preg_match('/^Internet\//i', $ua_string)){
                $os_family = 'Windows'; $os_name = 'Windows x';
            }
            if($os_family=='' && preg_match('/Miser\//i', $ua_string)){
                $os_family = 'Windows'; $os_name = 'Windows x';
            }
            if($os_family=='' && preg_match('/KUGOU20\//i', $ua_string)){
                $os_family = 'Windows'; $os_name = 'Windows x';
            }
            if($os_family=='' && preg_match('/HttpComponents\//i', $ua_string)){
                $os_family = 'Windows'; $os_name = 'Windows x';
            }
            if($os_family=='' && preg_match('/BBK i710/i', $ua_string)){
                $os_family = 'Windows'; $os_name = 'Windows x';
            }
            if($os_family == ''){ $os_family = 'Windows'; $os_name = 'Windows x'; }else{ unset($return['ua_string']); }
            $return['os_family'] = $os_family;
            $return['os_name'] = $os_name;
        }
        return $return;
    }
    
    /**
     * @name: is_android
     * @description: 是否是android系统
     * @scope: public
     * @param: string 被解析的useragent数据
     * @return: boolean
     * @create: 2014-10-13
    **/
    public function is_android($ua_string){
        $ua_parser = !is_array($ua_string) ? $this -> parser_string($ua_string) : $ua_string;
        $return = FALSE;
        if(isset($ua_parser['os_family']) && preg_match('/android/i', $ua_parser['os_family'])) $return = TRUE;
        return $return;
    }
    
    /**
     * @name: call_uagent_isios
     * @description: 是否是ios系统
     * @scope: public
     * @param: string 被解析的useragent数据
     * @return: boolean
     * @create: 2014-10-13
    **/
    public function is_ios($ua_string){
        $ua_parser = !is_array($ua_string) ? $this -> parser_string($ua_string) : $ua_string;
        $return = FALSE;
        if(isset($ua_parser['os_family']) && preg_match('/ios/i', $ua_parser['os_family'])) $return = TRUE;
        return $return;
    }
    
    /**
     * @name: is_winphone
     * @description: 是否是winphone系统
     * @scope: public
     * @param: string 被解析的useragent数据
     * @return: boolean
     * @create: 2014-10-13
    **/
    public function is_winphone($ua_string){
        $ua_parser = !is_array($ua_string) ? $this -> parser_string($ua_string) : $ua_string;
        $return = FALSE;
        if(isset($ua_parser['os_name']) && preg_match('/windows phone/i', $ua_parser['os_name'])) $return = TRUE;
        if(isset($ua_parser['os_name']) && preg_match('/windows mobile/i', $ua_parser['os_name'])) $return = TRUE;
        if(isset($ua_parser['os_name']) && preg_match('/windows/i', $ua_parser['os_name']) && isset($ua_parser['type']) && preg_match('/mobile browser/i', $ua_parser['type'])) $return = TRUE;
        return $return;
    }
}
?>