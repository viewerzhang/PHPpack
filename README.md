![PHPpack](https://www.zyt8.cn/a.png "PHPpack")
# PHPpack
PHPpack是一款用于构建大型项目的工具，用于将多个文件打包成一个单独的PHP文件。同时该工具内置加密功能，可以是您的PHP代码避免不必要的泄露。

Phppack is a tool for building large projects, which is used to package multiple files into a single PHP file. At the same time, the tool has built-in encryption function, which can prevent unnecessary disclosure of your PHP code.
# 如何开始？
How to start?

PHPpack是一个轻量级的工具所以使用起来也非常简单

Phppack is a lightweight tool, so it's very easy to use

然后键入 打开terminal or CMD
git pull 后 cd 到PHPpack 目录

`php phppack.php -m 入口文件名称 -o 输出文件路径 --v开启变量混淆`
## 演示
php的源文件 index.php
```php
<?php
require './hhh.php' ;
echo 'hello PHPpack';
```
```shell
$ php phperpack.php -m ./index.php
加载：/Applications/MAMP/htdocs/PHPerpack/index.php
加载：/Applications/MAMP/htdocs/PHPerpack/hhh.php
👌PHPpack为您构建成功！
🚗构建后文件位置：./output.php
⌚️总耗时：0.074505805969238s
```