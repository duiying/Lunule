# Lunule
一款基于MVC模式并集成了Smarty3模板引擎的PHP框架,并基于该框架实现一个简易的带后台登录以及curd操作的管理系统

### 前言
```
市面上已经有成熟的PHP框架,比如Yii,Laravel,ThinkPHP,Yaf,CI...,我们为什么还要设计自己的PHP框架呢?  
这是因为在设计自己框架的过程中,我们会更加深入地了解框架的运行原理,培养自己面向对象的编程思维,同时也会提升自身的编码能力
```
### 环境要求
```
PHP版本>=5.4
```
### 目录结构
```
www WEB部署目录
|--index.php    前台应用模块入口文件
|--admin.php    后台应用模块入口文件
|--README.md    README文件
|--Admin        后台应用模块目录
|--Index        前台应用模块目录(首次运行项目时会自动生成)
|--Common       公共目录
|--Lunule       框架目录
|--Static       静态资源目录
|--Temp         临时目录
```
其中框架目录Lunule的目录结构为:
```
Lunule 框架目录
|--Config         框架核心配置目录
|   |--config.php 框架核心配置文件
|--Data           框架资源目录
|   |--Tpl        框架模板目录
|--Extends        框架扩展类库目录
|   |--Lib        框架扩展类库目录
|   |--Tool       框架工具类库目录
|--Lib            框架库文件目录
|   |--Core       框架核心库文件目录
|   |--Function   框架核心函数目录
|--Lunule.php     框架入口文件
```
### 入口文件
在项目根目录下,有入口文件index.php和admin.php,index.php负责Index模块的入口,admin.php负责Admin模块的入口  
比如index.php的文件内容为:
```
<?php  

// 检测PHP环境
if(version_compare(PHP_VERSION,'5.4.0','<'))  die('require PHP > 5.4.0 !');

// 开启调试模式 建议开发阶段开启 部署阶段注释或者设为FALSE
define('DEBUG', TRUE);

// 定义应用目录
define('APP_NAME', 'Index');

// 引入Lunule入口文件
require './Lunule/Lunule.php';
```
### 自动生成
在第一次访问index.php入口文件的时候,会显示如图所示的默认的欢迎页面,并自动生成了一个默认的应用模块Home  
![welcome](https://raw.githubusercontent.com/duiying/Lunule/master/readmeimg/welcome.png)  
在项目根目录下,已经生成了前台应用模块目录Index,Index的目录结构为:
```
Index 前台应用模块目录
|--Config                        前台应用模块配置目录
|   |--config.php                前台应用模块配置文件
|--Controller                    前台应用模块控制器目录
|   |--IndexController.class.php 前台应用模块默认控制器
|--Tpl                           前台应用模块模板目录
|   |--error.html                前台应用模块错误提示模板文件
|   |--success.html              前台应用模块成功提示模板文件
```
### 命名规范
```
1.类文件都是以.class.php为后缀
2.类名与文件名一致,比如IndexController类的文件命名是IndexController.class.php
3.常量以大写字母和下划线命名
4.数据表和字段采用小写加下划线方式命名
5.配置参数以大写字母和下划线命名
```
### 配置格式
配置文件均采用PHP数组的方式
```
<?php
return [
	// 配置项 => 配置值
];
```
### 配置加载
配置的优先级为: 应用模块的配置 > 公共配置 > 框架核心配置文件
### URL模式
前台应用: http://serverName/index.php?c=控制器&a=方法  
后台应用: http://serverName/admin.php?c=控制器&a=方法
### 框架执行流程
![flow](https://raw.githubusercontent.com/duiying/Lunule/master/readmeimg/flow.png)
### 后台管理系统
基于该框架实现一个简易的带后台登录以及curd操作的管理系统,封装Layer弹层,用户体验良好  
账户root 密码123456  
首先创建名称为lunue的数据库,然后执行如下sql语句  
```
DROP TABLE IF EXISTS `lunule_category`;
CREATE TABLE `lunule_category` (
  `cid` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '栏目ID',
  `cname` varchar(50) NOT NULL DEFAULT '' COMMENT '栏目名称',
  PRIMARY KEY (`cid`)
) ENGINE=MyISAM AUTO_INCREMENT=15 DEFAULT CHARSET=utf8 COMMENT='栏目表';

INSERT INTO `lunule_category` VALUES ('9', 'JAVA笔记');
INSERT INTO `lunule_category` VALUES ('11', '关于作者');

DROP TABLE IF EXISTS `lunule_user`;
CREATE TABLE `lunule_user` (
  `uid` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '用户ID',
  `username` varchar(50) NOT NULL DEFAULT '' COMMENT '用户名',
  `password` varchar(50) NOT NULL DEFAULT '' COMMENT '密码',
  PRIMARY KEY (`uid`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='用户表';

INSERT INTO `lunule_user` VALUES ('1', 'wyx', 'e10adc3949ba59abbe56e057f20f883e');
```
访问地址: http://localhost/Lunule/admin.php  
展示图  
![login](https://raw.githubusercontent.com/duiying/Lunule/master/readmeimg/login.png)  
![admin](https://raw.githubusercontent.com/duiying/Lunule/master/readmeimg/admin.png)  




