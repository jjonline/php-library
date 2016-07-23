<?php
/**
 * 实用性较强的函数封装
 * @authors Jea杨 (JJonline@JJonline.Cn)
 * @date    2015-08-06 15:09:03
 * @version 1.0
 */
namespace {

    /**
     * 获取输入参数 支持过滤和默认值 author:Tp
     * 使用方法:
     * <code>
     * Input('id',0); 获取id参数 自动判断get或者post
     * Input('post.name','','htmlspecialchars'); 获取$_POST['name']
     * Input('get.'); 获取$_GET
     * </code>
     * @param string $name 变量的名称 支持指定类型
     * @param mixed $default 不存在的时候默认值
     * @param mixed $filter 参数过滤方法
     * @param mixed $datas 要获取的额外数据源::可用用于过滤外部数据
     * @return mixed
     */
    function Input($name,$default='',$filter=null,$datas=null)
    {
        static $_PUT            =   null;
        $type                   =   's';#默认转换获取到变量为string类型
        if(strpos($name,'/')){ // 指定返回类型修饰符
            list($name,$type)   =   explode('/',$name,2);
        }
        if(strpos($name,'.')) { // 指定参数来源
            list($method,$name) =   explode('.',$name,2);
        }else{ // 默认为自动判断
            $method             =   'param';
        }
        switch(strtolower($method)) {
            case 'get'     :   
                $input          =&  $_GET;#显式指定方式 引用方式调用
                break;
            case 'post'    :   
                $input          =&  $_POST;
                break;
            case 'put'     :   
                if(is_null($_PUT)){
                    parse_str(file_get_contents('php://input'), $_PUT);
                }
                $input          =   $_PUT;
                break;
            case 'param'   :
                switch($_SERVER['REQUEST_METHOD']) {
                    case 'POST':
                        $input  =   $_POST;
                        break;
                    case 'PUT':
                        if(is_null($_PUT)){
                            parse_str(file_get_contents('php://input'), $_PUT);
                        }
                        $input  =   $_PUT;
                        break;
                    default:
                        $input  =   $_GET;
                }
                break;
            case 'request' :
                $input          =&  $_REQUEST;
                break;
            case 'session' :
                $input          =&  $_SESSION;
                break;
            case 'cookie'  :
                $input          =&  $_COOKIE;
                break;
            case 'server'  :
                $input          =&  $_SERVER;
                break;
            case 'globals' :
                $input          =&  $GLOBALS;
                break;
            case 'data'    :
                $input          =&  $datas;
                break;
            default:
                return null;
        }
        if(''==$name) { // 获取全部变量
            $data               =   $input;
            $filters            =   isset($filter)?$filter:'';
            if($filters) {
                if(is_string($filters)){
                    $filters    =   explode(',',$filters);
                }
                foreach($filters as $filter){
                    $data       =   array_map_recursive($filter,$data); // 参数过滤
                }
            }
        }elseif(isset($input[$name])) { // 取值操作
            $data               =   $input[$name];
            $filters            =   isset($filter)?$filter:'';
            if($filters) {
                if(is_string($filters)){
                    if(0 === strpos($filters,'/')){
                        if(1 !== preg_match($filters,(string)$data)){
                            // 支持正则验证
                            return   isset($default) ? $default : null;
                        }
                    }else{
                        $filters=   explode(',',$filters);
                    }
                }elseif(is_int($filters)){
                    $filters    =   array($filters);
                }
                
                if(is_array($filters)){
                    foreach($filters as $filter){
                        if(function_exists($filter)) {
                            $data   =   is_array($data) ? array_map_recursive($filter,$data) : $filter($data); // 参数过滤
                        }else{
                            $data   =   filter_var($data,is_int($filter) ? $filter : filter_id($filter));
                            if(false === $data) {
                                return   isset($default) ? $default : null;
                            }
                        }
                    }
                }
            }
            if(!empty($type)){
                switch(strtolower($type)){
                    case 'a':   // 数组
                        $data   =   (array)$data;
                        break;
                    case 'd':   // 数字
                        $data   =   (int)$data;
                        break;
                    case 'f':   // 浮点
                        $data   =   (float)$data;
                        break;
                    case 'b':   // 布尔
                        $data   =   (boolean)$data;
                        break;
                    case 's':   // 字符串
                    default:
                        $data   =   (string)$data;
                }
            }
        }else{ // 变量默认值
            $data               =   isset($default)?$default:null;
        }
        #最后Input方法体统一递归必须清理的过滤方法Input_filter 请按业务需求完善Input_filter方法
        is_array($data) && array_walk_recursive($data,'Input_filter');
        return $data;
    }
    #Input函数专用递归调用函数处理方法
    function array_map_recursive($filter, $data)
    {
        $result = array();
        foreach ($data as $key => $val) {
            $result[$key] = is_array($val)
             ? array_map_recursive($filter, $val)
             : call_user_func($filter, $val);
        }
        return $result;
    }
    #Input函数专用过滤数据方法
    function Input_filter(&$value)
    {
        // TODO 其他安全过滤 请按业务逻辑实现

        // 过滤查询特殊字符
        if(preg_match('/^(EXP|NEQ|GT|EGT|LT|ELT|OR|XOR|LIKE|NOTLIKE|NOT BETWEEN|NOTBETWEEN|BETWEEN|NOTIN|NOT IN|IN)$/i',$value)){
            $value .= ' ';
        }
    }

    /**
     * session管理函数；统一操纵session author:Tp
     * @param string|array $name session名称 如果为数组则表示进行session设置
     * @param mixed $value session值
     * @return mixed
     */
    function session($name='',$value='')
    {
        global $__session_prefix;#session统一前缀全局变量
        $prefix          =  null;#默认无session前缀 除非通过调用session进行设置过
        if(isset($__session_prefix)) {
            $prefix      =  $__session_prefix;
        }
        if(is_array($name)) { // session初始化设置 在session_start 之前调用
            #设置session前缀 TODO仅当前脚本范围内有效
            if(isset($name['prefix'])) {
                $__session_prefix = $name['prefix'];
            }
            if(isset($name['id'])) {
                session_id($name['id']);
            }
            if(isset($name['name'])) {
                session_name($name['name']);
            }
            if(isset($name['path'])) {
                session_save_path($name['path']);
            }
            if(isset($name['domain'])) {
                ini_set('session.cookie_domain', $name['domain']);
            }
            if(isset($name['expire'])) {
                ini_set('session.gc_maxlifetime',   $name['expire']);
                ini_set('session.cookie_lifetime',  $name['expire']);
            }
            if(isset($name['use_trans_sid'])) {
                ini_set('session.use_trans_sid', $name['use_trans_sid']?1:0);
            }
            if(isset($name['use_cookies'])) {
                ini_set('session.use_cookies', $name['use_cookies']?1:0);
            }
            if(isset($name['cache_limiter'])) {
                session_cache_limiter($name['cache_limiter']);
            }
            if(isset($name['cache_expire'])) {
                session_cache_expire($name['cache_expire']);
            }
            // 指定了auto_start或start的session设置则启动session
            if(isset($name['auto_start']) || isset($name['start'])) {
                session_start();
            }
        }elseif('' === $value){ 
            if(''===$name){
                // 获取全部的session
                return $prefix ? $_SESSION[$prefix] : $_SESSION;
            }elseif(0===strpos($name,'[')) { // session 操作
                if('[pause]'==$name){ // 暂停session
                    session_write_close();
                }elseif('[start]'==$name){ // 启动session
                    session_start();
                }elseif('[destroy]'==$name){ // 销毁session
                    $_SESSION =  array();
                    session_unset();
                    session_destroy();
                }elseif('[regenerate]'==$name){ // 重新生成id
                    session_regenerate_id();
                }
            }elseif(0===strpos($name,'?')){ // 检查session
                $name   =  substr($name,1);
                if(strpos($name,'.')){ // 支持数组
                    list($name1,$name2) =   explode('.',$name);
                    return $prefix?isset($_SESSION[$prefix][$name1][$name2]):isset($_SESSION[$name1][$name2]);
                }else{
                    return $prefix?isset($_SESSION[$prefix][$name]):isset($_SESSION[$name]);
                }
            }elseif(is_null($name)){ // 清空session
                if($prefix) {
                    unset($_SESSION[$prefix]);
                }else{
                    $_SESSION           =   array();
                }
            }elseif($prefix){ // 获取session
                if(strpos($name,'.')){
                    list($name1,$name2) =   explode('.',$name);
                    return isset($_SESSION[$prefix][$name1][$name2])?$_SESSION[$prefix][$name1][$name2]:null;
                }else{
                    return isset($_SESSION[$prefix][$name])?$_SESSION[$prefix][$name]:null;
                }            
            }else{
                if(strpos($name,'.')){
                    list($name1,$name2) =   explode('.',$name);
                    return isset($_SESSION[$name1][$name2])?$_SESSION[$name1][$name2]:null;
                }else{
                    return isset($_SESSION[$name])?$_SESSION[$name]:null;
                }            
            }
        }elseif(is_null($value)){ // 删除session
            if(strpos($name,'.')){
                list($name1,$name2) =   explode('.',$name);
                if($prefix){
                    unset($_SESSION[$prefix][$name1][$name2]);
                }else{
                    unset($_SESSION[$name1][$name2]);
                }
            }else{
                if($prefix){
                    unset($_SESSION[$prefix][$name]);
                }else{
                    unset($_SESSION[$name]);
                }
            }
        }else{ // 设置session
            if(strpos($name,'.')){
                list($name1,$name2)                         =   explode('.',$name);
                if($prefix){
                    $_SESSION[$prefix][$name1][$name2]      =   $value;
                }else{
                    $_SESSION[$name1][$name2]               =   $value;
                }
            }else{
                if($prefix){
                    $_SESSION[$prefix][$name]               =   $value;
                }else{
                    $_SESSION[$name]                        =   $value;
                }
            }
        }
        return null;
    }

    /**
     * Cookie 设置、获取、删除  author:Tp
     * @param string $name cookie名称
     * @param mixed $value cookie值
     * @param mixed $option cookie参数
     * @return mixed
     */
    function cookie($name='', $value='', $option=null)
    {
        // 默认设置TODO
        $config = array(
            'prefix'    =>  '', // cookie 名称前缀
            'expire'    =>  0, // cookie 保存时间::相对于当前时间expire(秒)之后过期 固expire无需再加入当前时间的Unix时间戳
            'path'      =>  '/', // cookie 保存路径 默认全域
            'domain'    =>  null, // cookie 有效域名
            'secure'    =>  false, //  cookie 启用安全传输
            'httponly'  =>  false, // httponly设置
        );
        // 参数设置(会覆盖黙认设置)
        if(!is_null($option)) {
            if(is_numeric($option)) { #如果仅传入数字 则理解成设置cookie的有效期
                $option = array('expire' => $option);
            }elseif (is_string($option)) { #prefix=xxx&expire=xx格式的cookie设置参数
                parse_str($option, $option);
            }               
            $config     = array_merge($config, array_change_key_case($option,CASE_LOWER));
        }
        if(!empty($config['httponly'])){
            ini_set("session.cookie_httponly", 1);
        }
        // 清除指定前缀的所有cookie
        if(is_null($name)) {
            if(empty($_COOKIE)) {
                return null;
            }               
            // 要删除的cookie前缀，不指定则删除config设置的指定前缀
            $prefix = empty($value) ? $config['prefix'] : $value;
            if(!empty($prefix)) {// 如果前缀为空字符串将不作处理直接返回
                foreach ($_COOKIE as $key => $val) {
                    if (0 === stripos($key, $prefix)) {
                        setcookie($key, '', time() - 3600, $config['path'], $config['domain'],$config['secure'],$config['httponly']);
                        unset($_COOKIE[$key]);
                    }
                }
            }
            return null;
        }elseif('' === $name){
            // 获取全部的cookie
            return $_COOKIE;
        }
        #获取指定cookie名的cookie
        $name = $config['prefix'] . str_replace('.', '_', $name);
        if('' === $value) {
            if(isset($_COOKIE[$name])){
                $value =    $_COOKIE[$name];
                if(0===strpos($value,'Array:')){
                    $value  =   substr($value,6);
                    return array_map('urldecode',json_decode(MAGIC_QUOTES_GPC?stripslashes($value):$value,true));
                }else{
                    return $value;
                }
            }else{
                return null;
            }
        }else {
            if(is_null($value)) {
                setcookie($name, '', time() - 3600, $config['path'], $config['domain'],$config['secure'],$config['httponly']);
                unset($_COOKIE[$name]); // 删除指定cookie
            }else {
                // 设置cookie
                if(is_array($value)){
                    $value  = 'Array:'.json_encode(array_map('urlencode',$value));#数组类型的cookie值转换成字符串
                }
                $expire = !empty($config['expire']) ? time() + intval($config['expire']) : 0;
                setcookie($name, $value, $expire, $config['path'], $config['domain'],$config['secure'],$config['httponly']);
                $_COOKIE[$name] = $value;
            }
        }
        return null;
    }

    /**
     * 将一个Unix时间戳转换成“xx前”模糊时间表达方式
     * @param  mixed $timestamp Unix时间戳
     * @return boolean
     */
    function time_ago($timestamp)
    {
        $etime = time() - $timestamp;
        if ($etime < 1) return '刚刚';
            $interval = array (
            12 * 30 * 24 * 60 * 60  =>  '年前 ('.date('Y-m-d', $timestamp).')',
            30 * 24 * 60 * 60       =>  '个月前 ('.date('m-d', $timestamp).')',
            7 * 24 * 60 * 60        =>  '周前 ('.date('m-d', $timestamp).')',
            24 * 60 * 60            =>  '天前',
            60 * 60                 =>  '小时前',
            60                      =>  '分钟前',
            1                       =>  '秒前'
        );
        foreach ($interval as $secs => $str) {
            $d = $etime / $secs;
            if ($d >= 1) {
                $r = round($d);
                return $r . $str;
            }
        }
    }

    /**
     * 判断是否SSL协议 author:tp
     * @return boolean
     */
    function is_ssl()
    {
        if(isset($_SERVER['HTTPS']) && ('1' == $_SERVER['HTTPS'] || 'on' == strtolower($_SERVER['HTTPS']))){
            return true;
        }elseif(isset($_SERVER['SERVER_PORT']) && ('443' == $_SERVER['SERVER_PORT'] )) {
            return true;
        }
        return false;
    }

    /**
     * 获取客户端IP地址 author:tp
     * @param mixed $type 返回类型 0|false 返回IP地址 1|true 返回IPV4地址数字
     * @param boolean $adv 是否进行高级模式获取（有可能被伪装）---代理情况 
     * @return mixed
     */
    function get_client_ip($type = 0,$adv=false)
    {
        $type       =  $type ? 1 : 0;
        static $ip  =   NULL;
        if ($ip !== NULL) return $ip[$type];
        if($adv){
            if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                $arr    =   explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
                $pos    =   array_search('unknown',$arr);
                if(false !== $pos) unset($arr[$pos]);
                $ip     =   trim($arr[0]);
            }elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
                $ip     =   $_SERVER['HTTP_CLIENT_IP'];
            }elseif (isset($_SERVER['REMOTE_ADDR'])) {
                $ip     =   $_SERVER['REMOTE_ADDR'];
            }
        }elseif (isset($_SERVER['REMOTE_ADDR'])) {
            $ip     =   $_SERVER['REMOTE_ADDR'];
        }
        // IP地址合法验证
        $long = sprintf("%u",ip2long($ip));
        $ip   = $long ? array($ip, $long) : array('0.0.0.0', 0);
        return $ip[$type];
    }

    /**
     * URL重定向 重定向后调用该函数的脚本将终止运行 author:tp
     * @param string $url 重定向的URL地址
     * @param integer $time 重定向的等待时间（秒）
     * @param string $msg 重定向前的提示信息
     * @return void
     */
    function redirect($url, $time=0, $msg='')
    {
        //多行URL地址支持
        $url        = str_replace(array("\n", "\r"), '', $url);
        if (empty($msg))
            $msg    = "系统将在{$time}秒之后自动跳转到{$url}！";
        if (!headers_sent()) {
            // redirect
            if (0 === $time) {
                header('Location: ' . $url);
            } else {
                header("refresh:{$time};url={$url}");
                echo($msg);
            }
            exit();
        } else {
            $str      = "<meta http-equiv='Refresh' content='{$time};URL={$url}'>";
            if ($time != 0)
                $str .= $msg;
            exit($str);
        }
    }

    /**
     * 浏览器友好的变量输出==用于调试 author:tp
     * @param mixed   $var 变量
     * @param boolean $echo 是否输出 默认为True 如果为false 则返回输出字符串
     * @param string  $label 标签 默认为空
     * @param boolean $strict 是否严谨 默认为true
     * @return void|string
     */
    function dump($var, $echo=true, $label=null, $strict=true)
    {
        $label = ($label === null) ? '' : rtrim($label) . ' ';
        if (!$strict) {
            if (ini_get('html_errors')) {
                $output = print_r($var, true);
                $output = '<pre>' . $label . htmlspecialchars($output, ENT_QUOTES) . '</pre>';
            } else {
                $output = $label . print_r($var, true);
            }
        } else {
            ob_start();
            var_dump($var);
            $output = ob_get_clean();
            if (!extension_loaded('xdebug')) {
                $output = preg_replace('/\]\=\>\n(\s+)/m', '] => ', $output);
                $output = '<pre>' . $label . htmlspecialchars($output, ENT_QUOTES) . '</pre>';
            }
        }
        if ($echo) {
            echo($output);
            return null;
        }else
            return $output;
    }

    /**
     * 发送HTTP状态 author:tp
     * @param integer $code 状态码
     * @return void
     */
    function send_http_status($code)
    {
        static $_status = array(
                // Informational 1xx
                100 => 'Continue',
                101 => 'Switching Protocols',
                // Success 2xx
                200 => 'OK',
                201 => 'Created',
                202 => 'Accepted',
                203 => 'Non-Authoritative Information',
                204 => 'No Content',
                205 => 'Reset Content',
                206 => 'Partial Content',
                // Redirection 3xx
                300 => 'Multiple Choices',
                301 => 'Moved Permanently',
                302 => 'Moved Temporarily ',  // 1.1
                303 => 'See Other',
                304 => 'Not Modified',
                305 => 'Use Proxy',
                // 306 is deprecated but reserved
                307 => 'Temporary Redirect',
                // Client Error 4xx
                400 => 'Bad Request',
                401 => 'Unauthorized',
                402 => 'Payment Required',
                403 => 'Forbidden',
                404 => 'Not Found',
                405 => 'Method Not Allowed',
                406 => 'Not Acceptable',
                407 => 'Proxy Authentication Required',
                408 => 'Request Timeout',
                409 => 'Conflict',
                410 => 'Gone',
                411 => 'Length Required',
                412 => 'Precondition Failed',
                413 => 'Request Entity Too Large',
                414 => 'Request-URI Too Long',
                415 => 'Unsupported Media Type',
                416 => 'Requested Range Not Satisfiable',
                417 => 'Expectation Failed',
                // Server Error 5xx
                500 => 'Internal Server Error',
                501 => 'Not Implemented',
                502 => 'Bad Gateway',
                503 => 'Service Unavailable',
                504 => 'Gateway Timeout',
                505 => 'HTTP Version Not Supported',
                509 => 'Bandwidth Limit Exceeded'
        );
        if(isset($_status[$code])) {
            header('HTTP/1.1 '.$code.' '.$_status[$code]);
            // 确保FastCGI模式下正常
            header('Status:'.$code.' '.$_status[$code]);
        }
    }

    /**
     * XML编码 author:tp
     * @param mixed $data 数据
     * @param string $root 根节点名
     * @param string $item 数字索引的子节点名
     * @param string $attr 根节点属性
     * @param string $id   数字索引子节点key转换的属性名
     * @param string $encoding 数据编码
     * @return string
     */
    function xml_encode($data, $root='root', $item='item', $attr='', $id='id', $encoding='utf-8')
    {
        if(is_array($attr)){
            $_attr = array();
            foreach ($attr as $key => $value) {
                $_attr[] = "{$key}=\"{$value}\"";
            }
            $attr = implode(' ', $_attr);
        }
        $attr   = trim($attr);
        $attr   = empty($attr) ? '' : " {$attr}";
        $xml    = "<?xml version=\"1.0\" encoding=\"{$encoding}\"?>";
        $xml   .= "<{$root}{$attr}>";
        $xml   .= data_to_xml($data, $item, $id);
        $xml   .= "</{$root}>";
        return $xml;
    }

    /**
     * 数据XML编码 author:tp
     * @param mixed  $data 数据
     * @param string $item 数字索引时的节点名称
     * @param string $id   数字索引key转换为的属性名
     * @return string
     */
    function data_to_xml($data, $item='item', $id='id')
    {
        $xml = $attr = '';
        foreach ($data as $key => $val) {
            if(is_numeric($key)){
                $id && $attr = " {$id}=\"{$key}\"";
                $key  = $item;
            }
            $xml    .=  "<{$key}{$attr}>";
            $xml    .=  (is_array($val) || is_object($val)) ? data_to_xml($val, $item, $id) : $val;
            $xml    .=  "</{$key}>";
        }
        return $xml;
    }

    /**
    * 可逆的字符串加密和解密方法 discuz中的方法
    * 该函数密文的安全性主要在于密匙并且是可逆的，若用于密码处理建议使用password_hash和password_verfiy
    * 该可逆加密主要用于一些需要时间有效性效验的数据交换中
    * @param  string  $string    明文或密文
    * @param  boolean $isEncode  是否解密，true则为解密 false默认表示加密字符串
    * @param  string  $key       密钥 默认jjonline !!!!!!
    * @param  int     $expiry    密钥有效期 单位：秒 默认0为永不过期
    * @return string 空字符串表示解密失败（密文已过期） 
    */ 
    function reversible_crypt($string, $isDecode = false, $key = 'jjonline', $expiry = 0)
    {
        dump($key);exit;
        $ckey_length            =   4;
        // 密匙
        $key                    =   md5($key ? $key : 'jjonline'); 
        // 密匙a会参与加解密
        $keya                   =   md5(substr($key, 0, 16));
        // 密匙b会用来做数据完整性验证
        $keyb                   =   md5(substr($key, 16, 16));
        // 密匙c用于变化生成的密文
        $keyc                   =   $ckey_length ? ($isEncode ? substr($string, 0, $ckey_length): substr(md5(microtime()), -$ckey_length)) : '';
        // 参与运算的密匙
        $cryptkey               =   $keya.md5($keya.$keyc);
        $key_length             =   strlen($cryptkey);
        // 明文，前10位用来保存时间戳，解密时验证数据有效性，10到26位用来保存$keyb(密匙b)，解密时会通过这个密匙验证数据完整性
        // 如果是解码的话，会从第$ckey_length位开始，因为密文前$ckey_length位保存 动态密匙，以保证解密正确
        $string                 =   $isEncode ? base64_decode(substr($string, $ckey_length)) : sprintf('%010d', $expiry ? $expiry + time() : 0).substr(md5($string.$keyb), 0, 16).$string;
        $string_length          =   strlen($string);
        $result                 =   '';
        $box                    =   range(0, 255);
        $rndkey                 =   array();
        // 产生密匙簿
        for($i = 0; $i <= 255; $i++) {
            $rndkey[$i]         =   ord($cryptkey[$i % $key_length]);
        }
        // 用固定的算法，打乱密匙簿，增加随机性，好像很复杂，实际上并不会增加密文的强度
        for($j = $i = 0; $i < 256; $i++) {
            $j                  =   ($j + $box[$i] + $rndkey[$i]) % 256;
            $tmp                =   $box[$i];
            $box[$i]            =   $box[$j];
            $box[$j]            =   $tmp;
        }
        // 核心加解密部分
        for($a = $j = $i = 0; $i < $string_length; $i++) {
            $a                  =   ($a + 1) % 256;
            $j                  =   ($j + $box[$a]) % 256;
            $tmp                =   $box[$a];
            $box[$a]            =   $box[$j];
            $box[$j]            =   $tmp;
            // 从密匙簿得出密匙进行异或，再转成字符
            $result            .=   chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
        }
        if($isEncode) {
            // substr($result, 0, 10) == 0 验证数据有效性
            // substr($result, 0, 10) - time() > 0 验证数据有效性
            // substr($result, 10, 16) == substr(md5(substr($result, 26).$keyb), 0, 16) 验证数据完整性
            // 验证数据有效性，请看未加密明文的格式
            if((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26).$keyb), 0, 16)) {
                return substr($result, 26);
            } else {
                return '';
            }
        } else {
            // 把动态密匙保存在密文里，这也是为什么同样的明文，生成不同密文后能解密的原因
            // 因为加密后的密文可能是一些特殊字符，复制过程可能会丢失，所以用base64编码
            return $keyc.str_replace('=', '', base64_encode($result));
        }
    }

    /**
     * 生成UUID 单机使用
     * @param void
     * @return string
     */
    function uuid()
    {
        $charid  = md5(uniqid(mt_rand(), true));
        $hyphen  = chr(45);// "-"
        $uuid    = //chr(123).// "{"
                substr($charid, 0, 8).$hyphen
               .substr($charid, 8, 4).$hyphen
               .substr($charid,12, 4).$hyphen
               .substr($charid,16, 4).$hyphen
               .substr($charid,20,12);
               //.chr(125);// "}"
        return $uuid;
    }

    /**
     * 字符串截取，支持中文和其他编码 author:tp
     * @param string $str 需要转换的字符串
     * @param string $start 开始位置
     * @param string $length 截取长度
     * @param string $charset 编码格式
     * @return string
     */
    function mbsubstr($str, $start=0, $length, $charset="utf-8")
    {
        if(function_exists("mb_substr"))
            $slice = mb_substr($str, $start, $length, $charset);
        elseif(function_exists('iconv_substr')) {
            $slice = iconv_substr($str,$start,$length,$charset);
        }else{
            $re['utf-8']   = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
            $re['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
            $re['gbk']    = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
            $re['big5']   = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
            preg_match_all($re[$charset], $str, $match);
            $slice = join("",array_slice($match[0], $start, $length));
        }
        return  $slice;
    }

    /**
     * 产生随机字串 author:tp
     * 默认长度6位 字母和数字混合 支持中文
     * @param string $len 长度
     * @param string $type 字串类型
     * 0 字母 1 数字 2大写字母 3小写字母 4中文 默认：大小写字母和数字混合并且去除了容易混淆的字母oOLl和数字01
     * @param string $addChars 额外添加进去的字符
     * @return string
     */
    function rand_string($len=6,$type='',$addChars='')
    {
        $str ='';
        switch($type) {
            case 0:
                $chars='ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz'.$addChars;
                break;
            case 1:
                $chars= str_repeat('0123456789',3);
                break;
            case 2:
                $chars='ABCDEFGHIJKLMNOPQRSTUVWXYZ'.$addChars;
                break;
            case 3:
                $chars='abcdefghijklmnopqrstuvwxyz'.$addChars;
                break;
            case 4:
                $chars = "们以我到他会作时要动国产的一是工就年阶义发成部民可出能方进在了不和有大这主中人上为来分生对于学下级地个用同行面说种过命度革而多子后自社加小机也经力线本电高量长党得实家定深法表着水理化争现所二起政三好十战无农使性前等反体合斗路图把结第里正新开论之物从当两些还天资事队批点育重其思与间内去因件日利相由压员气业代全组数果期导平各基或月毛然如应形想制心样干都向变关问比展那它最及外没看治提五解系林者米群头意只明四道马认次文通但条较克又公孔领军流入接席位情运器并飞原油放立题质指建区验活众很教决特此常石强极土少已根共直团统式转别造切九你取西持总料连任志观调七么山程百报更见必真保热委手改管处己将修支识病象几先老光专什六型具示复安带每东增则完风回南广劳轮科北打积车计给节做务被整联步类集号列温装即毫知轴研单色坚据速防史拉世设达尔场织历花受求传口断况采精金界品判参层止边清至万确究书术状厂须离再目海交权且儿青才证低越际八试规斯近注办布门铁需走议县兵固除般引齿千胜细影济白格效置推空配刀叶率述今选养德话查差半敌始片施响收华觉备名红续均药标记难存测士身紧液派准斤角降维板许破述技消底床田势端感往神便贺村构照容非搞亚磨族火段算适讲按值美态黄易彪服早班麦削信排台声该击素张密害侯草何树肥继右属市严径螺检左页抗苏显苦英快称坏移约巴材省黑武培著河帝仅针怎植京助升王眼她抓含苗副杂普谈围食射源例致酸旧却充足短划剂宣环落首尺波承粉践府鱼随考刻靠够满夫失包住促枝局菌杆周护岩师举曲春元超负砂封换太模贫减阳扬江析亩木言球朝医校古呢稻宋听唯输滑站另卫字鼓刚写刘微略范供阿块某功套友限项余倒卷创律雨让骨远帮初皮播优占死毒圈伟季训控激找叫云互跟裂粮粒母练塞钢顶策双留误础吸阻故寸盾晚丝女散焊功株亲院冷彻弹错散商视艺灭版烈零室轻血倍缺厘泵察绝富城冲喷壤简否柱李望盘磁雄似困巩益洲脱投送奴侧润盖挥距触星松送获兴独官混纪依未突架宽冬章湿偏纹吃执阀矿寨责熟稳夺硬价努翻奇甲预职评读背协损棉侵灰虽矛厚罗泥辟告卵箱掌氧恩爱停曾溶营终纲孟钱待尽俄缩沙退陈讨奋械载胞幼哪剥迫旋征槽倒握担仍呀鲜吧卡粗介钻逐弱脚怕盐末阴丰雾冠丙街莱贝辐肠付吉渗瑞惊顿挤秒悬姆烂森糖圣凹陶词迟蚕亿矩康遵牧遭幅园腔订香肉弟屋敏恢忘编印蜂急拿扩伤飞露核缘游振操央伍域甚迅辉异序免纸夜乡久隶缸夹念兰映沟乙吗儒杀汽磷艰晶插埃燃欢铁补咱芽永瓦倾阵碳演威附牙芽永瓦斜灌欧献顺猪洋腐请透司危括脉宜笑若尾束壮暴企菜穗楚汉愈绿拖牛份染既秋遍锻玉夏疗尖殖井费州访吹荣铜沿替滚客召旱悟刺脑措贯藏敢令隙炉壳硫煤迎铸粘探临薄旬善福纵择礼愿伏残雷延烟句纯渐耕跑泽慢栽鲁赤繁境潮横掉锥希池败船假亮谓托伙哲怀割摆贡呈劲财仪沉炼麻罪祖息车穿货销齐鼠抽画饲龙库守筑房歌寒喜哥洗蚀废纳腹乎录镜妇恶脂庄擦险赞钟摇典柄辩竹谷卖乱虚桥奥伯赶垂途额壁网截野遗静谋弄挂课镇妄盛耐援扎虑键归符庆聚绕摩忙舞遇索顾胶羊湖钉仁音迹碎伸灯避泛亡答勇频皇柳哈揭甘诺概宪浓岛袭谁洪谢炮浇斑讯懂灵蛋闭孩释乳巨徒私银伊景坦累匀霉杜乐勒隔弯绩招绍胡呼痛峰零柴簧午跳居尚丁秦稍追梁折耗碱殊岗挖氏刃剧堆赫荷胸衡勤膜篇登驻案刊秧缓凸役剪川雪链渔啦脸户洛孢勃盟买杨宗焦赛旗滤硅炭股坐蒸凝竟陷枪黎救冒暗洞犯筒您宋弧爆谬涂味津臂障褐陆啊健尊豆拔莫抵桑坡缝警挑污冰柬嘴啥饭塑寄赵喊垫丹渡耳刨虎笔稀昆浪萨茶滴浅拥穴覆伦娘吨浸袖珠雌妈紫戏塔锤震岁貌洁剖牢锋疑霸闪埔猛诉刷狠忽灾闹乔唐漏闻沈熔氯荒茎男凡抢像浆旁玻亦忠唱蒙予纷捕锁尤乘乌智淡允叛畜俘摸锈扫毕璃宝芯爷鉴秘净蒋钙肩腾枯抛轨堂拌爸循诱祝励肯酒绳穷塘燥泡袋朗喂铝软渠颗惯贸粪综墙趋彼届墨碍启逆卸航衣孙龄岭骗休借".$addChars;
                break;
            default :
                // 默认去掉了容易混淆的字符oOLl和数字01，要添加请使用addChars参数
                $chars='ABCDEFGHIJKMNPQRSTUVWXYZabcdefghijkmnpqrstuvwxyz23456789'.$addChars;
                break;
        }
        if($len>10 ) {//位数过长重复字符串一定次数
            $chars= $type==1? str_repeat($chars,$len) : str_repeat($chars,5);
        }
        if($type!=4) {
            $chars   =   str_shuffle($chars);
            $str     =   substr($chars,0,$len);
        }else{
            // 中文随机字
            for($i=0;$i<$len;$i++){
              $str.= self::msubstr($chars, floor(mt_rand(0,mb_strlen($chars,'utf-8')-1)),1,'utf-8',false);
            }
        }
        return $str;
    }

    /**
    * 格式化字节大小 author:tp
    * @param  number $size      字节数
    * @param  string $delimiter 数字和单位分隔符
    * @return string            格式化后的带单位的大小
    */
    function format_bytes($size, $delimiter = '')
    {
        $units = array('B', 'KB', 'MB', 'GB', 'TB', 'PB');
        for($i = 0; $size >= 1024 && $i < 6; $i++) {
            $size /= 1024;
        }
        return round($size, 2) . $delimiter . $units[$i];
    }

    /**
     * 清理html内容中的js代码和各种标签内包裹的onXX事件
     * 直接清理掉所有标签内属性即可
     * {
     *     1、清理所有js代码
     *     2、清理所有标签内属性性质的js事件
     * }
     * @param  string $content 待清理的html文本
     * @return string 清理妥善的html文本
     */
    function clear_html($content)
    {
        ##去除所有JavaScript代码
        $content = preg_replace('/<script(.*?)<\/?script>/is', "", $content);

        ##去除所有a标签
        // $content = preg_replace('/<\/?a[^>]>/', '', $content); ##a标签相对危害小一些，依据实际情况取消注释

        ##去除标签内的各种属性  排除对img、a标签的误杀
        return preg_replace('/<(?!a\s+|img\s+)(\w+)\s+[^>]+>/', '<${1}>', $content);
    }

    /**
     * 将相对url转换为绝对完整Url
     * <code>
     *     将某一个Url（当前Url）页面中的超链接不同的写法转换为实际完整的Url（待转换Url）
     *     
     *     例如1、当前Url为：
     *         http://blog.jjonline.cn/phptech/172.html，待转换Url为：/view/173.html
     *         则待转换Url的实际完整Url为：http://blog.jjonline.cn/view/173.html
     *     例如2、当前Url为：
     *         http://blog.jjonline.cn/phptech/172.html，待转换Url为：./173.html 或 173.html
     *         则待转换Url的实际完整Url为：http://blog.jjonline.cn/phptech/173.html
     *     例如3、当前Url为：
     *         http://blog.jjonline.cn/phptech/172.html，待转换Url为：../view/173.html
     *         则待转换Url的实际完整Url为：http://blog.jjonline.cn/view/173.html
     *     例如4、当前Url为：
     *         http://blog.jjonline.cn/phptech/view/172.html，待转换Url为：./../../173.html
     *         则待转换Url的实际完整Url为：http://blog.jjonline.cn/173.html
     *         
     *     当然第3种和第4种比较变态，但这种Url也是可能存在的
     * </code>
     * @param  [string] $sUrl    页面中的Url，例如：./../../171.html
     * @param  [string] $baseUrl 该页面的Url，例如：http://blog.jjonline.cn/sort/php/area/article/173.html
     * @return [string]
     */
    function to_absolute_url($sUrl,$baseUrl) 
    {
        $srcinfo = parse_url($sUrl);
        if(isset($srcinfo['scheme'])) {
            ##完整的Url无需转换
            return $sUrl;
        }

        $baseinfo = parse_url($baseUrl);
        $url      = $baseinfo['scheme'].'://'.$baseinfo['host'];##识别出基础的根Url

        ##识别出待转换Url中的路径部分
        if(substr($srcinfo['path'], 0, 1) == '/') {
            $path = $srcinfo['path'];
        }else{
            $path = dirname($baseinfo['path']).'/'.$srcinfo['path'];
        }
        $rst        = array();##保存待转换Url中的路径部分，索引数组，一个元素是一个文件夹名或.和.. 下方对.和..进行替换
        $path_array = explode('/', $path);
        if(!$path_array[0]) {
            $rst[] = '';
        }

        foreach ($path_array as $key => $dir) {
            if ($dir == '..') {
                if (end($rst) == '..') {
                    $rst[] = '..';
                }elseif(!array_pop($rst)) {
                    $rst[] = '..';
                }
            }elseif($dir && $dir != '.') {
                $rst[] = $dir;
            }
        }

        if(!end($path_array)) {
            $rst[] = '';
        }
        $url .= implode('/', $rst);
        return str_replace('\\', '/', $url);
    }
}