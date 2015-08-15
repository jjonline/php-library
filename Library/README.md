# php-libaray Libaray下文件夹中结构和内容介绍
## PasswordHash文件夹
PasswordHash类：：url中不便于直接显示成数字的id加密成唯一性的字符串（hash）；php脚本接收到该hash后又可以很方便的还原成数字id。也可以将多个数字型的参数一次性加密后作为一个GET变量附加在url中。

两个核心方法**HashPassword($password)**、**CheckPassword($password,$stored_hash)**
## Hashids文件夹
Hashids类：：加密密码字符串成为不可逆的hash字符串，此加密方式一个明文密码对应无数个hash串；通过保存的hash串和密码明文进行对比，又可以效验明文密码的正确性。[该加密类被WordPress、emlog等许多开源程序使用]

四个核心方法**encode(mixed $int,[,int $int,int $int])**、**decode(string $hashString)**、**encode_hex(hex_string $hex_string)**、**decode_hex(string $hashString)**；注意encode_hex参数传入的是16进制字符串[不要附带0x]，且该方法仅能接收一个参数（而encode却可以接收多个int型参数或者一个value全部为int型的索引数组），decode_hex返回的结果也是不带0x的

decode方法进行了适当改进，当被encode的仅为一个int型参数时，decode方法不再返回仅一个值的数组(array)，而是直接返回int的该值(int)