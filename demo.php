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
<link href="./view.css" rel="stylesheet" type="text/css" media='all' />
<script src="http://blog.jjonline.cn/admin/editor/plugins/code/prettify.js" type="text/javascript"></script>
</head>
<body>
<?php
/**
 * php-libaray示例 本示例中许多代码并未简写 主要是为了便于阅读和理解
 * @authors Jea杨 (JJonline@JJonline.Cn)
 * @date    2015-8-5 14:39:52
 * @version 1.0
 */ 
/**
 * 启用命名空间的自动加载(去掉命名空间很简单，就不再墨迹) 匿名函数方式实现 
 * @param null
 */ 
spl_autoload_register(function ($class) {
	$Dir  		         =  str_replace('\\', '/', $class).'.class.php';
	$FileName            =  __DIR__.'/'.$Dir;#realpath('./'.$Dir)亦可 但调用了函数肯定会适度加重负担;
	include $FileName;
});
#Function文件夹中的文件自动加载
$FunctionDir            =  __DIR__.'/Function/';
if(is_dir($FunctionDir)) {
   $iterator            =  new DirectoryIterator($FunctionDir);
   while($iterator->valid()) {
       if($iterator->isFile()){
         $FileDir       =  $FunctionDir.$iterator->getFilename();
         if($iterator->getExtension() == 'php') {
            include $FileDir;
         }
       }
       $iterator->next();
   }
}
#文件结构迭代器
function getDirInfo($dir) {
   $iterator                     =   new DirectoryIterator($dir);
   #先输出文件夹
   while($iterator->valid()) {
       if($iterator->isDir() && $iterator->getFilename()!='.' && $iterator->getFilename()!='..' && $iterator->getFilename()!='.git') {
         echo '<li class="flist filedir"><i class="fa fa-folder-open"></i> '.$iterator->getFilename();
         echo '<ul class="dirlist">';
         getDirInfo($iterator->getPathname());
         echo '</ul></li>';
       }       
       $iterator->next();
   }
   #再输出文件
   $iterator->Rewind();
   while($iterator->valid()) {
      if($iterator->isFile()){
         echo '<li class="flist file"><i class="fa fa-file-text"></i> '.$iterator->getFilename().'</li>';
      }
      $iterator->next();
   }          
}
?>
</body>
<header class="header container">
	<h1 class="php-libaray">php-libaray示例 <em>相关代码详见：<a href="https://github.com/jjonline/php-libaray" target=_blank>https://github.com/jjonline/php-libaray</a></em></h1>
   <h2 class="php-libaray-des">注：本代码示例需要PHP5.3.0及其以上的PHP环境；当前服务器PHP版本：<?php echo PHP_VERSION;?></h2>
</header>
<section class="container">
   <article class="list fileSystem">
      <h2>php-libaray文件结构</h2>
      <ul class="php-libaray-dir">
         <?php
            getDirInfo(__DIR__);
         ?>
      </ul>
   </article>
   <article class="list init">
      <h2>本Demo的自动加载机制</h2>
<pre class="prettyprint lang-php linenums">&lt;?php
/**
 * 类自动加载 
 * @param null
 */ 
spl_autoload_register(function ($class) {
   $Dir        =  str_replace('\\', '/', $class).'.class.php';
   $FileName   =  __DIR__.'/'.$Dir;#realpath('./'.$Dir)亦可 但调用了函数肯定会适度加重负担;
   include $FileName;
});
#Function文件夹中的函数方法文件自动加载
$FunctionDir =  __DIR__.'/Function/';
if(is_dir($FunctionDir)) {
   $iterator =  new DirectoryIterator($FunctionDir);
   while($iterator->valid()) {
       if($iterator->isFile()){
         $FileDir =  $FunctionDir.$iterator->getFilename();
         if($iterator->getExtension() == 'php') {
            include $FileDir;
         }
       }
       $iterator->next();
   }
}
?&gt;</pre>
   </article>
   <article class="list init">
      <h2>Function文件夹中的一些函数方法示例</h2>
      <p>各函数使用方法和说明详见：Function文件夹中的README.md介绍。</p>

      <p>运行结果：</p>
      <p>1、<code>password_hash</code>不指定盐值加密密码<code>zheshiyigemima123</code>输出结果：<code><?php var_dump(password_hash('zheshiyigemima123',PASSWORD_BCRYPT));?></code>；没有通过第三个参数指定固定的盐值（系统会自己添加随机盐值），所以每刷新下结果都会变化的~~~</p>
      <p>2、<code>password_verify</code>效验密码<code>zheshiyigemima123</code>和曾经保存过的一个hash串<code>$2y$10$WITh4hNt3PZtWIidd8btEOgQiRhUq15ofxDOZQqIDs3BJD/XnEDAu</code>输出结果：<code><?php var_dump(password_verify('zheshiyigemima123','$2y$10$WITh4hNt3PZtWIidd8btEOgQiRhUq15ofxDOZQqIDs3BJD/XnEDAu'));?></code></p>
      <p>3、<code>password_get_info</code>获取hash串<code>$2y$10$WITh4hNt3PZtWIidd8btEOgQiRhUq15ofxDOZQqIDs3BJD/XnEDAu</code>的信息，输出结果：<code><?php var_dump(password_get_info('$2y$10$WITh4hNt3PZtWIidd8btEOgQiRhUq15ofxDOZQqIDs3BJD/XnEDAu'));?></code></p>
      <p>4、<code>is_mail_valid</code>检测字符串<code>JJonline@JJonline.Cn</code>是否是一个合法的邮箱格式，输出：<code><?php var_dump(is_mail_valid('JJonline@JJonline.Cn'));?></code>；检测字符串<code>JJon-l#?ine@JJonline.Cn</code>是否是一个合法的邮箱格式，输出：<code><?php var_dump(is_mail_valid("JJon-l#?ine@JJonline.Cn"));?></code>
         <br>Ps1：其实按 RFC822标准<code>JJon-l#?ine@JJonline.Cn</code>是一个合法的邮箱地址，只不过如此反人类的邮箱地址果断不拽它
         <br>Ps2：<code>is_mail_valid</code>默认采用正则检测，也可以采用<code>filter_var($mail,FILTER_VALIDATE_EMAIL)</code>方式支持RFC822标准
         <br>Ps3：<code>is_mail_valid</code>检测邮箱格式的标准为：用户名部分构成仅能为数字、字母、下划线、加好、减号(中划线)和点，且开头位置仅能为数字或字母；域名部分按域名规则，支持域名中有减号(或者称之为中划线)
         <br>&nbsp;&nbsp;&nbsp;测试支持检测的邮箱格式类型：
         <br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<code>is_mail_valid</code>检测邮箱地址字符串<code>JJonline-Cn@JJonline.Cn</code>的结果<code><?php var_dump(is_mail_valid('JJonline-Cn@JJonline.Cn'));?></code>；
         <br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<code>is_mail_valid</code>检测邮箱地址字符串<code>JJonline.Cn@JJonline.Cn</code>的结果<code><?php var_dump(is_mail_valid('JJonline.Cn@JJonline.Cn'));?></code>；
         <br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<code>is_mail_valid</code>检测邮箱地址字符串<code>JJonline_Com.Cn@JJonline.Cn</code>的结果<code><?php var_dump(is_mail_valid('JJonline_Com.Cn@JJonline.Cn'));?></code>；
         <br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<code>is_mail_valid</code>检测邮箱地址字符串<code>JJonline.Com.Cn@JJonline.Cn</code>的结果<code><?php var_dump(is_mail_valid('JJonline.Com.Cn@JJonline.Cn'));?></code>；
         <br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<code>is_mail_valid</code>检测邮箱地址字符串<code>123456@JJonline.Cn</code>的结果<code><?php var_dump(is_mail_valid('123456@JJonline.Cn'));?></code>；
         <br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<code>is_mail_valid</code>检测邮箱地址字符串<code>JJonline.Cn@JJonline-Com.Cn</code>的结果<code><?php var_dump(is_mail_valid('JJonline.Cn@JJonline-Com.Cn'));?></code>；
         <br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;So，符合人类认知的几种邮箱格式检测均已支持~倘若需要支持检测邮箱中域名后缀为特定的，比如.com、.cn的还不支持
         <br>Ps4：邮箱地址和网站网站域名一样是不区分大小写滴；那么问题来了Url区分大小写吗？答案是：看情况，因为*nux文件系统区分，而window文件系统特么又不区分~；只能说协议部分和域名部分是不区分大小写滴~
      </p>
      <p>5、<code>is_phone_valid</code>检测存在的天朝手机号<code>15872254727</code>，输出：<code><?php var_dump(is_phone_valid('17612345678'));?></code>；<code>is_phone_valid</code>检测不存在的天朝手机号<code>170123456</code>，输出：<code><?php var_dump(is_phone_valid('170123456'));?></code>；天朝手机号11位，开头为13[0-9]、14[0-9]、15[0-9]、18[0-9]、176、177、178新号段以及虚拟运营商的170[059]</p>
      <p>6、<code>is_url_valid</code>检测Url<code>https://www.jjonline.cn:443/UserInfo/index.php?UserId=123456&type=Vip#Node=part1</code>，输出<code><?php var_dump(is_url_valid('https://www.jjonline.cn:443/UserInfo/index.php?UserId=123456&type=Vip#Node=part1'));?></code>；该方法仅检测http或https打头的Url，包括端口、get变量和锚点支持</p>
      <p>7、<code>is_uid_valid</code>检测QQ号<code>77808859</code>，输出：<code><?php var_dump(is_uid_valid('77808859'));?></code>；该方法三个参数，第一个必选参数为需要检测的数字账户id，第二个可选参数指定合法的数字账户最短位数[默认4位]，第三个可选参数指定合法的数字账户最长位数[默认11位]。</p>
      <p>8、<code>is_password_valid</code>检测密码字符串<code>mima123456</code>，输出<code><?php var_dump(is_password_valid('mima123456'));?></code>；该方法检测的密码字符串必须同时包含字母和数字；该方法三个参数，第一个必选参数为需要检测的密码字符串，第二个可选参数指定合法的密码字符串最短长度[默认8位]，第三个可选参数指定合法的密码字符串最长长度[默认16位]。</p>
      <p>9、<code>is_citizen_id_valid</code>检测身份证号<code>420521198907031846</code>是否合乎规范，输出：<code><?php var_dump(is_citizen_id_valid('420521198907031846'));?></code>；该函数兼容15位老身份证号和18位新身份证号（若传入15位合法的身份证号将返回转换过的18位身份证号），符合规范返回有内容的关联数组（boolean判断为true），不符合规范返回false
         <br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Ps:我都这么卖力的分享了，请给点面子不要拿此身份证号瞎搞</p>
      <p>10、<code>time_ago</code>时间友好表示法，写此示例时的时间戳<code>1438852440</code>使用<code>time_ago</code>，输出：<code><?php var_dump(time_ago('1438852440'));?></code></p>
      <p>10、<code>Input</code>统一方式获取并过滤超全局变量：$_GET、$_POST、php://input、$_REQUEST、$_SESSION、$_COOKIE、$_SERVER、$GLOBALS以及手动指定的变量；<code>Input('id',0);获取id参数，若为空或被过滤掉则返回0，自动判断是get或者post、Input('post.name','','htmlspecialchars');获取$_POST['name']并用htmlspecialchars函数进行过滤、Input('get.') 获取$_GET</code>；四个参数，第一个参数必选，注意第一个参数的书写方式、第二个参数可选，指定获取不到数据时返回的默认值，默认为空、第三个参数指定过滤数据的方法[若需多个方法过滤，多个方法名用英文逗号分隔，也可以直接指定一个正则表达式用正则去过滤（注意正则表达式的分割符必须是正斜线或者说左斜线/）]；第四个参数用于指定额外的数据源，指定第四个参数则相当于用第三个参数的过滤方法对第四个参数的数据进行过滤。Input默认Sql安全过滤需要针对特定业务场景，有需要进一步过滤，完善./Function/UsefullFunction.php中的<code>Input_filter函数</code></p>
      <p>不再详细介绍的函数：<code>format_bytes</code></p>
   </article>
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
var _hmt = _hmt || [];
(function() {
  var hm = document.createElement("script");
  hm.src = "//hm.baidu.com/hm.js?3d44ee054b721d5191136ab4f49a2292";
  var s = document.getElementsByTagName("script")[0]; 
  s.parentNode.insertBefore(hm, s);
})();
</script>
</html>