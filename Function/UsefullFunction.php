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
	function Input($name,$default='',$filter=null,$datas=null) {
		static $_PUT			=	null;
		$type   				=   's';#默认转换获取到变量为string类型
		if(strpos($name,'/')){ // 指定返回类型修饰符
			list($name,$type) 	=	explode('/',$name,2);
		}
	    if(strpos($name,'.')) { // 指定参数来源
	        list($method,$name) =   explode('.',$name,2);
	    }else{ // 默认为自动判断
	        $method 			=   'param';
	    }
	    switch(strtolower($method)) {
	        case 'get'     :   
	        	$input 			=&	$_GET;#显式指定方式 引用方式调用
	        	break;
	        case 'post'    :   
	        	$input 			=&	$_POST;
	        	break;
	        case 'put'     :   
	        	if(is_null($_PUT)){
	            	parse_str(file_get_contents('php://input'), $_PUT);
	        	}
	        	$input 			=	$_PUT;        
	        	break;
	        case 'param'   :
	            switch($_SERVER['REQUEST_METHOD']) {
	                case 'POST':
	                    $input  =  	$_POST;
	                    break;
	                case 'PUT':
	                	if(is_null($_PUT)){
	                    	parse_str(file_get_contents('php://input'), $_PUT);
	                	}
	                	$input 	=	$_PUT;
	                    break;
	                default:
	                    $input  =   $_GET;
	            }
	            break;
	        case 'request' :   
	        	$input 			=&	$_REQUEST;   
	        	break;
	        case 'session' :   
	        	$input 			=&	$_SESSION;   
	        	break;
	        case 'cookie'  :   
	        	$input 			=&	$_COOKIE;    
	        	break;
	        case 'server'  :   
	        	$input 			=& 	$_SERVER;    
	        	break;
	        case 'globals' :   
	        	$input 			=& 	$GLOBALS;    
	        	break;
	        case 'data'    :   
	        	$input 			=& 	$datas;      
	        	break;
	        default:
	            return null;
	    }
	    if(''==$name) { // 获取全部变量
	        $data       		=   $input;
	        $filters    		=   isset($filter)?$filter:'';
	        if($filters) {
	            if(is_string($filters)){
	                $filters    =   explode(',',$filters);
	            }
	            foreach($filters as $filter){
	                $data   	=   array_map_recursive($filter,$data); // 参数过滤
	            }
	        }
	    }elseif(isset($input[$name])) { // 取值操作
	        $data       		=   $input[$name];
	        $filters    		=   isset($filter)?$filter:'';
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
	                        $data 	=   is_array($data) ? array_map_recursive($filter,$data) : $filter($data); // 参数过滤
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
	        		case 'a':	// 数组
	        			$data 	=	(array)$data;
	        			break;
	        		case 'd':	// 数字
	        			$data 	=	(int)$data;
	        			break;
	        		case 'f':	// 浮点
	        			$data 	=	(float)$data;
	        			break;
	        		case 'b':	// 布尔
	        			$data 	=	(boolean)$data;
	        			break;
	                case 's':   // 字符串
	                default:
	                    $data   =   (string)$data;
	        	}
	        }
	    }else{ // 变量默认值
	        $data       		=   isset($default)?$default:null;
	    }
	    #最后Input方法体统一递归必须清理的过滤方法Input_filter 请按业务需求完善Input_filter方法
	    is_array($data) && array_walk_recursive($data,'Input_filter');
	    return $data;
	}
	#Input函数专用递归调用函数处理方法
	function array_map_recursive($filter, $data) {
	    $result = array();
	    foreach ($data as $key => $val) {
	        $result[$key] = is_array($val)
	         ? array_map_recursive($filter, $val)
	         : call_user_func($filter, $val);
	    }
	    return $result;
	}
	#Input函数专用过滤数据方法
	function Input_filter(&$value){
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
	function session($name='',$value='') {
	    global $__session_prefix;#session统一前缀全局变量
	    $prefix  		 =  null;#默认无session前缀 除非通过调用session进行设置过
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
	                $_SESSION 			=	array();
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
				list($name1,$name2) 						=	explode('.',$name);
				if($prefix){
					$_SESSION[$prefix][$name1][$name2]   	=	$value;
				}else{
					$_SESSION[$name1][$name2]  				=	$value;
				}
			}else{
				if($prefix){
					$_SESSION[$prefix][$name]   			=	$value;
				}else{
					$_SESSION[$name]  						=	$value;
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
	function cookie($name='', $value='', $option=null) {
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
	function time_ago($timestamp) {
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
	* 格式化字节大小
	* @param  number $size      字节数
	* @param  string $delimiter 数字和单位分隔符
	* @return string            格式化后的带单位的大小
	* @author 
	*/
	function format_bytes($size, $delimiter = '') {
		$units = array('B', 'KB', 'MB', 'GB', 'TB', 'PB');
		for($i = 0; $size >= 1024 && $i < 6; $i++) {
			$size /= 1024;
		}
		return round($size, 2) . $delimiter . $units[$i];
	}
}