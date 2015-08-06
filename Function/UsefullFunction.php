<?php
/**
 * 实用性较强的函数封装
 * @authors Jea杨 (JJonline@JJonline.Cn)
 * @date    2015-08-06 15:09:03
 * @version 1.0
 */
namespace {
	
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