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
      <p>11、<code>Input</code>统一方式获取外部变量或用户提交的变量数据；函数原型：<code>Input('变量类型.变量名/修饰符',['默认值'],['过滤方法'],['额外数据源'])</code></p>
      <p>“变量类型”可选为：</p>
      <table>
        <tr><th>变量类型</th><th>含义解释</th></tr>
        <tr><td>get</td><td>获取GET变量；此时第四个参数无任何意义</td></tr>
        <tr><td>post</td><td>获取POST变量；此时第四个参数无任何意义</td></tr>
        <tr><td>param</td><td>自动判断请求类型获取GET、POST或者PUT变量；此时第四个参数无任何意义</td></tr>
        <tr><td>request</td><td>获取REQUEST变量；此时第四个参数无任何意义</td></tr>
        <tr><td>put</td><td>获取PUT变量；此时第四个参数无任何意义</td></tr>
        <tr><td>session</td><td>获取$_SESSION变量；建议使用session函数；此时第四个参数无任何意义</td></tr>
        <tr><td>cookie</td><td>获取$_COOKIE变量；建议使用cookie函数；此时第四个参数无任何意义</td></tr>
        <tr><td>server</td><td>获取$_SERVER变量；此时第四个参数无任何意义</td></tr>
        <tr><td>globals</td><td>获取$GLOBALS变量；此时第四个参数无任何意义</td></tr>
        <tr><td>data</td><td>获取其他类型的变量，需要第四个参数['额外数据源']配合</td></tr>
      </table>
        <p>“修饰符”可选为：<code>s、d、b、a、f</code>；表示获取的数据被强制转换的类型，s=>string[字符串]、d=>double[整形]、b=>boolean[布尔值]、a=>array[数组]、f=>float[浮点数]；未设置该参数默认为s</p>
        <p>“默认值”表示需要获取的指定变量不存在时返回这个默认值，注意<code>变量不存在的含义</code>；假设获取get变量action，也就是说<code>$_GET['action']</code>不存在才会返回默认值；这里存在这种情况：<code>($_GET['action']==='')为true</code>；这就需要对“变量是否存在”的深入理解，直接给答案不解释，这种情况Input返回空字符串并不会返回设置的默认值。</p>
        <p>“过滤方法”参数，可以是数据处理或过滤的函数名字符串（自定义函数亦可，留意过滤函数方法体的合理性），多个函数使用逗号分隔函数名成字符串或用索引数组；也可以是一个正则表达式，使用正则来过滤数据(此时表达式分隔符必须是左划线[正划线]；使用正则倘若匹配失败则不会返回原值，而是会返回设置的默认值或者null)；同时也可以是int型常量或变量用于filter_var的第二个参数，并使用filter_var进行过滤。若传递的函数并不存在，此时将尝试将该参数理解成filter_var过滤方法的第二个参数（int型）并用filter_var函数对数据过滤。</p>
        <p>“额外数据源”可以使用Input处理该函数第一个参数中的“变量类型”所不支持的数据类型（主要指那些超全局变量）；Input函数仅用于获取（并不进行数据设置），使用“额外数据源”参数则需要“变量类型”必须设置为data，继而“默认值”参数、“过滤方法”参数相互配合，用更少的代码完成更多的事情。该参数类型可以是数组也可以是字符串。</p>
        <p>注意：Input默认Sql注入的安全过滤需要针对特定业务场景，有需要进一步过滤请完善./Function/UsefullFunction.php中的<code>Input_filter函数</code>；该函数默认过滤掉纯粹的特定Sql语句中的关键词，若数据中包含这些Sql关键词是不会被过滤的！</p>
        <p>Input是一个很强大的函数，用法举例：</p>
        <ul class="sample_list">
          <li>获取所有get变量<code>Input('get.')</code>或<code>Input('get.','','trim,strip_tags')</code>；第一种写法相当于获取$_GET，第二种写法则对$_GET进行了过滤，并设置了默认值（当且仅当$_GET不存在时才会返回默认值；这里某种意义上来看设置默认值并没有什么意义，因为超全局数组在不手动unset的情况下isset均为true）</li>
          <li>获取get变量名为action的值并过滤：<code>Input('get.actoin','不存在get变量action','trim,strip_tags')</code>，输出结果：<code><?php var_dump(Input('get.action','不存在get变量action','trim,strip_tags'));?></code>，你也可以在本url上加上<code>?action= 这是action变量&lt;p&gt;值&lt;/p&gt; </code>(注意html标签p和首尾空格会被过滤掉)；这样返回的结果应该为：<code>这是action变量值</code>。（也就是传统方法中的<code>$_GET['actoin']</code>；只不过使用Input方法功能更强大，可以统一指定过滤方法也可以指定当action不存在时返回的默认值；节省很多业务逻辑代码）</li>
          <li>获取session值，<code>Input('session.uid',false)</code>若存在<code>$_SESSION['uid']</code>则返回<code>$_SESSION['uid']</code>，否则返回<code>false</code>；需要留意的是$_SESSION可能是多维（二维及其以上）数组，Input仅能获取到第一维中的数据（即一个数组），<strong>暂时并不能</strong>通过<code>Input('session.uid.name',false)</code>来获取<code>$_SESSION['uid']['name']</code>；此功能以后<strong>可能</strong>会支持。</li>
          <li>获取cookie值，<code>Input('cookie.uid',false)</code>若存在<code>$_COOKIE['uid']</code>则返回<code>$_COOKIE['uid']</code>，否则返回<code>false</code>；当然你也可以指定过滤方法。</li>
          <li>自动判断请求类型并获取指定变量名的值，<code>Input('request.id')</code>或<code>Input('id')</code>；如果当前请求类型是GET，那么等效于<code>$_GET['id']</code>，如果当前请求类型是POST或者PUT，那么相当于获取<code>$_POST['id']</code>或者PUT提交的数据中的id项数据。</li>
          <li><code>Input('server.REQUEST_METHOD')</code>获取<code>$_SERVER['REQUEST_METHOD']</code></li>
          <li>获取外部数据，<code>Input('data.file1','','',$_FILES)</code></li>
        </ul>
      <p>12、<code>session</code>函数用于统一设置、获取session</p>
      <p>13、<code>cookie</code>函数用于统一设置、获取cookie；函数原型<code>cookie('COOKIE名',['COOKIE值'],['COOKIE配置项'])</code>；注意此函数有默认配置项，可以按需定制（修改函数体开始的$config数组即可），亦可通过'COOKIE配置项'参数覆盖默认配置。</p>
      <p>'COOKIE名'：用于获取或设置指定名称的COOKIE，若'COOKIE名'传入<code>null</code>则表示删除<strong>指定前缀的所有cookie</strong>，此时若cookie名前缀为空将不做任何处理即不删除任何cookie；作为php标准，当并未按需指定默认配置时可以通过'COOKIE配置项'参数传入cookie前缀，写法为：<code>cookie(null,null,array('prefix'=>'J_'))</code>；函数体也做了变通处理还可以这么写<code>cookie(null,'J_')</code>，此时第二个参数将被理解成要删除的cookie前缀。</p>
      <p>'COOKIE值'：用于设置cookie的值，或当'COOKIE名'为<code>null</code>并且'COOKIE值'不为<code>null</code>，此时'COOKIE值'表示传入cookie前缀，以用于快速删除该cookie前缀的所有cookie。</p>
      <p>'COOKIE配置项'参数形式：</p>
      <p>'COOKIE配置项'允许三种类型变量参数：int、string(int)以及array，可以理解为两种，多数情况下允许传入数组；仅进行cooke设置时'COOKIE配置项'参数方可允许为数值型参数（无论是int还是int型的字符串）；此时的数值表示为该设置的cookie设置一个过期时间，例如<code>cookie('J_cookie','required',3600)</code>，表示设置一个名为<code>J_cookie</code>，值为<code>required</code>，过期时间为<code>3600秒</code>的cookie。当'COOKIE配置项'为数组时，该关联数组的结构为：<code>array('prefix'=>string,'expire'=>int,'path'=>path string,domain'=>string,'httponly'=>boolean)</code>，其中索引不区分大小写，prefix为设置该cookie的前缀（设置前缀也可以通过默认值配置或者直接将'COOKIE名'设置为完整的cookie名称）、设置的cookie过期时间(默认浏览器关闭cookie失效)、以及cookie的作用目录(默认/)、作用域名(默认当前域名)、以及该cookie是否httponly；除prefix外，其余几个索引键与<code>setcookie(string $name [, string $value [, int $expire = 0 [, string $path [, string $domain [, bool $secure = false [, bool $httponly = false ]]]]]])</code>函数对应；仅需留意的是expire索引键的值通过cookie函数仅需指定多少秒后过期即可，而无需再加上<code>time()</code>，比如使用setcookie函数设置一个1个小时后过期的cookie，expire参数为<code>time()+3600</code>，而使用cookie函数，expire仅需要传入<code>3600</code>即可。</p>
      <p>cookie是一个很强大的函数，用法举例：</p>
      <ul class="sample_list">
        <li>设置一个有前缀prefix的cookie，假设该prefix为<code>J_</code>，有多种方法：第一种<code>cookie('user','jjonline@jjonline.cn',array('prefix'=>'J_',expire=>3600,'domain'=>'.jjonline.cn','httponly'=>true))</code>第二种<code>cookie('J_user','jjonline@jjonline.cn',array(expire=>3600,'domain'=>'.jjonline.cn','httponly'=>true))</code>，或者直接在cookie函数体中将<code>$config</code>中的<code>prefix</code>项写死为<code>'J_'</code></li>
        <li>读取上一步设置的cookie，也有多种写法：<code>cookie('J_user')</code>或者<code>cookie('user','',array('prefix'=>'J_'))</code>以及<code>cookie('J.user')</code>；倘若在cookie函数体中将$config中的prefix项写死为'J_'，还可以这样<code>cookie('user')</code></li>
        <li>删除上一步设置的cookie，<code>cookie('J_user',null)</code>或者<code>cookie('J.user',null)</code></li>
        <li>为cookie值设置一个数组，<code>cookie('J_info',array('id'=>1,'vip'=>1))</code>，cookie内部自动将数组使用json_encode编码成字符串并在字符串开头位置加上<code>Array:</code>，采用cookie函数读取该cookie<code>cookie('J_info')</code>将返回数组。</li>
      </ul>
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