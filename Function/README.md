# 实用函数库
## PasswordFunction.php
在php5.3至php5.4版本中**实现php5.5及其以上版本中的内置的Bcrypt函数方法族**

**password_hash()**

**password_get_info()**

**password_verify()**

**password_needs_rehash()**

**详见**：<a href="http://cn2.php.net/manual/zh/ref.password.php" target=_blank>http://cn2.php.net/manual/zh/ref.password.php</a>
## FilterValidFunction.php
检测字符串是否符合以下要求的多个函数方法：

**邮箱地址=>bool is_mail_valid(string $mail)**

**天朝手机号=>bool is_phone_valid(mixed $phone)**

**http或https开头的Url=>bool is_url_valid(string $url)**

**数字ID账户=>bool is_uid_valid(mixed $uid,int $minLength[=4],int $maxLength[=11])**

三个参数：第一个必选参数$uid为需要检测的数字ID，第二个可选参数$minLength指定合法的uid最低位数[默认4位]，第三个可选参数$maxLength指定合法的uid最高位数[默认11位]

**[至少要同时包含数字和字母的]密码格式效验=>bool is_password_valid(string $password,int $minLength[=8],int $maxLength[=16])**

三个参数：第一个必选参数$password为需要检测的密码字符串，第二个可选参数$minLength指定合法的密码字符串最小长度[默认8个字符串]，第三个可选参数$maxLength指定合法的合法的密码字符串最大长度[默认16个字符串]

**天朝身份证号=>mixed is_citizen_id_valid(mixed $citizen_id)**

返回类型：false或者array，并不全是返回boolean类型，但返回的array是一个关联数组[可以从中取值]，布尔判断为true；该函数兼容15位老身份证号和18位新身份证号[若传入15位合法的身份证号将返回转换过的18位身份证号]

## UsefullFunction.php
封装的一些常用的方法。

**时间转换为友好显示法，例如“5分钟前”=>string time_ago(int $UnixTimeStamp)**

**文件体积字节（Byte）转换为友好表示法，例如“5MB”=>string format_bytes($size, $delimiter = '')**