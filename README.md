FidyDDns
========

基于Zend Framework实现的一个Dnspod DDNS客户端。
因为只用到了Zend_Http_Client组件，所以你也可以用curl等函数替换掉，来摆脱对ZF的依赖。

使用方法举例：

```php
<?php
$ddns = new My_Dnspod(); //请先修改类文件中的登陆用户名和密码

$ddns->getDomainInfo("xxx.com"));//获取域名基本信息

$ddns->setSubdomainIP("blog",$ddns->my_ip);//给二级域名设置新ip（函数会自动判断域名ip是否发生变化，没变就不提交变更请求）
```
