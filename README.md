# php-libaray
## 简介
php-Libaray是由本人在日常code中常用的一些php开源库汇总而来，其中进行了一些修改。
项目有两个文件夹
**Libaray文件夹是一些常用的类**
**Function文件夹是一些很实用的函数方法或为某些php低版本拓展在新版本中已实现的函数方法,例如:password_hash()**

php-Libaray中所有库均有所修改，去除了兼容php5.3一下的判断代码，以提升调用和执行效率。

**php-Libaray仅支持PHP5.3.0及其以上的php环境。**

库中Libaray文件夹中是各种类库核心，每一种类库一个文件夹保存。

``demo.php``是``Libaray``文件夹下各个类库的调用举例，具备自动加载机制，在php环境下，浏览器直接访问demo.php即可看到详细的使用介绍。

``index.php``是``demo.php``的一个快速入口，可以看到该文件中仅一句代码：``include __DIR__.'/demo.php';``
## Libaray文件夹下的类库命名规则
既然仅支持php5.3及其以上的php环境，php-Libaray所有代码均添加了命名空间的支持
### 命名空间规则与目录规则对应
Libaray文件夹下的所有类库均归属于Libaray命名空间下

Libaray文件夹下每一个开源库一个文件夹，又对应于一个子命名空间。
### 类名与类文件名规则对应
####类名规则：####
类名首字母大写，多个具有语义的单词类名，各单词首字母也大写
	ps：虽然PHP类名并不区分大小写，但作为一种规则可以减少很多不必要的麻烦
####类文件规则：####
类文件名为``类名.class.php``
		
例如Hashids类，命名空间为:``Libaray\Hashids\``;文件名为``Hashids.class.php``，文件路径为：``./Libaray/Hashids/Hashids.class.php``
	
## 版权申明
由于php-Libaray收集至开源库，各个开源库遵循的开源协议各异，鄙人不保留任何php-Libaray中各个开源库代码的权利，代码所有权归各开源库原作者所有。

换种说法：php-Libaray只是一个各个好用php开源库汇总集合，鄙人额外添加了一些注释或适配修改；添加了一些中文注释或中文调用方法的说明。

## 在线Demo演示
<a href="http://blog.jjonline.cn/project/php-libaray/">http://blog.jjonline.cn/project/php-libaray/</a>