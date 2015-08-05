<?php
/**
 * php-libaray示例
 * @authors Jea杨 (JJonline@JJonline.Cn)
 * @date    2015-8-5 14:39:52
 * @version 1.0
 */ 

/**
 * 未启用命名空间的自动加载(实现命名空间很简单，就不再墨迹) 匿名函数方式实现 
 * @param null
 */ 
spl_autoload_register(function ($class) {
	var_dump($class);
});
$obj =  new \namespaces\classo();