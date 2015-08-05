<!DOCTYPE HTML>
<html lang="zh-cn">
<head>
<meta charset="UTF-8">
<meta name="renderer" content="webkit">
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimum-scale=1.0, maximum-scale=1.0">
<meta http-equiv="Cache-Control" content="no-siteapp">
<meta http-equiv="Cache-Control" content="no-transform">
<title>php-libaray示例</title>
<meta name="Author" content="Jea杨(http://blog.jjonline.cn)">
<script type='text/javascript' src='http://apps.bdimg.com/libs/jquery/1.9.1/jquery.min.js'></script>
<link href="http://apps.bdimg.com/libs/bootstrap/3.3.0/css/bootstrap.min.css" rel="stylesheet" type="text/css" media='all' />
<link href="http://apps.bdimg.com/libs/fontawesome/4.2.0/css/font-awesome.min.css" rel="stylesheet" type="text/css" media='all' />
<link href="http://blog.jjonline.cn/Upload/file/view.css" rel="stylesheet" type="text/css" media='all' />
<script src="http://blog.jjonline.cn/admin/editor/plugins/code/prettify.js" type="text/javascript"></script>
</head>
<body>
<?php
/**
 * php-libaray示例
 * @authors Jea杨 (JJonline@JJonline.Cn)
 * @date    2015-8-5 14:39:52
 * @version 1.0
 */ 
/**
 * 启用命名空间的自动加载(去掉命名空间很简单，就不再墨迹) 匿名函数方式实现 
 * @param null
 */ 
spl_autoload_register(function ($class) {
	$Dir  		=	str_replace('\\', '/', $class).'.class.php';
	$FileName   =   realpath('./'.$Dir);
	include $FileName;
});
?>
</body>
<header class="header container">
	<h1 class="php-libaray">php-libaray示例</h1>
</header>
<section class="container">
	<article class="list Hashids">
		<h2>1、Hashids类：加密数字型id成字符串hash并可反向解密hash成数字id</h2>
		<p>应用场景：url中不便于直接显示成数字的id加密成唯一性的字符串（hash）；php脚本接收到该hash后又可以很方便的还原成数字id。也可以将多个数字型的参数一次性加密后作为一个GET变量附加在url中。</p>
<pre class="prettyprint lang-php linenums">&lt;?php
   #三个可选参数，
   #参数1为加密盐值
   #参数2为指定加密后的hash串最小长度
   #参数3为手动指定hash串中允许出现的字符
   $obj =  new Libaray\Hashids\Hashids('http://blog.jjonline.cn',16);
   #加密数字1成为字符串 此处可以传递多个数字一起加密 或者一个value全部为数字的索引数组
   var_dump($obj-&gt;encode(1));# 可能的字符串输出：1nZVNL5Eq5793Jyg
   #解密hash字符串为数字原型
   $restult = $obj-&gt;decode('1nZVNL5Eq5793Jyg');
   #注意解密后成为数组
   var_dump($restult);
   #加密多个数字型索引数组
   var_dump($obj-&gt;encode(1,2,3));#可能的字符串输出：K8aO5d4Uos45b2dr
   #或者写法为var_dump($obj-&gt;encode([1,2,3]));
   #此类还提供加密16进制数的方法 不再介绍 原理一致  只不过方法名变化：encode_hex和decode_hex
?&gt;</pre>
	<p>运行结果：</p>
	<p>数字<code>1</code>加密后的hash字符串为：<code><?php var_dump((new Libaray\Hashids\Hashids('http://blog.jjonline.cn',16))->encode(1));?></code></p>
	<p>hash串<code>1nZVNL5Eq5793Jyg</code>解密后的数字原型为：<code><?php var_dump((new Libaray\Hashids\Hashids('http://blog.jjonline.cn',16))->decode('1nZVNL5Eq5793Jyg'));?></code></p>
	<p>数组<code>[1,2,3]</code>加密后的hash字符串为：<code><?php var_dump((new Libaray\Hashids\Hashids('http://blog.jjonline.cn',16))->encode([1,2,3]));?></code></p>
	<p>数组型hash串<code>K8aO5d4Uos45b2dr</code>解密后的数字原型数组为：<code><?php var_dump((new Libaray\Hashids\Hashids('http://blog.jjonline.cn',16))->decode('K8aO5d4Uos45b2dr'));?></code></p>
	</article>
	<article class="list PasswordHash">
		<h2>2、PasswordHash类：不可逆的密码加密和密码对比，开源原始名：phpass</h2>
		<p>应用场景：加密密码字符串成为不可逆的hash字符串，此加密方式一个明文密码对应无数个hash串；通过保存的hash串和密码明文进行对比，又可以效验明文密码的正确性。[该加密类被WordPress、emlog等许多开源程序使用]</p>
<pre class="prettyprint lang-php linenums">&lt;?php
   #两个参数，第一个参数指定加密深度 int 取值范围5-30 第二个参数指定该加密是否可以移植指定false，更换运行环境后解密将出现问题
   $PasswordHash  =  new Libaray\PasswordHash\PasswordHash(8,true);
   #加密密码 zheshiyigemima123 可以发现每一次刷新结果都不一样 保留一个结果：$P$BPae94jMsALnSwBm5fnKiMBpLuxZfN1 用于下方密码效验试验
   var_dump($PasswordHash-&gt;HashPassword('zheshiyigemima123'));
   #对比数据库保存的密码hash串：$P$BPae94jMsALnSwBm5fnKiMBpLuxZfN1是否为明文密码：zheshiyigemima123的一个hash；换种方式描述：核对用户密码是否正确
   #第一个参数为需要核对的明文密码 第二个参数为曾经加密获得并保存的hash串 
   #该方法返回boolean值 true效验成功  false的话.....说多了都显得愚蠢了
   var_dump($PasswordHash-&gt;CheckPassword('zheshiyigemima123','$P$BPae94jMsALnSwBm5fnKiMBpLuxZfN1'));
   #继续试验  将密码明文换下 zheshiyigemima321
   var_dump($PasswordHash-&gt;CheckPassword('zheshiyigemima321','$P$BPae94jMsALnSwBm5fnKiMBpLuxZfN1'));
?&gt;	
</pre>
	<p>运行结果：</p>
	<p>密码<code>zheshiyigemima123</code>加密后的hash字符串为：<code><?php var_dump((new Libaray\PasswordHash\PasswordHash(8,true))->HashPassword('zheshiyigemima123'));?></code>；每刷新下结果都会变化的~~~</p>
	<p>用曾经保存过的一个hash串<code>$P$BPae94jMsALnSwBm5fnKiMBpLuxZfN1</code>来核对密码<code>zheshiyigemima123</code>(正确的密码)是否正确：<code><?php var_dump((new Libaray\PasswordHash\PasswordHash(8,true))->CheckPassword('zheshiyigemima123','$P$BPae94jMsALnSwBm5fnKiMBpLuxZfN1'));?></code></p>
	<p>用曾经保存过的一个hash串<code>$P$BPae94jMsALnSwBm5fnKiMBpLuxZfN1</code>来核对密码<code>zheshiyigemima321</code>(错误的密码)是否正确：<code><?php var_dump((new Libaray\PasswordHash\PasswordHash(8,true))->CheckPassword('zheshiyigemima321','$P$BPae94jMsALnSwBm5fnKiMBpLuxZfN1'));?></code></p>
	</article>
	<!--article class="list"></article-->
	<p class="more">更多...待添加，或您提<a href="https://github.com/jjonline/php-libaray/pulls" target=_blank>pull request</a></p>
</section>
<script type='text/javascript'>
prettyPrint();
</script>
</html>